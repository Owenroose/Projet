<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class OrderController extends Controller
{
    public function __construct()
    {
        // Configuration FedaPay (ajoutez vos clés dans le .env)
        FedaPay::setApiKey(config('fedapay.secret_key'));
        FedaPay::setEnvironment(config('fedapay.environment', 'sandbox'));
    }

    /**
     * Affiche le formulaire de commande pour un produit spécifique ou pour le panier.
     */
    public function create(Request $request, $slug = null)
    {
        $cartItems = [];
        $totalAmount = 0;

        if ($slug) {
            $product = Product::where('slug', $slug)->firstOrFail();
            $cartItems[] = [
                'product' => $product,
                'quantity' => 1,
                'subtotal' => $product->price,
            ];
            $totalAmount = $product->price;
        } else {
            $cart = Session::get('cart', []);

            if (!is_array($cart) || empty($cart)) {
                return redirect()->route('cart.show')->with('error', 'Votre panier est vide.');
            }

            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                if ($product) {
                    if (!$product->in_stock || ($product->stock_quantity && $product->stock_quantity < $details['quantity'])) {
                        // Gérer l'indisponibilité en stock
                        continue;
                    }
                    $quantity = $details['quantity'] ?? 1;
                    $subtotal = $product->price * $quantity;
                    $cartItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                    $totalAmount += $subtotal;
                }
            }
            if (empty($cartItems)) {
                return redirect()->route('cart.show')->with('error', 'Aucun produit dans le panier n\'est disponible en stock.');
            }
        }
        return view('orders.create', compact('cartItems', 'totalAmount'));
    }

    /**
     * Traite la commande, crée la transaction FedaPay et retourne l'URL de redirection.
     */
  /**
 * Traite la commande, crée la transaction FedaPay et retourne l'URL de redirection.
 */
public function store(Request $request)
{
    Log::info('Début de store()', [
        'request_all' => $request->all(),
    ]);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'product_id' => 'nullable|integer|exists:products,id',
        'quantities' => 'nullable|array',
        'quantities.*' => 'integer|min:1|max:99'
    ]);

    DB::beginTransaction();
    try {
        $orderGroup = Str::uuid()->toString();
        $orderItems = [];
        $totalProductAmount = 0;

        if ($request->has('product_id') && $request->product_id) {
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantities[$product->id] ?? 1;
            $orderItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity
            ];
            $totalProductAmount = $product->price * $quantity;
        } else {
            $cart = Session::get('cart', []);
            $quantities = $request->quantities ?? [];

            foreach ($cart as $productId => $cartItem) {
                $product = Product::find($productId);
                if ($product) {
                    $quantity = $quantities[$productId] ?? $cartItem['quantity'] ?? 1;
                    $orderItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity
                    ];
                    $totalProductAmount += $product->price * $quantity;
                }
            }
        }

        if (empty($orderItems)) {
            throw new \Exception('Aucun produit n\'est disponible en stock pour la commande.');
        }

        // Calcul des frais de livraison
        $cityPrices = [
            'Cotonou' => 1500, 'Calavi' => 1500, 'Porto-Novo' => 3000,
            'Ouidah' => 3000, 'Centre/Nord' => 4000
        ];
        $baseShippingFee = $cityPrices[$validated['city']] ?? 0;
        $shippingFee = $this->calculateShippingFee($baseShippingFee, $totalProductAmount);
        $finalAmount = $totalProductAmount + $shippingFee;

        Log::info('Création transaction FedaPay', [
            'order_group' => $orderGroup,
            'amount' => $finalAmount,
            'shipping_fee' => $shippingFee,
            'total_product_amount' => $totalProductAmount
        ]);

        // CORRECTION ICI : Utiliser des URLs absolues au lieu des routes nommées
        $transaction = Transaction::create([
            "description" => "Commande Nova Tech Bénin - " . count($orderItems) . " produit(s)",
            "amount" => $finalAmount,
            "currency" => ["iso" => "XOF"],
            "callback_url" => url('/orders/payment/callback'),
            "success_url" => url('/orders/success/' . $orderGroup),
            "failure_url" => url('/orders/failure'),
            "customer" => [
                "email" => $validated['email'] ?: $validated['name'] . '@novatech.bj',
                "lastname" => $validated['name'],
                "phone_number" => [
                    "number" => $validated['phone'],
                    "country" => "BJ"
                ]
            ],
            "custom_metadata" => [
                "order_group" => $orderGroup,
                "customer_city" => $validated['city'],
                "total_products" => count($orderItems)
            ]
        ]);

        $token = $transaction->generateToken();

        Log::info('Transaction FedaPay créée', [
            'transaction_id' => $transaction->id,
            'token_id' => $token->id,
            'redirect_url' => $token->url
        ]);

        foreach ($orderItems as $item) {
            Order::create([
                'order_group' => $orderGroup,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['product']->price,
                'subtotal' => $item['subtotal'],
                'shipping_fee' => count($orderItems) === 1 ? $shippingFee : ($shippingFee / count($orderItems)),
                'total_amount' => $finalAmount,
                'fedapay_transaction_id' => $transaction->id,
                'fedapay_token' => $token->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'customer_address' => $validated['address'],
                'customer_city' => $validated['city'],
            ]);
        }

        DB::commit();

        Log::info('Commande créée avec succès', [
            'order_group' => $orderGroup,
            'transaction_id' => $transaction->id,
            'amount' => $finalAmount,
            'redirect_url' => $token->url
        ]);

        return response()->json([
            'success' => true,
            'redirectUrl' => $token->url,
            'message' => 'Commande créée avec succès'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Erreur lors de la création de la commande', [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'request_data' => $request->only(['name', 'phone', 'city', 'product_id']),
            'quantities' => $request->quantities ?? [],
            'trace' => $e->getTraceAsString()
        ]);

        // Messages d'erreur plus spécifiques selon le type d'erreur
        if (strpos($e->getMessage(), 'FedaPay') !== false) {
            $errorMessage = 'Erreur de connexion au service de paiement. Veuillez réessayer dans quelques instants.';
        } elseif (strpos($e->getMessage(), 'Missing required parameter') !== false) {
            $errorMessage = 'Erreur de configuration des routes. Veuillez contacter le support.';
        } elseif (strpos($e->getMessage(), 'Ville') !== false) {
            $errorMessage = 'La ville sélectionnée n\'est pas prise en charge pour la livraison.';
        } elseif (strpos($e->getMessage(), 'produit') !== false) {
            $errorMessage = 'Un ou plusieurs produits ne sont plus disponibles.';
        } else {
            $errorMessage = 'Une erreur est survenue lors du traitement de votre commande. Veuillez réessayer.';
        }

        return response()->json([
            'success' => false,
            'error' => $errorMessage
        ], 500);
    }
}

    /**
     * Calcule les frais de livraison selon le montant de la commande
     */
    private function calculateShippingFee($baseFee, $orderTotal)
    {
        if ($orderTotal >= 200000) { // 200 000 CFA et plus
            return 0; // Livraison gratuite
        } elseif ($orderTotal >= 50000) { // 50 000 CFA et plus
            return intval($baseFee / 2); // -50% sur les frais
        } elseif ($orderTotal >= 2000) { // 2 000 CFA et plus (mais moins de 50 000)
            return 0; // Livraison offerte
        } else {
            return $baseFee; // Prix normal
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
     * Met à jour le statut d'une commande (admin).
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($orderId);
        Order::where('order_group', $order->order_group)->update([
            'status' => $request->status,
            'status_updated_at' => now()
        ]);

        Log::info("Statut commande mis à jour: groupe {$order->order_group} -> {$request->status}");
        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès.');
    }

    /**
     * Supprime une commande (admin).
     */
    public function destroy($orderId)
    {
        $order = Order::findOrFail($orderId);
        $orderGroup = $order->order_group;

        Order::where('order_group', $orderGroup)->delete();
        Log::info("Commande(s) supprimée(s): groupe {$orderGroup}");
        return redirect()->back()->with('success', 'Commande(s) supprimée(s) avec succès.');
    }

    /**
     * Gère le callback de FedaPay après le paiement.
     */
    public function handleCallback(Request $request)
    {
        $transactionId = $request->input('id');

        if (!$transactionId) {
            Log::error('Callback FedaPay sans ID de transaction');
            return redirect()->route('orders.failure')->with('error', 'Transaction invalide');
        }

        try {
            $transaction = Transaction::retrieve($transactionId);

            Log::info('Callback FedaPay reçu', [
                'transaction_id' => $transactionId,
                'status' => $transaction->status,
                'amount' => $transaction->amount ?? 'N/A'
            ]);

            $orderGroup = $transaction->custom_metadata['order_group'] ?? null;
            if (!$orderGroup) {
                Log::error('Ordre group manquant dans les métadonnées', ['transaction_id' => $transactionId, 'metadata' => $transaction->custom_metadata ?? []]);
                return redirect()->route('orders.failure')->with('error', 'Commande introuvable');
            }

            $orders = Order::where('order_group', $orderGroup)->get();
            if ($orders->isEmpty()) {
                Log::error('Aucune commande trouvée pour le groupe', ['order_group' => $orderGroup, 'transaction_id' => $transactionId]);
                return redirect()->route('orders.failure')->with('error', 'Commande introuvable');
            }

            // Mettre à jour le statut de la commande en base de données
            foreach ($orders as $order) {
                $order->payment_status = $transaction->status;
                if ($transaction->status === 'approved') {
                    $order->paid_at = now();
                    $order->status = 'processing';
                } elseif ($transaction->status === 'declined' || $transaction->status === 'cancelled') {
                    $order->status = 'cancelled';
                }
                $order->save();
            }

            Log::info('Commandes mises à jour', ['order_group' => $orderGroup, 'orders_count' => $orders->count(), 'new_status' => $transaction->status]);

            // Vider la session du panier seulement après un paiement réussi
            if ($transaction->status === 'approved') {
                Session::forget('cart');
                Log::info('Session panier vidée après paiement réussi', ['order_group' => $orderGroup]);

                return redirect()->route('orders.success', ['orderGroup' => $orderGroup])->with('success', 'Paiement effectué avec succès !');
            } else {
                return redirect()->route('orders.failure')->with('error', 'Le paiement n\'a pas pu être traité.');
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du callback FedaPay', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('orders.failure')->with('error', 'Une erreur est survenue lors du traitement du paiement');
        }
    }

    /**
     * Affiche la page de succès après le paiement.
     * @param  string  $orderGroup
     * @return \Illuminate\View\View
     */
    public function showSuccess($orderGroup)
    {
        $orders = Order::where('order_group', $orderGroup)->get();

        if ($orders->isEmpty()) {
            return redirect()->route('home')->with('error', 'Commande introuvable.');
        }

        $order = $orders->first();
        $orderItems = [];
        $totalProductAmount = 0;

        foreach ($orders as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ];
                $totalProductAmount += $item->subtotal;
            }
        }

        // Le montant total est déjà calculé correctement dans la base (produits + livraison)
        $totalAmount = $order->total_amount;
        $shippingFee = $order->shipping_fee;

        return view('orders.success', compact('order', 'orderItems', 'totalProductAmount', 'totalAmount', 'shippingFee'));
    }

    public function showInvoice(Order $order)
    {
        $orderItems = Order::where('order_group', $order->order_group)->get();
        $totalProductAmount = $orderItems->sum('subtotal');
        $shippingFee = $order->shipping_fee;
        $totalAmount = $totalProductAmount + $shippingFee;

        return view('orders.invoice', compact('order', 'orderItems', 'totalProductAmount', 'shippingFee', 'totalAmount'));
    }

    public function cancel(Request $request)
{
    Log::info('Paiement annulé par l\'utilisateur', [
        'request_data' => $request->all()
    ]);

    return redirect()->route('orders.failure')
        ->with('error', 'Le paiement a été annulé. Vous pouvez réessayer à tout moment.');
}
}
