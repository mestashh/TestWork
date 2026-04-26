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

        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        if ($search) {
            $builder = Product::search($search);

            if ($categoryId) {
                $builder->where('category_id', (int) $categoryId);
            }

            return $builder
                ->query(fn ($query) => $query->with('category'))
                ->paginate(15)
                ->withQueryString();
        }

        return Product::query()
            ->with('category')
            ->when($categoryId, fn ($q, $id) => $q->where('category_id', $id))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
    }
}
