<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if (!empty($data['parent_id'])) {
            $parent = Category::find($data['parent_id']);
            if ($parent->parent_id) {
                return back()->withErrors(['name' => 'Sub-kategori hanya 1 tingkat.']);
            }
        }

        Category::create($data);
        return back();
    }
}
