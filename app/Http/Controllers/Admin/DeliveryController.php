<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    /**
     * Affiche la liste des livraisons pour le livreur authentifié.
     */
    public function index()
    {
        $driverId = auth()->id();
        $deliveries = Delivery::where('driver_id', $driverId)
                              ->with('order')
                              ->get();

        return view('admin.delivery.index', compact('deliveries'));
    }

    /**
     * Affiche les détails d'une livraison.
     */
    public function show($orderGroup)
    {
        $delivery = Delivery::where('order_group', $orderGroup)
                            ->where('driver_id', auth()->id())
                            ->with('order.products')
                            ->firstOrFail();

        return view('admin.delivery.show', compact('delivery'));
    }

    /**
     * Valide la livraison d'une commande.
     */
    public function validateDelivery($orderGroup)
    {
        $delivery = Delivery::where('order_group', $orderGroup)
                            ->where('driver_id', auth()->id())
                            ->firstOrFail();

        if ($delivery->status !== 'delivered') {
            DB::beginTransaction();
            try {
                // Mettre à jour le statut de la livraison
                $delivery->status = 'delivered';
                $delivery->save();

                // Mettre à jour le statut de la commande principale
                Order::where('order_group', $orderGroup)->update([
                    'status' => 'delivered',
                    'delivered_at' => now()
                ]);

                // Enregistrer l'action dans l'historique
                OrderHistory::create([
                    'order_group' => $orderGroup,
                    'status_before' => 'shipped',
                    'status_after' => 'delivered',
                    'action' => 'delivery_validation',
                    'user_id' => auth()->id(),
                    'notes' => 'Livraison validée par le livreur.',
                ]);

                DB::commit();
                return redirect()->route('admin.delivery.index')->with('success', 'La livraison a été validée.');
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Erreur lors de la validation de la livraison: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Une erreur est survenue.');
            }
        }

        return redirect()->back()->with('info', 'La livraison est déjà validée.');
    }
}
