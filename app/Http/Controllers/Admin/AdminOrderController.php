<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $orders = Order::where('order_group', $orderGroup)->with('product')->get();

        if ($orders->isEmpty()) {
            abort(404, 'Commande non trouvée.');
        }

        // Informations générales de la commande
        $orderInfo = $orders->first();

        // Calculer les totaux
        $productsTotal = $orders->sum('subtotal');
        $shippingFee = $orderInfo->shipping_fee;
        $totalAmount = $orderInfo->total_amount;

        return view('admin.orders.show', compact('orders', 'orderInfo', 'productsTotal', 'shippingFee', 'totalAmount'));
    }

    /**
     * Met à jour le statut d'une ou plusieurs commandes.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_groups' => 'required|array',
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $orderGroups = $request->input('order_groups');
        $newStatus = $request->input('status');

        try {
            DB::beginTransaction();

            $updated = Order::whereIn('order_group', $orderGroups)->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

            DB::commit();

            Log::info("Statut des commandes mis à jour", [
                'order_groups' => $orderGroups,
                'new_status' => $newStatus,
                'updated_count' => $updated
            ]);

            return response()->json([
                'message' => "Statut de {$updated} commande(s) mis à jour avec succès."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour des statuts de commande: ' . $e->getMessage());
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour des statuts.'
            ], 500);
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
