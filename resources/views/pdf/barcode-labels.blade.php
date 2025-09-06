<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'mm';
            src: url('/fonts/mm.ttf') format('truetype');
            font-style: normal;
            font-display: swap;
            font-weight: normal;
        }
        
        body {
            font-family: 'mm', 'DejaVu Sans Mono', 'Courier New', monospace, 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        
        .label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm;
            justify-content: center;
            padding: 5mm;
        }
        
        .label {
            /* EXACT same dimensions as preview */
            width: {{ $labelData['width_mm'] }}mm;
            height: {{ $labelData['height_mm'] }}mm;
            min-width: {{ $labelData['width_mm'] }}mm;
            
            /* EXACT same styling as preview */
            text-align: center;
            border: 1px solid #000;
            padding: 2px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-inside: avoid;
        }
        
        .sku-text {
            font-size: 10px;
            margin-bottom: 2px;
            line-height: 1;
        }
        
        .barcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            flex: 1;
            max-width: 100%;
        }
        
        .barcode-container svg {
            max-width: 100%;
            height: auto;
        }
        
        .product-name {
            font-size: 10px;
            margin: 4px 0;
            line-height: 1;
        }
        
        .price {
            font-weight: bold;
            font-size: 10px;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="label-container">
        @foreach($labels as $label)
            <div class="label">
                <!-- SKU - exact same as preview -->
                <div class="sku-text">{{ $label['sku'] }}</div>
                
                <!-- Barcode - exact same as preview -->
                <div class="barcode-container">
                    {!! $label['barcode_html'] !!}
                </div>
                
                <!-- Product Name - exact same as preview -->
                <div class="product-name">{{ $label['name'] }}</div>
                
                <!-- Price - exact same as preview -->
                <div class="price">{{ number_format($label['price']) }} ks</div>
            </div>
        @endforeach
    </div>
</body>
</html>
