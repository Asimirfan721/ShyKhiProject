<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::getAllProduct();
        return view('backend.product.index')->with('products', $products);
    }

    public function create()
    {
        $brands = Brand::all();
        $categories = Category::where('is_parent', 1)->get();
        return view('backend.product.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'size' => 'nullable|array',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric'
        ]);

        $data = $request->except('photo');
        $data['size'] = $request->input('size') ? implode(',', $request->input('size')) : '';
        $data['slug'] = $this->generateSlug($request->title);
        $data['is_featured'] = $request->input('is_featured', 0);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $data['photo'] = Product::uploadPhoto($file);
        }

        $status = Product::create($data);

        session()->flash($status ? 'success' : 'error', $status ? 'Product Successfully added' : 'Please try again!!');
        return redirect()->route('product.index');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('backend.product.show', compact('product'));
    }

    public function edit($id)
    {
        $brands = Brand::all();
        $product = Product::findOrFail($id);
        $categories = Category::where('is_parent', 1)->get();
        return view('backend.product.edit', compact('product', 'brands', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $this->validate($request, [
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'size' => 'nullable|array',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $data['size'] = $request->input('size') ? implode(',', $request->input('size')) : '';
        $data['is_featured'] = $request->input('is_featured', 0);

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($product->photo) {
                Product::deletePhoto($product->photo);
            }

            // Upload new photo
            $file = $request->file('photo');
            $data['photo'] = Product::uploadPhoto($file);
        }

        $status = $product->fill($data)->save();

        session()->flash($status ? 'success' : 'error', $status ? 'Product Successfully updated' : 'Please try again!!');
        return redirect()->route('product.index');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete photo before deleting the product
        if ($product->photo) {
            Product::deletePhoto($product->photo);
        }

        $status = $product->delete();

        session()->flash($status ? 'success' : 'error', $status ? 'Product successfully deleted' : 'Error while deleting product');
        return redirect()->route('product.index');
    }

    private function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = Product::where('slug', $slug)->count();
        return $count > 0 ? $slug . '-' . date('ymdis') . '-' . rand(0, 999) : $slug;
    }
}
