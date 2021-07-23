<?php

namespace App\Http\Controllers;

use App\Facades\Marketplace;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Show the home page.
     *
     * @return View|Factory
     */
    public function index()
    {
        $orders = Order::all();
        $products = Product::all();

        return view('home', compact(['orders', 'products']));
    }

    /**
     * Get a new order|product from Marketplace API and save it to DB
     *
     * @return RedirectResponse
     */
    public function store(): RedirectResponse
    {
        $model = Marketplace::getProductOrOrderEntity();

        if (!$model->exists) {
            $model->save();
        }

        return redirect()->route('home');
    }
}
