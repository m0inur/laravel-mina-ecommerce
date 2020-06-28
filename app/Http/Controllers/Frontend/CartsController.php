<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\Order;

use Auth;
class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('frontend.pages.carts');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required'
        ],
        [
            'product_id.required' => 'Please give a product'
        ]);

        if(Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('order_id', NULL)
            ->first();
        } else {
            $cart = Cart::where('ip_adress', request()->ip())
            ->where('product_id', $request->product_id)
            ->where('order_id', NULL)
            ->first();
        }

        // $cart = Cart::orWhere('user_id', Auth::id())
        //             ->orWhere('ip_adress', request()->ip())
        //             ->Where('product_id', $request->product_id)
        //             ->first();

        if(!is_null($cart)) {
            // dd($request->product_id);
            $cart->increment('product_quantity');
        } else {
            // dd('test');
            $cart = new Cart();
            if(Auth::check()) {
    
                $cart->user_id = Auth::id();
            } 
            $cart->ip_adress = request()->ip();
            $cart->product_id = $request->product_id;
            $cart->save();
        }
        

        session()->flash('success', 'Product has been added to cart');
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cart = Cart::find($id);
        if(!is_null($cart)) {
            $cart->product_quantity = $request->product_quantity;
            $cart->save();
        } else {
            return redirect()->route('carts');
        }
        session()->flash('success', 'Cart has been updated');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cart = Cart::find($id);
        if(!is_null($cart)) {
            $cart->delete();
        } else {
            return redirect()->route('carts');
        }
        session()->flash('success', 'Cart Item has been deleted');
        return back();
    }
}
