<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected $product;
    protected $wishlist;

    public function __construct(Product $product, Wishlist $wishlist)
    {
        $this->product = $product;
        $this->wishlist = $wishlist;
    }

    public function wishlist(Request $request)
    {
        $slug = $request->input('slug');
        
        if (empty($slug)) {
            return back()->with('error', 'Invalid Product');
        }

        $product = $this->product->where('slug', $slug)->first();
        
        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        $userId = Auth::id();
        $alreadyInWishlist = $this->wishlist->where('user_id', $userId)
                                            ->where('product_id', $product->id)
                                            ->whereNull('cart_id')
                                            ->exists();
        
        if ($alreadyInWishlist) {
            return back()->with('error', 'Product already in wishlist');
        }

        if ($product->stock < 1) {
            return back()->with('error', 'Stock not sufficient');
        }

        $wishlist = new Wishlist();
        $wishlist->user_id = $userId;
        $wishlist->product_id = $product->id;
        $wishlist->price = $product->price - ($product->price * $product->discount / 100);
        $wishlist->quantity = 1;
        $wishlist->amount = $wishlist->price * $wishlist->quantity;
        $wishlist->save();

        return back()->with('success', 'Product successfully added to wishlist');
    }

    public function wishlistDelete(Request $request)
    {
        $id = $request->input('id');
        $wishlist = $this->wishlist->find($id);
        
        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Wishlist item successfully removed');
        }
        
        return back()->with('error', 'Error occurred, please try again');
    }
}
