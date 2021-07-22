<?php

namespace App\Http\Controllers;

use App\Facades\Marketplace;
use App\Models\Order;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function index()
    {
        $orders = Order::all();
        $products = Product::all();

        return view('home', compact(['orders', 'products']));
    }

    /*
     * Get a new order|product from Marketplace API and save it to DB
     */
    public function store()
    {
        $model = Marketplace::getProductOrOrderInstance();

        if (!$model->exists) {
            $model->save();
        }

        return redirect()->route('home');
    }
}
