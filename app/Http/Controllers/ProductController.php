<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); // Corrected line
        $categories = Product::select('category')->distinct()->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $relatedProducts = Product::where('id', '!=', $product->id)
                                ->where('category', $product->category)
                                ->take(4)->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
