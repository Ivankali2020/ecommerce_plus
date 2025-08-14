<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Exports\VariantExport;
use App\Imports\ProductImport;
use App\Imports\VariantImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportExportController extends Controller
{
    public function exportProducts()
    {
        return Excel::download(new ProductExport, 'products_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportVariants()
    {
        return Excel::download(new VariantExport, 'variants_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file'));
            return back()->with('success', 'Products imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    public function importVariants(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new VariantImport, $request->file('file'));
            return back()->with('success', 'Variants imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing variants: ' . $e->getMessage());
        }
    }
}
