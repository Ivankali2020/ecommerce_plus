<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('seller_id'),
            ExportColumn::make('brand.name'),
            ExportColumn::make('category.name'),
            ExportColumn::make('exchange_id'),
            ExportColumn::make('name'),
            ExportColumn::make('description'),
            ExportColumn::make('attributes')->listAsJson(),
            ExportColumn::make('MOQ'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('buy_price'),
            ExportColumn::make('price'),
            ExportColumn::make('quantity'),
            ExportColumn::make('discount'),
            ExportColumn::make('discount_price'),
            ExportColumn::make('mmk_buy_price'),
            ExportColumn::make('mmk_price'),
            ExportColumn::make('mmk_discount_price'),
            ExportColumn::make('has_variant'),
            ExportColumn::make('variants_exists')->exists('variants'),
            ExportColumn::make('is_public'),
            ExportColumn::make('is_recommended'),
            ExportColumn::make('is_whole_sale'),
            ExportColumn::make('is_return'),
            ExportColumn::make('is_shipped'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
