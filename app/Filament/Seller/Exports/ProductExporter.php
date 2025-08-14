<?php

namespace App\Filament\Seller\Exports;

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
            ExportColumn::make('name')
                ->label('Product Name'),
            ExportColumn::make('description'),
            ExportColumn::make('sku')
                ->label('Product SKU'),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('brand.name')
                ->label('Brand'),
            ExportColumn::make('supplier.name')
                ->label('Supplier'),
            ExportColumn::make('price'),
            ExportColumn::make('buy_price'),
            ExportColumn::make('mmk_price'),
            ExportColumn::make('mmk_buy_price'),
            ExportColumn::make('discount')
                ->label('Discount %'),
            ExportColumn::make('discount_price'),
            ExportColumn::make('mmk_discount_price'),
            ExportColumn::make('quantity'),
            ExportColumn::make('MOQ'),
            ExportColumn::make('attributes')
                ->formatStateUsing(fn ($state) => json_encode($state)),
            ExportColumn::make('has_variant')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),
            ExportColumn::make('is_public')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),
            ExportColumn::make('is_recommended')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),
            ExportColumn::make('is_whole_sale')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),
            ExportColumn::make('is_return')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),
            ExportColumn::make('is_shipped')
                ->formatStateUsing(fn ($state) => $state ? 'true' : 'false'),
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
