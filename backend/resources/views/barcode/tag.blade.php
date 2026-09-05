<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: 38mm 25mm;
            margin: 0;
        }
        body {
            width: 38mm;
            height: 25mm;
            margin: 0;
            padding: 1mm;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
            text-align: center;
            overflow: hidden;
            background: #fff;
        }
        .brand {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
        }
        .product-name {
            font-size: 6pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 1mm;
        }
        .barcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10mm;
        }
        .barcode-container svg {
            max-width: 100%;
            height: 100%;
        }
        .sku {
            font-size: 6pt;
            margin-top: 0.5mm;
        }
        .price {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 1mm;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $barcodeData = $generator->getBarcode($sku, $generator::TYPE_CODE_128);
    @endphp

    @if($show_brand)
        <div class="brand">Tori Crown</div>
    @endif

    @if($show_name)
        <div class="product-name">{{ $name }}</div>
    @endif

    @if($show_barcode)
        <div class="barcode-container">
            {!! $barcodeData !!}
        </div>
    @endif

    @if($show_sku)
        <div class="sku">{{ $sku }}</div>
    @endif

    @if($show_price)
        <div class="price">৳ {{ number_format($price, 0) }}</div>
    @endif
</body>
</html>
