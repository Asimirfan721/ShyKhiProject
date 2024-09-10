<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use DB;

class PaypalController extends Controller
{
    public function payment()
    {
        $cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->get()->toArray();

        $data = [];
        $data['items'] = array_map(function ($item) {
            $name = Product::where('id', $item['product_id'])->pluck('title')->first();
            return [
                'name' => $name,
                'price' => $item['price'],
                'desc'  => 'Thank you for using PayPal',
                'qty'   => $item['quantity']
            ];
        }, $cart);

        $data['invoice_id'] = 'ORD-' . strtoupper(uniqid());
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = route('payment.success');
        $data['cancel_url'] = route('payment.cancel');

        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $data['total'] = $total;

        if (session('coupon')) {
            $data['shipping_discount'] = session('coupon')['value'];
        }

        Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => session()->get('id')]);

        // Initialize PayPal provider
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));

        // Create the PayPal order
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $data['total']
                    ]
                ]
            ],
            "application_context" => [
                "cancel_url" => $data['cancel_url'],
                "return_url" => $data['return_url']
            ]
        ]);

        // Redirect user to PayPal payment page
        if (isset($response['id']) && $response['status'] === 'CREATED') {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect($link['href']);
                }
            }
        }

        session()->flash('error', 'Something went wrong with the PayPal payment.');
        return redirect()->back();
    }

    public function cancel()
    {
        dd('Your payment was canceled. You can create a cancel page here.');
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));

        // Capture the PayPal payment
        $response = $provider->capturePaymentOrder($request->token);

        if ($response['status'] === 'COMPLETED') {
            session()->flash('success', 'You successfully paid through PayPal! Thank you.');
            session()->forget('cart');
            session()->forget('coupon');
            return redirect()->route('home');
        }

        session()->flash('error', 'Something went wrong with your payment. Please try again.');
        return redirect()->back();
    }
}
