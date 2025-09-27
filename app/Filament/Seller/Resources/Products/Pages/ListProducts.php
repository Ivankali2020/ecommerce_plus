<?php

namespace App\Filament\Seller\Resources\Products\Pages;

use App\Exports\ProductExport;
use App\Exports\ProductTemplateExport;
use App\Exports\VariantExport;
use App\Exports\VariantTemplateExport;
use App\Filament\Seller\Resources\Products\ProductResource;
use Exception;
use App\Imports\ProductImport;
use App\Imports\VariantImport;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {

        return [

            // Template Download Actions
            Action::make('download_product_template')
                ->label(__('import_export.actions.download_product_template'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')->outlined()
                ->action(function () {
                    return Excel::download(new ProductTemplateExport, __('import_export.file_names.product_template'));
                }),

            Action::make('download_variant_template')
                ->label(__('import_export.actions.download_variant_template'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')->outlined()
                ->action(function () {
                    return Excel::download(new VariantTemplateExport, __('import_export.file_names.variant_template'));
                }),

            ActionGroup::make([

                // Export Actions
                Action::make('export_products')
                    ->label(__('import_export.actions.export_products'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $timestamp = now()->format('Y-m-d_H-i-s');
                        $filename = str_replace(':timestamp', $timestamp, __('import_export.file_names.products_export'));

                        return Excel::download(new ProductExport, $filename);
                    }),

                Action::make('export_variants')
                    ->label(__('import_export.actions.export_variants'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function () {
                        $timestamp = now()->format('Y-m-d_H-i-s');
                        $filename = str_replace(':timestamp', $timestamp, __('import_export.file_names.variants_export'));

                        return Excel::download(new VariantExport, $filename);
                    }),

                // Import Actions
                Action::make('import_products')
                    ->label(__('import_export.actions.import_products'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->schema([
                        FileUpload::make('file')
                            ->label(__('import_export.form.select_excel_file'))
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->helperText(__('import_export.form.download_template_first'))
                            ->directory('imports')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Get the uploaded file from storage
                            $file = $data['file'];

                            // Remove any "public/" prefix if it exists (Filament sometimes adds this)
                            // $cleanFile = str_replace('public/', '', $file);

                            // // Ensure the file path includes imports/ directory
                            // if (! str_starts_with($cleanFile, 'imports/')) {
                            //     $cleanFile = 'imports/'.$cleanFile;
                            // }

                            $filePath = Storage::disk('public')->path($file);

                            // Dispatch the import job
                            Excel::import(new ProductImport, $filePath);

                            // Clean up uploaded file
                            Storage::disk('public')->delete($file);

                            Notification::make()
                                ->title(__('import_export.notifications.products_imported_success'))
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title(__('import_export.notifications.error_importing_products'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),                Action::make('import_variants')
                    ->label(__('import_export.actions.import_variants'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->schema([
                        FileUpload::make('file')
                            ->label(__('import_export.form.select_excel_file'))
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                            ->helperText(__('import_export.form.ensure_products_exist'))
                            ->directory('imports')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Get the uploaded file from storage
                            $file = $data['file'];

                            // // Remove any "public/" prefix if it exists (Filament sometimes adds this)
                            // $cleanFile = str_replace('public/', '', $file);

                            // // Ensure the file path includes imports/ directory
                            // if (! str_starts_with($cleanFile, 'imports/')) {
                            //     $cleanFile = 'imports/'.$cleanFile;
                            // }

                            $filePath = Storage::disk('public')->path($file);

                             // Dispatch the import job
                            Excel::import(new VariantImport, $filePath);
                            // Clean up uploaded file
                            Storage::disk('public')->delete($file);

                            Notification::make()
                               ->title(__('import_export.notifications.products_imported_success'))
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title(__('import_export.notifications.error_importing_products'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

            ])->label(__('import_export.actions.actions_group'))
                ->button()->outlined()
                ->icon('heroicon-o-cog')
                ->color('primary'),

            CreateAction::make(),
        ];
    }
}
