<div class="mb-5 mx-auto sm:px-6 lg:px-8">
    @if(empty($products) || $products->isEmpty())
        <p>Ooops, seems like we don't have any products yet! But it's ok, use the button above to get some ;)</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">SKU</th>
                    <th scope="col">Image</th>
                </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <th scope="row">{{ $product->id }}</th>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->SKU }}</td>
                    <td>
                        @if(empty($product->image))
                            -
                        @else
                            <img style="max-width: 50px; max-height: 50px; width: auto; height: auto"
                                 src="{{ $product->image }}">
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
