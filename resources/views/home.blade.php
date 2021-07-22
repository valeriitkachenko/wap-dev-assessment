<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Valerii Tkachenko - Assessment</title>

        <!-- Styles -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    </head>
    <body class="antialiased container">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            <h1 class="mb-5 text-center">Valerii Tkachenko - Assessment</h1>

            <div class="mb-5 text-center">
                <form method="POST" action="{{ route('marketplace.fetch') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Click me!</button>
                    <p class="mt-3">Feel free to click on the button above to fetch a new Order or Product from the WAP test API endpoint</p>
                </form>
            </div>

            <div class="flex flex-nowrap flex-column">
                <h2 id="accented-tables">Orders</h2>
                @include('partials/orders', ['orders' => $orders])

                <h2 id="accented-tables">Products</h2>
                @include('partials/products', ['products' => $products])
            </div>
        </div>
    </body>
</html>
