<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->invoice_no }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            background: #eee;
            padding: 20px;
        }

        .invoice-container {
            max-width: 350px;
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .border-top {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .border-bottom {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 5px 0;
        }

        .text-end {
            text-align: right;
        }


        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }

            /* 80mm পেপার সাইজ */
            body {
                margin: 10px;
                font-size: 12px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="invoice-container">
        <div class="text-center">
            <h2 class="mb-1">My POS Shop</h2>
            <p class="mb-1">Dhaka, Bangladesh</p>
            <p>Phone: 01700-000000</p>
        </div>

        <div class="border-bottom"></div>

        <div>
            <p><strong>Inv No:</strong> {{ $order->invoice_no }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('d-M-Y h:i A') }}</p>
            <p><strong>Customer:</strong> {{ $order->customer->name ?? 'Walk-in' }}</p>
        </div>

        <div class="border-bottom"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Item</th>
                    <th style="width: 20%">Qty</th>
                    <th style="width: 20%">Price</th>
                    <th class="text-end" style="width: 20%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{-- প্রোডাক্ট আছে কিনা আগে চেক করা হচ্ছে --}}
                            @if ($item->product && $item->product->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                    style="width: 30px; height: 30px; object-fit: cover; margin-right: 5px; vertical-align: middle;">
                            @endif

                            {{-- প্রোডাক্ট ডিলিট হলেও নাম দেখাবে (অর্ডার আইটেম টেবিল থেকে) --}}
                            {{ $item->product_name }}

                            @if ($item->variant_name)
                                <small>({{ $item->variant_name }})</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-top"></div>

        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-end">{{ $order->subtotal }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td class="text-end">- {{ $order->discount }}</td>
            </tr>
            <tr class="fw-bold" style="font-size: 16px;">
                <td>Grand Total</td>
                <td class="text-end">{{ $order->total_amount }}</td>
            </tr>
            <tr>
                <td>Paid</td>
                <td class="text-end">{{ $order->paid_amount }}</td>
            </tr>
            <tr>
                <td>Change</td>
                <td class="text-end">{{ number_format($order->paid_amount - $order->total_amount, 2) }}</td>
            </tr>
        </table>

        <div class="border-top"></div>

        <div class="text-center">
            <p>Thank you for shopping!</p>
            <small>Software by Smart-Pos-System</small>
        </div>

        <div class="text-center no-print" style="margin-top: 20px;">
            <button onclick="window.print()" style="padding: 5px 10px; cursor: pointer;">Print Again</button>
            <a href="{{ route('pos.index') }}" style="margin-left: 10px;">Back to POS</a>
        </div>
    </div>

</body>

</html>
