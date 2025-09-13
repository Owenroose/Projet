<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use FedaPay\Transaction;

class OrderController extends Controller
{
    /**
     * Liste toutes les commandes.
     */
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Affiche le formulaire de commande.
     */
    public function create($slug = null)
    {
        return view('orders.create');
    }

    /**
     * Enregistre une commande sans paiement immédiat.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'shipping_fee' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'order_group' => Str::uuid()->toString(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'shipping_fee' => $request->shipping_fee,
            'total_amount' => $request->quantity * 1000 + $request->shipping_fee,
            'status' => 'pending',
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'customer_address' => $request->address,
            'customer_city' => $request->city,
        ]);

        return redirect()->route('orders.show', $order->id)
                         ->with('success', 'Commande enregistrée avec succès.');
    }

    /**
     * Traite la commande et initie le paiement via FedaPay.
     */
    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'shipping_fee' => 'required|numeric|min:0',
        ]);

        try {
            $orderGroup = Str::uuid()->toString();
            $finalAmount = $request->quantity * 1000 + $request->shipping_fee;

            $transaction = Transaction::create([
                "description" => "Paiement commande",
                "amount" => $finalAmount,
                "currency" => ["iso" => "XOF"],
                "callback_url" => route('orders.payment.callback'),
                "success_url" => route('orders.success'),
                "failure_url" => route('orders.failure'),
                "customer" => [
                    "email" => $request->email,
                    "lastname" => $request->name,
                    "phone_number" => [
                        "number" => $request->phone,
                        "country" => "BJ"
                    ]
                ],
                "custom_metadata" => [
                    "order_group" => $orderGroup
                ]
            ]);

            $token = $transaction->generateToken();

            Order::create([
                'order_group' => $orderGroup,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'shipping_fee' => $request->shipping_fee,
                'total_amount' => $finalAmount,
                'fedapay_transaction_id' => $transaction->id,
                'fedapay_token' => $token->id,
                'status' => 'pending',
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'customer_address' => $request->address,
                'customer_city' => $request->city,
            ]);

            return redirect($token->url);
        } catch (\Exception $e) {
            Log::error('Erreur FedaPay process: ' . $e->getMessage());
            return back()->with('error', 'Erreur de paiement: ' . $e->getMessage());
        }
    }

    /**
     * Affiche le détail d'une commande.
     */
    public function show($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('orders.show', compact('order'));
    }

    /**
     * Met à jour le statut d'une commande.
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled'
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('orders.show', $orderId)
                         ->with('success', 'Statut mis à jour avec succès.');
    }

    /**
     * Supprime une commande.
     */
    public function destroy($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->delete();

        return redirect()->route('orders.index')
                         ->with('success', 'Commande supprimée avec succès.');
    }

    /**
     * Callback de FedaPay après tentative de paiement.
     */
    public function handleCallback(Request $request)
    {
        $transactionId = $request->input('id');

        try {
            $transaction = Transaction::retrieve($transactionId);

            if (isset($transaction->custom_metadata['order_group'])) {
                $orderGroup = $transaction->custom_metadata['order_group'];

                $orders = Order::where('order_group', $orderGroup)->get();
                foreach ($orders as $order) {
                    $order->payment_status = $transaction->status;
                    if ($transaction->status === 'approved') {
                        $order->paid_at = now();
                        $order->status = 'processing';
                    }
                    $order->save();
                }
            }

            if ($transaction->status === 'approved') {
                return redirect()->route('orders.success');
            } else {
                return redirect()->route('orders.failure');
            }
        } catch (\Exception $e) {
            Log::error('Erreur FedaPay callback: ' . $e->getMessage());
            return redirect()->route('orders.failure')->with('error', $e->getMessage());
        }
    }
}
