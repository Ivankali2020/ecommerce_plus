<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Pyidaungsu';
            src: url("/fonts/Futura100MYA-Regular.woff2") format("woff2");
            font-style: normal;
            font-display: swap;
            font-weight: normal;
        }

        body {
            font-family: 'Pyidaungsu', sans-serif, 'Noto Sans Myanmar', 'DejaVu Sans Mono', 'Courier New', monospace, 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            width: 100%;
        }
        .receipt-container {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 2px;
        }
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 1px;
        }
        .invoice-info {
            margin-bottom: 6px;
            font-size: 11px;
        }
        .items-section {
            margin-bottom: 5px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 1px;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: bold;
            margin-bottom: 4px;
        }
        .item-variant {
            font-size: 8px;
            margin-bottom: 1px;
        }
        .item-price-qty {
            font-size: 8px;
        }
        .item-total {
            font-weight: bold;
            text-align: right;
            min-width: 50px;
            font-size: 11px;
        }
        .totals-section {
            padding-top: 3px;
            margin-bottom: 5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            font-size: 10px;
        }
        .grand-total {
            font-size: 11px;
            font-weight: bold;
            padding-top: 2px;
            margin-top: 3px;
        }
        .payment-info {
            margin-bottom: 5px;
            padding: 3px;
        }
        .qr-section {
            text-align: center;
            margin-bottom: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .qr-code {
            margin: 2px 0;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #666;
            padding-top: 2px;
        }
        .separator {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="store-name">{{ $order->seller->name }}</div>
            <div style="font-size: 10px;">
                @if($order->seller->addresses->count() > 0)
                    {{ $order->seller->addresses->first()->address ?? '' }}
                @endif
            </div>
            <div style="font-size: 10px;">{{ $order->seller->phone ?? '' }} , {{ $order->seller->email ?? '' }}</div>
        </div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div><strong>Invoice:</strong> {{ $order->invoice_number }}</div>
            <div><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</div>
            <div><strong>Cashier:</strong> {{ $order->staff->name ?? 'POS Staff' }}</div>
        </div>

        <div class="separator"></div>

        <!-- Items Section -->
        <div class="items-section">
            <div style="font-weight: bold; margin-bottom: 10px;">ITEMS:</div>

            @foreach($order->products as $item)
                <div class="item">
                    <div class="item-details">
                        <div class="item-name">{{ $item->product->name }}</div>
                        @if($item->variant)
                            <div class="item-variant">Variant: {{ $item->variant->name }}</div>
                        @endif
                        @if($item->discount > 0)
                            <div class="item-price-qty">
                                {{ $item->quantity }} x {{ number_format($item->discount_price) }} MMK ({{ $item->discount }}% off)
                            </div>
                        @else
                            <div class="item-price-qty">
                                {{ $item->quantity }} x {{ number_format($item->price) }} MMK
                            </div>
                        @endif
                    </div>
                    <div class="item-total">{{ number_format($item->sub_price) }} MMK</div>
                </div>
            @endforeach
        </div>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ number_format($order->sub_total) }} MMK</span>
            </div>

            @if($order->tax_price > 0)
                <div class="total-row">
                    <span>Tax ({{ $order->tax }}%):</span>
                    <span>{{ number_format($order->tax_price) }} MMK</span>
                </div>
            @endif

            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ number_format($order->total_price) }} MMK</span>
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->given_amount > 0)
            <div class="payment-info">
                <div class="total-row">
                    <span>Given Amount:</span>
                    <span> - {{ number_format($order->given_amount) }} MMK</span>
                </div>
                <div class="total-row">
                    <span>Change:</span>
                    <span>{{ number_format($order->change_amount) }} MMK</span>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>ကျေးဇူးတင်ပါတယ် ရှင့်!</div>
            <div style="margin-top: 5px;">ပြန်လာဖို့မမေ့ပါနဲ့</div>
        </div>
    </div>
</body>
</html>
