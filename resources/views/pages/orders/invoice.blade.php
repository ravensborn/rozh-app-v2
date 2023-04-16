<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <title>{{env('APP_NAME')}}</title>

    <style>
        .print-body {
            border: 1px solid gray;
            padding: 10px;
            margin: 3px;
            /*width: 360px;*/
            /*height: 500px;*/
            display: inline-block
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        ul li {
            margin-bottom: 7px;
        }

        .barcode {
            text-align: center !important;
        }

        .barcode img {
            width: 140px;
            height: auto;
        }

        /*table {*/
        /*    border-collapse: collapse;*/
        /*}*/

        /*th, td {*/
        /*    border: 1px solid black;*/
        /*    padding: 4px;*/
        /*}*/
    </style>
</head>

<body>

<div class="print-body">


    {{ $order->page->name }}

    <div style="float: right;">

    </div>

    <hr>


    <ul>

        <li class="barcode">
            <img alt='Barcode Generator TEC-IT'
                 src='https://barcode.tec-it.com/barcode.ashx?data={{ $order->number }}&code=Code128&multiplebarcodes=false&translate-esc=true&unit=Fit&dpi=96&imagetype=Jpg&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0&hidehrt=False&dmsize=Default'/>
        </li>

{{--        <li>--}}
{{--            <b>Order Date:</b>--}}
{{--            {{$order->created_at->format('Y-m-d')}}--}}
{{--        </li>--}}

        <li>
            <b>Order Number:</b>
            {{$order->number}}
        </li>

{{--        <li>--}}
{{--            <b>Customer Name:</b>--}}
{{--            {{$order->customer_name}}--}}
{{--        </li>--}}

        <li>
            <b>Forwarder Address:</b>
            {{ $order->forwarderLocation->name }}
        </li>

        <li>
            <b>Delivery Address:</b>
            {{$order->delivery_address}}
        </li>

        <li>
            <b>Mobile:</b>
            {{$order->customer_primary_phone}}
            @if($order->customer_secondary_phone)
                {{ '-' .  $order->customer_secondary_phone }}
            @endif
        </li>
        <li>
            <b>
                Total:
            </b>
            {{ number_format($order->total() + $order->delivery_price) }} IQD
        </li>
    </ul>


    {{--    <table class="table table-sm table-bordered">--}}
    {{--        <thead>--}}
    {{--        <tr>--}}
    {{--            <th>Name</th>--}}
    {{--            <th>Price</th>--}}
    {{--            <th>Quantity</th>--}}
    {{--            <th>Total</th>--}}
    {{--        </tr>--}}
    {{--        </thead>--}}
    {{--        <tbody>--}}
    {{--        @foreach($order->items as $item)--}}

    {{--            <tr>--}}
    {{--                <td style="word-break: break-word; width: 100px;">{{ $item->name }}</td>--}}
    {{--                <td>--}}
    {{--                    {{ number_format($item->price) }}--}}
    {{--                </td>--}}
    {{--                <td>{{ $item->quantity }}</td>--}}
    {{--                <td>--}}
    {{--                    {{ number_format($item->total()) }}--}}
    {{--                </td>--}}

    {{--            </tr>--}}


    {{--        @endforeach--}}


    {{--        <tr>--}}
    {{--            <th>Delivery</th>--}}
    {{--            <th colspan="3">--}}
    {{--            </th>--}}
    {{--            <th>--}}
    {{--                {{ number_format($order->delivery_price) }}--}}
    {{--            </th>--}}
    {{--        </tr>--}}

    {{--        <tr>--}}
    {{--            <th>Total</th>--}}
    {{--            <th colspan="3">--}}
    {{--            </th>--}}
    {{--            <th>--}}
    {{--                {{ number_format($order->total()) }}--}}
    {{--            </th>--}}
    {{--        </tr>--}}


    {{--        </tbody>--}}
    {{--    </table>--}}


</div>


<div class="d-print-none p-3 ps-0">
    <button class="d-print-none btn btn-primary" style="width: 200px;" onclick="window.print()">Print</button>

</div>


</body>
</html>
