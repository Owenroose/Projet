<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Affiche la vue du panier.
     */
    public function show()
    {
        // Récupère le panier de la session, en fournissant un tableau vide par défaut
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if ($product) {
                $cartItems[$id] = [
                    'product' => $product,
                    'quantity' => $details['quantity'],
                    'subtotal' => $product->price * $details['quantity']
                ];
                $total += $cartItems[$id]['subtotal'];
            }
        }

        return view('cart.show', compact('cartItems', 'total'));
    }

    /**
     * Ajoute un produit au panier.
     */
    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $product = Product::findOrFail($request->product_id);

        // Vérifier le stock
        if (!$product->in_stock) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit n\'est plus en stock.'
            ], 400);
        }

        // Récupère le panier, en garantissant que c'est un tableau
        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            // Si le produit existe, on augmente la quantité
            $cart[$product->id]['quantity']++;
        } else {
            // Sinon, on ajoute le produit au panier
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image,
                "slug" => $product->slug,
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier avec succès.',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Met à jour la quantité d'un produit dans le panier.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = Session::get('cart', []);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > 0) {
            // Mise à jour de la quantité
            $cart[$request->product_id]['quantity'] = $request->quantity;
        } else {
            // Si la quantité est 0, on retire le produit du panier
            unset($cart[$request->product_id]);
        }

        Session::put('cart', $cart);

        // Re-calculer le total et le nombre d'articles
        $totalItems = 0;
        $total = 0;
        foreach ($cart as $item) {
            $totalItems += $item['quantity'];
            $total += $item['quantity'] * $item['price'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Panier mis à jour avec succès.',
            'cart_count' => $totalItems,
            'cart_total' => $total
        ]);
    }

    /**
     * Retire un produit du panier.
     */
    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $cart = Session::get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            Session::put('cart', $cart);
        }

        // Re-calculer le total et le nombre d'articles
        $totalItems = 0;
        $total = 0;
        foreach ($cart as $item) {
            $totalItems += $item['quantity'];
            $total += $item['quantity'] * $item['price'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier avec succès.',
            'cart_count' => $totalItems,
            'cart_total' => $total
        ]);
    }

    /**
     * Vide le panier.
     */
    public function clear()
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé avec succès.',
            'cart_count' => 0
        ]);
    }

    /**
     * Récupère le contenu du panier en JSON.
     */
    public function getCartData()
    {
        $cart = Session::get('cart', []);
        $totalItems = 0;
        $total = 0;

        // Assurez-vous que $cart est un tableau avant de l'itérer
        if (is_array($cart)) {
            foreach ($cart as $item) {
                // S'assurer que chaque élément a une clé 'quantity'
                $quantity = $item['quantity'] ?? 0;
                $totalItems += $quantity;
                $total += ($item['price'] ?? 0) * $quantity;
            }
        }

        return response()->json([
            'cart' => $cart,
            'cart_count' => $totalItems,
            'total' => $total
        ]);
    }

    /**
     * Récupère le nombre d'articles dans le panier.
     */
    public function getCartCount()
    {
        $cart = Session::get('cart', []);

        // Utilisation de array_sum avec array_column pour plus de sécurité
        // array_column retournera un tableau, même si $cart est vide
        $quantities = array_column($cart, 'quantity');
        $totalItems = array_sum($quantities);

        return response()->json([
            'cart_count' => $totalItems
        ]);
    }

    /**
     * Synchronise le panier avec les données de session.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'cart' => 'required|array'
        ]);

        // Valider chaque élément du panier
        foreach ($request->cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit invalide détecté dans le panier.'
                ], 400);
            }

            // Vérifier le stock
            if (!$product->in_stock) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' n\'est plus en stock.'
                ], 400);
            }
        }

        Session::put('cart', $request->cart);

        return response()->json([
            'success' => true,
            'message' => 'Panier synchronisé avec succès.'
        ]);
    }
}
