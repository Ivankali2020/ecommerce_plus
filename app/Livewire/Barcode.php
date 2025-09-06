<?php

namespace App\Livewire;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use App\Models\Printer;
use App\Models\Variant;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\PdfToImage\Enums\OutputFormat;

class Barcode extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public ?Product $product = null;

    public ?Variant $variant = null;

    public $barcodeTypes = [
        'C39' => 'C39',
        // 'C39+' => 'C39+',
        // 'C39E' => 'C39E',
        // 'C39E+' => 'C39E+',
        // 'C93' => 'C93',
        // 'C128' => 'C128',
        // 'C128A' => 'C128A',
        // 'C128B' => 'C128B',
        'CODABAR' => 'CODABAR',
        'QRCODE' => 'QRCODE',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')
            ->components([
                Select::make('product_id')
                    ->label(__('filament/resources/barcode-resource.labels.product_name'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (?string $state) {
                        if ($state) {
                            $this->product = Product::where('id', $state)->first();
                        } else {
                            $this->reset('product');
                        }
                    })
                    ->options(Product::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id')),

                Select::make('variant_id')->required()
                    ->label(__('filament/resources/barcode-resource.labels.variant'))
                    ->hidden(fn (Get $get) => blank($get('product_id')) || ! Product::where('id', $get('product_id'))->select('has_variant')->first()->has_variant)
                    ->searchable()->preload()->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        if ($state) {
                            $this->variant = Variant::where('id', $state)->first();
                        } else {
                            $this->reset('variant');
                        }
                    })
                    ->options(function (Get $get) {
                        return Product::where('id', $get('product_id'))->first()->variants->pluck('name', 'id');
                    }),

                Select::make('type')
                    ->label(__('filament/resources/barcode-resource.labels.type'))
                    ->native(false)
                    ->live()
                    ->options($this->barcodeTypes),

                TextInput::make('total')
                    ->label(__('filament/resources/barcode-resource.labels.total'))
                    ->maxValue(50)
                    ->live()->integer()->required()
                    ->default(1),

                TextInput::make('width_mm')
                    ->label('Width (mm)')
                    ->numeric()
                    ->live()
                    ->default(50)
                    ->minValue(20)
                    ->maxValue(200)
                    ->suffix('mm'),

                TextInput::make('height_mm')
                    ->label('Height (mm)')
                    ->numeric()
                    ->live()
                    ->default(30)
                    ->minValue(10)
                    ->maxValue(100)
                    ->suffix('mm'),

                TextInput::make('barcode_width')
                    ->label('Barcode Width')
                    ->numeric()
                    ->live()
                    ->default(2)
                    ->minValue(1)
                    ->maxValue(10)
                    ->helperText('Width multiplier for barcode bars'),

                TextInput::make('barcode_height')
                    ->label('Barcode Height')
                    ->numeric()
                    ->live()
                    ->default(43)
                    ->minValue(10)
                    ->maxValue(200)
                    ->helperText('Height of barcode bars in pixels'),

            ])->columns(columns: 4);
    }

    public function printBarcodes()
    {
        try {
            if (!$this->product || !$this->data['type'] || !$this->data['total']) {
                Notification::make()
                    ->title('Incomplete data')
                    ->body('Please select product, barcode type, and quantity.')
                    ->warning()
                    ->send();
                return;
            }

            // Get authenticated seller
            $user = auth('seller')->user();
            if (!$user) {
                Notification::make()
                    ->title('Authentication required')
                    ->danger()
                    ->send();
                return;
            }

            // Check for default printer
            $sellerId = $user->parent_id ?? $user->id;
            $printerConfig = Printer::where('seller_id', $sellerId)
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();

            if (!$printerConfig) {
                Notification::make()
                    ->title('No printer configured')
                    ->body('Please configure a default printer in settings.')
                    ->warning()
                    ->send();
                return;
            }

            // Generate PDF and print
            $pdfPath = $this->generateBarcodePdf();
            
            if ($pdfPath && file_exists($pdfPath)) {
                $this->printBarcodeLabels($pdfPath, $printerConfig);
            } else {
                throw new Exception('Failed to generate barcode PDF');
            }

        } catch (Exception $e) {
            logger()->error('Barcode printing failed: ' . $e->getMessage());
            
            Notification::make()
                ->title('Printing failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function generateBarcodePdf()
    {
        try {
            // Ensure directory exists
            $labelsDir = public_path('labels');
            if (!file_exists($labelsDir)) {
                mkdir($labelsDir, 0755, true);
            }

            // Prepare data using same logic as preview
            $labelData = $this->prepareLabelData();
            $labels = $this->generateLabelsArray();

            // Calculate PDF page size to fit labels perfectly
            $totalLabels = count($labels);
            $labelsPerRow = max(1, min(4, $totalLabels)); // Max 4 labels per row
            $rows = ceil($totalLabels / $labelsPerRow);
            
            // Calculate page dimensions (add padding)
            $pageWidth = ($labelData['width_mm'] * $labelsPerRow) + (5 * ($labelsPerRow + 1)); // 5mm padding between labels
            $pageHeight = ($labelData['height_mm'] * $rows) + (5 * ($rows + 1)); // 5mm padding between rows
            
            // Minimum page size
            $pageWidth = max(50, $pageWidth);
            $pageHeight = max(30, $pageHeight);

            // Generate PDF with exact dimensions
            $pdf = Pdf::view('pdf.barcode-labels', [
                    'labels' => $labels,
                    'labelData' => $labelData
                ])
                ->paperSize($pageWidth, $pageHeight, 'mm')
                ->margins(0, 0, 0, 0); // No margins for exact sizing

            // Save PDF
            $filename = 'barcode-labels-' . time() . '.pdf';
            $pdfPath = public_path("labels/{$filename}");
            $pdf->save($pdfPath);

            return $pdfPath;

        } catch (Exception $e) {
            logger()->error('Error generating barcode PDF: ' . $e->getMessage());
            return null;
        }
    }

    private function prepareLabelData()
    {
        // Use EXACT same logic as your current preview
        $widthMm = isset($this->data['width_mm']) && !empty($this->data['width_mm']) && is_numeric($this->data['width_mm']) ? (float)$this->data['width_mm'] : 50;
        $heightMm = isset($this->data['height_mm']) && !empty($this->data['height_mm']) && is_numeric($this->data['height_mm']) ? (float)$this->data['height_mm'] : 30;
        
        $barcodeWidth = isset($this->data['barcode_width']) && !empty($this->data['barcode_width']) && is_numeric($this->data['barcode_width']) ? (int)$this->data['barcode_width'] : 2;
        $barcodeHeight = isset($this->data['barcode_height']) && !empty($this->data['barcode_height']) && is_numeric($this->data['barcode_height']) ? (int)$this->data['barcode_height'] : 43;
        
        return [
            'width_mm' => $widthMm,
            'height_mm' => $heightMm,
            'barcode_width' => $barcodeWidth,
            'barcode_height' => $barcodeHeight,
            'total' => $this->data['total'] ?? 1,
            'type' => $this->data['type']
        ];
    }

    private function generateLabelsArray()
    {
        $labels = [];
        $sku = $this->variant?->sku ?? $this->product->sku;
        $name = $this->variant?->name ?? $this->product->name;
        $price = $this->variant?->mmk_price ?? $this->product->mmk_price;
        
        for ($i = 0; $i < ($this->data['total'] ?? 1); $i++) {
            $barcodeHtml = $this->generateBarcodeHtmlForPdf($sku);
            
            $labels[] = [
                'sku' => $sku,
                'name' => $name,
                'price' => $price,
                'barcode_html' => $barcodeHtml
            ];
        }
        
        return $labels;
    }

    private function generateBarcodeHtmlForPdf($sku)
    {
        try {
            // Use EXACT same logic as your current preview
            $barcodeWidth = isset($this->data['barcode_width']) && !empty($this->data['barcode_width']) && is_numeric($this->data['barcode_width']) ? (int)$this->data['barcode_width'] : 2;
            $barcodeHeight = isset($this->data['barcode_height']) && !empty($this->data['barcode_height']) && is_numeric($this->data['barcode_height']) ? (int)$this->data['barcode_height'] : 43;
            
            if ($this->data['type'] === 'QRCODE') {
                // For QR codes, use barcode width as size multiplier
                return (new \Milon\Barcode\DNS2D())->getBarcodeHTML($sku, 'QRCODE', $barcodeWidth, $barcodeWidth);
            } else {
                // Use custom barcode width and height for regular barcodes
                return (new \Milon\Barcode\DNS1D())->getBarcodeHTML($sku, $this->data['type'], $barcodeWidth, $barcodeHeight);
            }
        } catch (\Exception $e) {
            return '<span style="color: red; font-size: 8px;">Barcode Error</span>';
        }
    }

    private function printBarcodeLabels($pdfPath, $printerConfig)
    {
        try {
            // Convert PDF to PNG (reuse POS logic)
            $imagePath = $this->convertPdfToImage($pdfPath);
            
            if ($imagePath && file_exists($imagePath)) {
                // Create printer connector
                $connector = $this->createPrinterConnector($printerConfig);
                if (!$connector) {
                    throw new Exception('Could not create printer connector');
                }

                $printer = new \Mike42\Escpos\Printer($connector);

                // Load and print the image
                $img = \Mike42\Escpos\EscposImage::load($imagePath);
                $printer->bitImageColumnFormat($img);
                
                usleep(500000); // 0.5 seconds
                
                // Ensure proper cutting
                $printer->text("\n");
                usleep(300000);
                $printer->initialize();
                $printer->feed(1);
                $printer->cut(\Mike42\Escpos\Printer::CUT_FULL);
                
                usleep(200000);
                $printer->close();

                // Clean up files
                // if (file_exists($imagePath)) unlink($imagePath);
                if (file_exists($pdfPath)) unlink($pdfPath);

                Notification::make()
                    ->title('Labels printed successfully')
                    ->body("Printed {$this->data['total']} labels to: {$printerConfig->name}")
                    ->success()
                    ->send();
            } else {
                throw new Exception('PDF to image conversion failed');
            }

        } catch (Exception $e) {
            throw new Exception('Printing failed: ' . $e->getMessage());
        }
    }

    private function convertPdfToImage($pdfPath)
    {
        try {
            if (!file_exists($pdfPath)) {
                return null;
            }

            // Set Ghostscript path for non-production environments
            if(env('APP_ENV') !== 'production'){
                // Set Ghostscript path for ImageMagick
                putenv('PATH=/opt/homebrew/bin:' . getenv('PATH'));
                
                // Also try setting GS_PROG for ImageMagick
                putenv('GS_PROG=/opt/homebrew/bin/gs');
            }

            // Convert PDF to image using Spatie package
            $imagePath = storage_path("app/temp-barcode-labels-" . time() . ".png");
            
            $pdfToImage = new \Spatie\PdfToImage\Pdf($pdfPath);
            $pdfToImage
                ->format(OutputFormat::Png)
                ->resolution(203)
                ->quality(30)
                ->save($imagePath);

            return $imagePath;

        } catch (Exception $e) {
            logger()->error('PDF to image conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    private function createPrinterConnector($printerConfig)
    {
        try {
            return match ($printerConfig->connection_type) {
                'network' => new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
                    $printerConfig->ip_address,
                    $printerConfig->port
                ),
                'usb' => new \Mike42\Escpos\PrintConnectors\FilePrintConnector($printerConfig->usb_path),
                default => throw new Exception('Unsupported printer connection type'),
            };
        } catch (Exception $e) {
            logger()->error('Error creating printer connector: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.barcode');
    }
}
