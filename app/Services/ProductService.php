<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        return Product::query()
            ->with('category')
            ->when(
                request('category_id'),
                fn ($query, $categoryId) => $query->where('category_id', $categoryId)
            )
            ->when(
                request('search'),
                fn ($query, $search) => $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
            )
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
    }
}
