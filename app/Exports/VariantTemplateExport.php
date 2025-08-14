<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VariantTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                1,
                'Red Large',
                'SKU001-RL',
                120.00,
                180.00,
                25,
                5,
                171.00,
                1,
                'No',
                'size:large;color:red'
            ],
            [
                1,
                'Blue Medium',
                'SKU001-BM',
                110.00,
                170.00,
                30,
                0,
                170.00,
                1,
                'No',
                'size:medium;color:blue'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Variant Name',
            'SKU',
            'Buy Price',
            'Price',
            'Quantity',
            'Discount',
            'Discount Price',
            'MOQ',
            'Is Wholesale',
            'Attributes'
        ];
    }
}
