<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminOrderController extends Controller
{
    /**
     * Affiche la liste des commandes avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = Order::with(['product'])->latest();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'today' => Order::whereDate('created_at', today())->count(),
            'this_month' => Order::whereMonth('created_at', now()->month)->count(),
            'total_revenue' => Order::whereIn('status', ['delivered', 'shipped', 'processing'])->sum('total_price')
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        $order->load(['product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'status_updated_at' => now()
        ]);

        // Log de l'action
        Log::info("Commande #{$order->id} - Statut changé de '{$oldStatus}' vers '{$request->status}' par " . auth()->user()->name);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour avec succès',
            'new_status' => $request->status
        ]);
    }

    /**
     * Ajoute des notes à une commande
     */
    public function addNote(Request $request, Order $order)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // Récupère les notes existantes ou initialise un tableau vide
        $notes = json_decode($order->notes, true) ?? [];
        $notes[] = [
            'content' => $request->content,
            'author' => auth()->user()->name,
            'created_at' => now()->toDateTimeString()
        ];

        $order->update(['notes' => json_encode($notes)]);

        return response()->json([
            'success' => true,
            'message' => 'Note ajoutée avec succès'
        ]);
    }

    /**
     * Marque une commande comme prioritaire
     */
    public function togglePriority(Order $order)
    {
        $order->update([
            'is_priority' => !$order->is_priority
        ]);

        return response()->json([
            'success' => true,
            'is_priority' => $order->is_priority,
            'message' => $order->is_priority ? 'Commande marquée comme prioritaire' : 'Priorité supprimée'
        ]);
    }

    /**
     * Supprime une commande
     */
    public function destroy(Order $order)
    {
        $orderNumber = $order->id;
        $order->delete();

        return redirect()->route('admin.orders.index')
                        ->with('success', "Commande #{$orderNumber} supprimée avec succès");
    }

    /**
     * Génère un rapport des commandes
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:excel,pdf'
        ]);

        $orders = Order::with('product')
                      ->whereBetween('created_at', [
                          $request->date_from . ' 00:00:00',
                          $request->date_to . ' 23:59:59'
                      ])
                      ->get();

        if ($request->format === 'excel') {
            return $this->exportToExcel($orders, $request->date_from, $request->date_to);
        } else {
            return $this->exportToPDF($orders, $request->date_from, $request->date_to);
        }
    }

    /**
     * Recherche automatique pour l'autocomplete
     */
    public function searchAutocomplete(Request $request)
    {
        $term = $request->get('term', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $orders = Order::with('product')
                      ->where(function($query) use ($term) {
                          $query->where('name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%")
                                ->orWhere('phone', 'like', "%{$term}%")
                                ->orWhereHas('product', function($q) use ($term) {
                                    $q->where('name', 'like', "%{$term}%");
                                });
                      })
                      ->limit(10)
                      ->get();

        $suggestions = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'label' => "#{$order->id} - {$order->name} - {$order->product->name}",
                'value' => $order->name,
                'url' => route('admin.orders.show', $order)
            ];
        });

        return response()->json($suggestions);
    }

    /**
     * Exporte les données vers Excel (exemple de structure)
     */


    /**
     * Dashboard des commandes avec statistiques avancées
     */
    public function dashboard()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            // Commandes par période
            'today' => [
                'count' => Order::whereDate('created_at', $today)->count(),
                'revenue' => Order::whereDate('created_at', $today)->sum('total_price')
            ],
            'week' => [
                'count' => Order::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => Order::where('created_at', '>=', $thisWeek)->sum('total_price')
            ],
            'month' => [
                'count' => Order::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => Order::where('created_at', '>=', $thisMonth)->sum('total_price')
            ],

            // Commandes par statut
            'by_status' => [
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ],

            // Commandes prioritaires
            'priority' => Order::where('is_priority', true)->count(),

            // Revenus total
            'total_revenue' => Order::whereIn('status', ['delivered', 'shipped', 'processing'])->sum('total_price')
        ];

        // Commandes récentes
        $recentOrders = Order::with('product')->latest()->limit(5)->get();

        // Graphique des commandes par jour (7 derniers jours)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d/m'),
                'orders' => Order::whereDate('created_at', $date)->count(),
                'revenue' => Order::whereDate('created_at', $date)->sum('total_price')
            ];
        }

        return view('admin.orders.dashboard', compact('stats', 'recentOrders', 'chartData'));
    }

        private function exportToExcel($orders, $dateFrom, $dateTo)
    {
        // Ici vous pouvez utiliser Laravel Excel ou une autre bibliothèque
        // Pour l'exemple, on retourne un CSV simple

        $filename = "commandes_{$dateFrom}_au_{$dateTo}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Client', 'Email', 'Téléphone', 'Produit',
                'Quantité', 'Prix Total', 'Statut', 'Date de commande'
            ]);

            // Données
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->name,
                    $order->email,
                    $order->phone,
                    $order->product->name,
                    $order->quantity,
                    number_format($order->total_price, 0, ',', ' ') . ' FCFA',
                    ucfirst($order->status),
                    $order->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
