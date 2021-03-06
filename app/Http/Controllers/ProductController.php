<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        if (request()->categorie) {
            $products = Product::with('categories')->whereHas('categories', function ($query) {
                $query->where('slug', request()->categorie);
            })->orderBy('created_at', 'DESC')->paginate(6);
        } else {
            $products = Product::with('categories')->orderBy('created_at', 'DESC')->paginate(6);
        }

        return view('products.index')->with('products', $products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $stock = $product->stock == 0 ? 'Indisponible' : 'Disponible';
        return view('products.show')->with([
            'product'=> $product,
            'stock' => $stock
        ]);
    }

    public function search()
    {
        request()->validate([
            'query' => 'required|min:3'
        ]);

        $query = request()->input('query');

        $products = Product::where('title', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%")
                ->paginate(6);

        return view('products.search')->with('products', $products);
    }
}
