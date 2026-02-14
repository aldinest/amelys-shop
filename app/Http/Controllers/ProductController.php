<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('type', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')
                        ->paginate(10)
                        ->withQueryString();

        return view('user.products.index', compact('products'));
    }


    public function create()
    {
        return view('user.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
        ]);

        Product::create($request->only('name', 'type'));

        return redirect()
            ->route('user.products.index')
            ->with('success', 'Product created');
    }

    public function edit(Product $product)
    {
        return view('user.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $product->update($request->only('name', 'type'));

        return redirect()
            ->route('user.products.index')
            ->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('user.products.index')
            ->with('success', 'Product deleted');
    }
}
