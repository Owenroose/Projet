<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    /**
     * Affiche la liste des commandes avec filtres et recherche
     */
    public function index(Request $request)
    {
        // Récupération des order_group uniques avec les informations de la première commande du groupe
        $orderGroupsQuery = DB::table('orders')
            ->select(
                'order_group',
                DB::raw('MIN(id) as first_order_id'),
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('MAX(customer_email) as customer_email'),
                DB::raw('MAX(customer_phone) as customer_phone'),
                DB::raw('MAX(customer_city) as customer_city'),
                DB::raw('MAX(customer_address) as customer_address'),
                DB::raw('MAX(total_amount) as total_amount'),
                DB::raw('MAX(status) as status'),
                DB::raw('MAX(payment_status) as payment_status'),
                DB::raw('SUM(quantity) as total_items'),
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('MAX(updated_at) as updated_at')
            )
            ->groupBy('order_group')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('status')) {
            $orderGroupsQuery->having('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $orderGroupsQuery->having('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $orderGroupsQuery->havingRaw('DATE(created_at) >= ?', [$request->date_from]);
        }

        if ($request->filled('date_to')) {
            $orderGroupsQuery->havingRaw('DATE(created_at) <= ?', [$request->date_to]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $orderGroupsQuery->havingRaw('(customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)',
                ["%{$search}%", "%{$search}%", "%{$search}%"]);
        }

        // Pagination
        $perPage = 15;
        $page = $request->get('page', 1);
        $total = $orderGroupsQuery->get()->count();
        $orders = $orderGroupsQuery->offset(($page - 1) * $perPage)->limit($perPage)->get();

        // Créer l'objet de pagination
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $orders,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Pour chaque groupe de commande, récupérer les détails des produits
        $orders->getCollection()->transform(function ($orderGroup) {
            $orderGroup->items = Order::where('order_group', $orderGroup->order_group)
                ->with('product')
                ->get();

            // Calculer le montant total des produits (sans frais de livraison)
            $orderGroup->products_total = $orderGroup->items->sum('subtotal');
            $orderGroup->shipping_fee = $orderGroup->items->first()->shipping_fee ?? 0;

            return $orderGroup;
        });

        // Statistiques
        $stats = $this->getOrderStats();

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Affiche le tableau de bord des commandes
     */
    public function dashboard()
    {
        // Statistiques
        $stats = $this->getOrderStats();

        // Commandes récentes (groupées par order_group)
        $recentOrderGroups = DB::table('orders')
            ->select(
                'order_group',
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('MAX(customer_email) as customer_email'),
                DB::raw('MAX(total_amount) as total_amount'),
                DB::raw('MAX(status) as status'),
                DB::raw('MAX(payment_status) as payment_status'),
                DB::raw('MIN(created_at) as created_at')
            )
            ->groupBy('order_group')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = collect($recentOrderGroups)->map(function($orderGroup) {
            $orderGroup->items = Order::where('order_group', $orderGroup->order_group)
                ->with('product')
                ->get();
            return $orderGroup;
        });

        // Données du graphique (Commandes par mois) - basé sur order_group uniques
        $ordersByMonth = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, COUNT(DISTINCT order_group) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartData = [
            'labels' => [],
            'data' => []
        ];

        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        // Initialiser tous les mois avec 0
        for ($i = 1; $i <= 12; $i++) {
            $chartData['labels'][] = $months[$i - 1];
            $chartData['data'][] = 0;
        }

        // Remplir avec les données réelles
        foreach ($ordersByMonth as $order) {
            $chartData['data'][$order->month - 1] = $order->count;
        }

        // Données du graphique circulaire - basé sur order_group uniques
        $statusCounts = DB::table('orders')
            ->select('status', DB::raw('COUNT(DISTINCT order_group) as count'))
            ->groupBy('status')
            ->get();

        $statusLabels = [];
        $statusData = [];
        $statusColors = [];
        $colorMap = [
            'pending' => '#ffc107',
            'processing' => '#17a2b8',
            'shipped' => '#28a745',
            'delivered' => '#007bff',
            'cancelled' => '#dc3545',
        ];

        foreach ($statusCounts as $status) {
            $statusLabels[] = $this->getStatusLabel($status->status);
            $statusData[] = $status->count;
            $statusColors[] = $colorMap[$status->status] ?? '#6c757d';
        }

        $statusPieChartData = [
            'labels' => $statusLabels,
            'data' => $statusData,
            'colors' => $statusColors
        ];

        return view('admin.orders.dashboard', compact('stats', 'recentOrders', 'chartData', 'statusPieChartData'));
    }

    /**
     * Affiche les détails d'un groupe de commande
     */
    public function show($orderGroup)
    {
        // Votre code existant pour récupérer les détails de la commande
        $orders = Order::where('order_group', $orderGroup)
                       ->with('product')
                       ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        $orderInfo = $orders->first();
        $orderInfo->items = $orders;
        $orderInfo->products_total = $orders->sum('subtotal');
        $orderInfo->shipping_fee = $orders->first()->shipping_fee ?? 0;
        $orderInfo->total_with_shipping = $orderInfo->products_total + $orderInfo->shipping_fee;

        // Charger l'historique de la commande
        $history = OrderHistory::where('order_group', $orderGroup)
                               ->with('user')
                               ->orderBy('created_at', 'desc')
                               ->get();

        // Charger l'affectation de la livraison
        $delivery = Delivery::where('order_group', $orderGroup)->with('driver')->first();

        // Récupérer la liste des utilisateurs avec le rôle 'livreur'
        $drivers = User::where('role', 'driver')->get();

        return view('admin.orders.show', compact('orderInfo', 'history', 'delivery', 'drivers'));
    }

    /**
     * Affecte un livreur à une commande.
     */
    public function assignDriver(Request $request, $orderGroup)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $order = Order::where('order_group', $orderGroup)->firstOrFail();

        // Vérifiez si une livraison existe déjà pour cette commande
        $delivery = Delivery::firstOrNew(['order_group' => $orderGroup]);

        // Enregistrer l'affectation
        $delivery->driver_id = $request->driver_id;
        $delivery->status = 'assigned';
        $delivery->save();

        // Enregistrer l'action dans l'historique
        OrderHistory::create([
            'order_group' => $orderGroup,
            'status_before' => $order->status,
            'status_after' => $order->status,
            'action' => 'delivery_assignment',
            'user_id' => auth()->id(), // ID de l'admin qui affecte
            'notes' => 'Affectation au livreur : ' . $delivery->driver->name,
        ]);

        return redirect()->back()->with('success', 'Livreur affecté avec succès.');
    }

    /**
     * Met à jour le statut d'une ou plusieurs commandes.
     */
    public function updateStatus(Request $request, $orderGroup)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);

        $order = Order::where('order_group', $orderGroup)->firstOrFail();
        $oldStatus = $order->status;
        $newStatus = $request->input('status');

        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', 'Le statut n\'a pas changé.');
        }

        // Vérifiez si le changement de statut est valide
        if (!in_array($newStatus, ['processing', 'shipped', 'delivered', 'cancelled'])) {
             return redirect()->back()->with('error', 'Changement de statut invalide.');
        }

        DB::beginTransaction();
        try {
            // Mettre à jour le statut pour toutes les commandes du groupe
            Order::where('order_group', $orderGroup)->update([
                'status' => $newStatus,
                'updated_at' => now(),
                // Mettre à jour les timestamps si nécessaire
                'shipped_at' => ($newStatus === 'shipped' && is_null($order->shipped_at)) ? now() : $order->shipped_at,
                'delivered_at' => ($newStatus === 'delivered' && is_null($order->delivered_at)) ? now() : $order->delivered_at,
            ]);

            // Enregistrer l'historique de l'action
            OrderHistory::create([
                'order_group' => $orderGroup,
                'status_before' => $oldStatus,
                'status_after' => $newStatus,
                'action' => 'status_update',
                'user_id' => auth()->id(),
                'notes' => $request->input('notes'),
            ]);

            // Décrémenter le stock si le statut passe à 'processing'
            if ($oldStatus === 'pending' && $newStatus === 'processing') {
                $orders = Order::where('order_group', $orderGroup)->get();
                foreach ($orders as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        $product->stock_quantity -= $orderItem->quantity;
                        $product->save();
                    }
                }
            }

            // Notifier le client par e-mail
            if ($order->customer_email && ($newStatus === 'processing' || $newStatus === 'shipped')) {
                // Assurez-vous d'avoir créé la Mailable class
                // Mail::to($order->customer_email)->send(new OrderStatusUpdate($orderGroup, $newStatus));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à jour du statut de la commande: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Supprime un groupe de commande et ses articles.
     */
    public function destroy($orderGroup)
    {
        try {
            DB::beginTransaction();

            $deleted = Order::where('order_group', $orderGroup)->delete();

            DB::commit();

            Log::info("Commande supprimée", [
                'order_group' => $orderGroup,
                'deleted_count' => $deleted
            ]);

            return response()->json([
                'message' => 'Commande supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la commande: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la commande.'
            ], 500);
        }
    }

    /**
     * Exporte les commandes en fichier CSV.
     */
    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        // Récupérer les groupes de commandes dans la période spécifiée
        $orderGroups = DB::table('orders')
            ->select(
                'order_group',
                DB::raw('MAX(customer_name) as customer_name'),
                DB::raw('MAX(customer_email) as customer_email'),
                DB::raw('MAX(customer_phone) as customer_phone'),
                DB::raw('MAX(customer_city) as customer_city'),
                DB::raw('MAX(total_amount) as total_amount'),
                DB::raw('MAX(status) as status'),
                DB::raw('MAX(payment_status) as payment_status'),
                DB::raw('MIN(created_at) as created_at')
            )
            ->whereBetween('created_at', [$request->date_from . ' 00:00:00', $request->date_to . ' 23:59:59'])
            ->groupBy('order_group')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->exportToExcel($orderGroups, $request->date_from, $request->date_to);
    }

    /**
     * Génère les statistiques des commandes
     */
    private function getOrderStats()
    {
        $totalOrderGroups = DB::table('orders')->distinct('order_group')->count();
        $totalSales = DB::table('orders')->sum('total_amount');
        $pendingOrders = DB::table('orders')->where('status', 'pending')->distinct('order_group')->count();
        $priorityOrders = DB::table('orders')->whereIn('status', ['pending', 'processing'])->distinct('order_group')->count();

        return [
            'totalOrders' => $totalOrderGroups,
            'totalSales' => $totalSales,
            'pendingOrders' => $pendingOrders,
            'priorityOrders' => $priorityOrders
        ];
    }

    /**
     * Retourne le libellé français du statut
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Exporte les données vers un fichier CSV
     */
    private function exportToExcel($orderGroups, $dateFrom, $dateTo)
    {
        $filename = "commandes_{$dateFrom}_au_{$dateTo}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($orderGroups) {
            $file = fopen('php://output', 'w');

            // BOM pour UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes CSV
            fputcsv($file, [
                'ID de groupe',
                'Client',
                'Email',
                'Téléphone',
                'Ville',
                'Produits',
                'Quantité totale',
                'Prix Total',
                'Statut commande',
                'Statut paiement',
                'Date de commande'
            ], ';');

            // Données
            foreach ($orderGroups as $orderGroup) {
                // Récupérer les détails des produits pour ce groupe
                $orderItems = Order::where('order_group', $orderGroup->order_group)
                    ->with('product')
                    ->get();

                $productNames = $orderItems->pluck('product.name')->implode(', ');
                $totalQuantity = $orderItems->sum('quantity');

                fputcsv($file, [
                    $orderGroup->order_group,
                    $orderGroup->customer_name,
                    $orderGroup->customer_email ?? '',
                    $orderGroup->customer_phone,
                    $orderGroup->customer_city,
                    $productNames,
                    $totalQuantity,
                    number_format($orderGroup->total_amount, 0, ',', ' ') . ' FCFA',
                    $this->getStatusLabel($orderGroup->status),
                    ucfirst($orderGroup->payment_status ?? 'pending'),
                    Carbon::parse($orderGroup->created_at)->format('d/m/Y H:i')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
