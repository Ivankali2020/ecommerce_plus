@php use Picqer\Barcode\BarcodeGeneratorHTML; @endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        [x-cloak] {
            display: none !important;
        }

        input::placeholder {
            font-size: 13px !important;
        }

        select::placeholder {
            font-size: 13px !important;
        }

        select {
            font-size: 13px !important;
        }
    </style>

    @livewireScripts

    {{--    @filamentStyles--}}
    @vite('resources/css/app.css')
</head>

<body class="antialiased" onload="print()">

<div class="d-flex flex-wrap justify-content-center">
    @foreach(range(1,$page) as $a)
        <div class="text-center flex-col  flex items-center justify-center m-3  ">
            <p class="text-black font-bold   text-sm  ">{{ $price }} Ks</p>
            {!! (new BarcodeGeneratorHTML())->getBarcode($code,(new BarcodeGeneratorHTML())::TYPE_CODE_128,1,50) !!}
            @if($name != null)
                <p class="text-gray-500 text-sm mt-1 ">{{ $name }}</p>
            @endif
        </div>
    @endforeach
</div>


{{--@livewire('notifications')--}}
{{--@filamentScripts--}}
@vite('resources/js/app.js')
<script>

</script>
</body>
</html>
