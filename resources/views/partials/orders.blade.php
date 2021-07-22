<div class="mb-5 mx-auto sm:px-6 lg:px-8">
    @if(empty($orders) || $orders->isEmpty())
        <p>Ooops, seems like we don't have any orders yet! But it's ok, use the button above to get some ;)</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" class="text-right">External ID</th>
                    <th scope="col" class="text-right">Total</th>
                    <th scope="col" class="text-right">Shipping total</th>
                    <th scope="col">Create time</th>
                    <th scope="col">Timezone</th>
                </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <th scope="row">{{ $order->id }}</th>
                    <td class="text-right">{{ $order->external_id }}</td>
                    <td class="text-right">{{ $order->total }}</td>
                    <td class="text-right">{{ $order->shipping_total }}</td>
                    <td>{{ $order->create_time }}</td>
                    <td>{{ $order->timezone }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
