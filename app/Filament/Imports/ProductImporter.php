<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('seller_id')
//                ->relationship(resolveUsing: 'name')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('brand')
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),

            ImportColumn::make('category')
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),

            ImportColumn::make('exchange_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),

            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required', 'max:16777215']),

            ImportColumn::make('attributes'),

            ImportColumn::make('MOQ')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),

            ImportColumn::make('sku')
                ->label('SKU')
                ->rules(['max:255']),

            ImportColumn::make('buy_price')
                ->numeric()
                ->rules(['integer']),

            ImportColumn::make('price')
                ->numeric()
                ->rules(['integer']),

            ImportColumn::make('quantity')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('discount')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('discount_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('mmk_buy_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('mmk_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('mmk_discount_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('has_variant')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_public')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_recommended')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_whole_sale')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_return')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_shipped')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        //         return Product::firstOrNew([
        //             // Update existing records, matching them by `$this->data['column_name']`
        //             'email' => $this->data['email'],
        //         ]);

        return new Product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
