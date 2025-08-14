<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Sample Product 1',
                'SKU001',
                'Sample product description',
                'Electronics',
                'Samsung',
                'Supplier ABC',
                'MMK',
                150000.00,
                220000.00,
                50,
                10,
                170000, // 10% discount for mmk
                1,
                'No',
                'Yes',
                'No',
                'No',
                'No',
                'Yes',
                'size:lg,xl;color:red,blue'
            ],
            [
                'Sample Product 2',
                'SKU002',
                'Another sample product',
                'Clothing',
                'Nike',
                'Supplier XYZ',
                'MMK',
                75000.00,
                120000.00,
                100,
                15,
                100000, // 15% discount for mmk
                1,
                'Yes',
                'Yes',
                'Yes',
                'No',
                'Yes',
                'Yes',
                'material:cotton;brand_type:premium,standard'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'SKU',
            'Description',
            'Category',
            'Brand',
            'Supplier',
            'Currency',
            'Buy Price',
            'Price',
            'Quantity',
            'Discount',
            'Discount Price',
            'MOQ',
            'Has Variant',
            'Is Public',
            'Is Recommended',
            'Is Wholesale',
            'Is Return',
            'Is Shipped',
            'Attributes'
        ];
    }
}
