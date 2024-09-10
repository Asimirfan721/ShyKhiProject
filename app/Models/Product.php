<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Cart;
use App\Models\Brand;

class Product extends Model
{
    protected $fillable = [
        'title', 'slug', 'summary', 'description', 'cat_id', 'child_cat_id',
        'price', 'brand_id', 'discount', 'status', 'photo', 'size', 'stock',
        'is_featured', 'condition'
    ];

    public function cat_info()
    {
        return $this->hasOne('App\Models\Category', 'id', 'cat_id');
    }

    public function sub_cat_info()
    {
        return $this->hasOne('App\Models\Category', 'id', 'child_cat_id');
    }

    public static function getAllProduct()
    {
        return Product::with(['cat_info', 'sub_cat_info'])->orderBy('id', 'desc')->paginate(10);
    }

    public function rel_prods()
    {
        return $this->hasMany('App\Models\Product', 'cat_id', 'cat_id')
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(8);
    }

    public function getReview()
    {
        return $this->hasMany('App\Models\ProductReview', 'product_id', 'id')
            ->with('user_info')
            ->where('status', 'active')
            ->orderBy('id', 'DESC');
    }

    public static function getProductBySlug($slug)
    {
        return Product::with(['cat_info', 'rel_prods', 'getReview'])
            ->where('slug', $slug)
            ->first();
    }

    public static function countActiveProduct()
    {
        return Product::where('status', 'active')->count();
    }

    public function carts()
    {
        return $this->hasMany(Cart::class)->whereNotNull('order_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class)->whereNotNull('cart_id');
    }

    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    /**
     * Get the URL of the product photo.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && Storage::exists('public/images/' . $this->photo)) {
            return Storage::url('public/images/' . $this->photo);
        }
        return asset('images/default.jpg'); // Provide a default image URL if necessary
    }

    /**
     * Store the uploaded photo and return the filename.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    public static function uploadPhoto($file)
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/images', $filename);
        return $filename;
    }

    /**
     * Delete the photo from storage.
     *
     * @param  string  $filename
     * @return void
     */
    public static function deletePhoto($filename)
    {
        if (Storage::exists('public/images/' . $filename)) {
            Storage::delete('public/images/' . $filename);
        }
    }
}
