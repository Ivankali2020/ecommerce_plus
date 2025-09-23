<?php

namespace App\Exports;

use App\Helper\OrderStatus;
use App\Models\Expanse;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EnhancedOrderProductExport implements FromCollection, ShouldAutoSize, WithColumnWidths, WithEvents, WithHeadings
{
    protected Collection $data;

    protected array $filters;

    protected string $activeTab;

    protected $totalExpenses;

    protected $fromDate;

    protected $toDate;

    protected $orderTotals;

    public function __construct(Collection $data, array $filters = [], string $activeTab = 'all')
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->activeTab = $activeTab;

        // Calculate total expenses within the date range
        $expenseQuery = Expanse::query();

        if (! empty($this->filters['created_at']['created_from'])) {
            $this->fromDate = Carbon::parse($this->filters['created_at']['created_from']);
            $expenseQuery->where('created_at', '>=', $this->fromDate);
        }

        if (! empty($this->filters['created_at']['created_until'])) {
            $this->toDate = Carbon::parse($this->filters['created_at']['created_until']);
            $expenseQuery->where('created_at', '<=', $this->toDate);
        }

        $this->totalExpenses = $expenseQuery->sum('amount');

        // Calculate order totals (frozen_fee, cash_on_delivery_fees, delivery_fees)
        $orderQuery = Order::query();

        // Apply date filters
        if ($this->fromDate) {
            $orderQuery->where('created_at', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $orderQuery->where('created_at', '<=', $this->toDate);
        }

        // Apply status filter based on active tab
        if ($this->activeTab !== 'all') {
            $statusMap = [
                'new' => OrderStatus::New,
                'processing' => OrderStatus::Processing,
                'shipping' => OrderStatus::Shipped,
                'delivered' => OrderStatus::Delivered,
                'cancel' => OrderStatus::Cancelled,
                'refunded' => OrderStatus::Refunded,
            ];

            if (isset($statusMap[$this->activeTab])) {
                $orderQuery->where('status', $statusMap[$this->activeTab]);
            }
        }

        $this->orderTotals = $orderQuery->selectRaw('
            SUM(delivery_fees) as total_delivery_fees
        ')->first();

    }

    public function collection()
    {
        // Add total row at the end
        $dataWithTotal = $this->data->toArray();

        // Calculate total price, buy price, profit, and quantity
        $totalPrice = $this->data->sum('Total Price');
        $totalQuantity = $this->data->sum('Total Quantity');
        $totalBuyPrice = $this->data->sum('Total Buy Price');
        $totalProfit = $this->data->sum('Total Profit');
        $totalRefunded = $this->data->sum('Total Refunded');

        // Add total row
        $dataWithTotal[] = [
            'Product Name' => 'TOTAL',
            'Total Quantity' => $totalQuantity,
            'Total Buy Price' => $totalBuyPrice,
            'Total Price' => $totalPrice,
            'Total Profit' => $totalProfit,
            'Total Refunded' => $totalRefunded,
        ];

        return collect($dataWithTotal);
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Total Quantity',
            'Total Buy Price',
            'Total Price',
            'Total Profit',
            'Total Refunded',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45, // Product Name - wider for product names
            'B' => 18, // Total Quantity - medium width
            'C' => 22, // Total Buy Price - wider for currency values
            'D' => 22, // Total Price - wider for currency values
            'E' => 22, // Total Profit - wider for currency values
            'F' => 22, // Total Refunded - wider for currency values
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Set row height for all rows
                for ($row = 1; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Set vertical alignment to middle for all cells
                $sheet->getStyle("A1:E{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Make header row bold with background color
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                $sheet->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');

                // Make total row bold with different background color
                $sheet->getStyle("A{$lastRow}:E{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}:E{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FCE5CD');

                // Format price columns (C, D, E, F) as ကျပ် currency
                $sheet->getStyle("C2:F{$lastRow}")->getNumberFormat()->setFormatCode('ks#,##0');

                // Add a date range information row
                $infoRow = $lastRow + 2; // Leave one blank row

                $dateRangeText = 'Report Period: ';
                if ($this->fromDate && $this->toDate) {
                    $dateRangeText .= $this->fromDate->format('Y-m-d').' to '.$this->toDate->format('Y-m-d');
                } elseif ($this->fromDate) {
                    $dateRangeText .= 'From '.$this->fromDate->format('Y-m-d');
                } elseif ($this->toDate) {
                    $dateRangeText .= 'Until '.$this->toDate->format('Y-m-d');
                } else {
                    $dateRangeText .= 'All time';
                }

                $sheet->setCellValue("A{$infoRow}", $dateRangeText);
                $sheet->mergeCells("A{$infoRow}:D{$infoRow}");
                $sheet->getStyle("A{$infoRow}")->getFont()->setBold(true);

                // Generate summary info
                $sheet->setCellValue('A'.($infoRow + 2), 'FINANCIAL SUMMARY');
                $sheet->mergeCells('A'.($infoRow + 2).':C'.($infoRow + 2));
                $sheet->getStyle('A'.($infoRow + 2))->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.($infoRow + 2))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 2).':C'.($infoRow + 2))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('B6D7A8'); // Light green header
                $sheet->getRowDimension($infoRow + 2)->setRowHeight(30); // Increased height for title

                // Set column widths for summary
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);

                // Add header row for categories
                $sheet->setCellValue('A'.($infoRow + 4), 'CATEGORY');
                $sheet->setCellValue('B'.($infoRow + 4), 'AMOUNT (ကျပ်)');
                $sheet->setCellValue('C'.($infoRow + 4), 'PERCENTAGE');
                $sheet->getStyle('A'.($infoRow + 4).':C'.($infoRow + 4))->getFont()->setBold(true);
                $sheet->getStyle('A'.($infoRow + 4).':C'.($infoRow + 4))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 4).':C'.($infoRow + 4))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D9D9D9'); // Light gray header
                $sheet->getRowDimension($infoRow + 4)->setRowHeight(28); // Increased height for header row

                // Get total revenue for percentage calculations
                $totalRevenue = $sheet->getCell("D{$lastRow}")->getValue();

                // Total Revenue row
                $sheet->setCellValue('A'.($infoRow + 5), 'Total Revenue');
                $sheet->setCellValue('B'.($infoRow + 5), $totalRevenue);
                $sheet->setCellValue('C'.($infoRow + 5), '100%');
                $sheet->getStyle('A'.($infoRow + 5).':C'.($infoRow + 5))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 5).':C'.($infoRow + 5))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E6F3EA'); // Very light green
                $sheet->getRowDimension($infoRow + 5)->setRowHeight(26); // Increased height for data rows

                // Total Buy Price (Cost of Goods) row
                $totalBuyPrice = $sheet->getCell("C{$lastRow}")->getValue();
                $buyPricePercentage = $totalRevenue > 0 ? ($totalBuyPrice / $totalRevenue) * 100 : 0;
                $sheet->setCellValue('A'.($infoRow + 6), 'Total Buy Price (Cost of Goods)');
                $sheet->setCellValue('B'.($infoRow + 6), $totalBuyPrice);
                $sheet->setCellValue('C'.($infoRow + 6), round($buyPricePercentage, 1).'%');
                $sheet->getStyle('A'.($infoRow + 6).':C'.($infoRow + 6))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 6).':C'.($infoRow + 6))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFF8DC'); // Light cream color for cost
                $sheet->getRowDimension($infoRow + 6)->setRowHeight(26);

                // Total Delivery Fees row
                $deliveryFees = $this->orderTotals->total_delivery_fees ?? 0;
                $deliveryFeesPercentage = $totalRevenue > 0 ? ($deliveryFees / $totalRevenue) * 100 : 0;
                $sheet->setCellValue('A'.($infoRow + 9), 'Total Delivery Fees');
                $sheet->setCellValue('B'.($infoRow + 9), $deliveryFees);
                $sheet->setCellValue('C'.($infoRow + 9), round($deliveryFeesPercentage, 1).'%');
                $sheet->getStyle('A'.($infoRow + 9).':C'.($infoRow + 9))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 9).':C'.($infoRow + 9))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFF2CC'); // Light yellow
                $sheet->getRowDimension($infoRow + 9)->setRowHeight(26);

                // Total Expenses row
                $expensePercentage = $totalRevenue > 0 ? ($this->totalExpenses / $totalRevenue) * 100 : 0;
                $sheet->setCellValue('A'.($infoRow + 10), 'Total Expenses');
                $sheet->setCellValue('B'.($infoRow + 10), $this->totalExpenses);
                $sheet->setCellValue('C'.($infoRow + 10), round($expensePercentage, 1).'%');
                $sheet->getStyle('A'.($infoRow + 10).':C'.($infoRow + 10))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 10).':C'.($infoRow + 10))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F9DDDD'); // Light red for expenses
                $sheet->getRowDimension($infoRow + 10)->setRowHeight(26);

                // Total Refuneded row
                $totalRefunded = $sheet->getCell("F{$lastRow}")->getValue();
                $refundedPercentage = $totalRevenue > 0 ? ($totalRefunded / $totalRevenue) * 100 : 0;
                $sheet->setCellValue('A'.($infoRow + 8), 'Total Refunded');
                $sheet->setCellValue('B'.($infoRow + 8), $totalRefunded);
                $sheet->setCellValue('C'.($infoRow + 8), round($refundedPercentage, 1).'%');
                $sheet->getStyle('A'.($infoRow + 8).':C'.($infoRow + 8))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 8).':C'.($infoRow + 8))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F9DDDD'); // Light red for refunded
                $sheet->getRowDimension($infoRow + 8)->setRowHeight(26);

                // Total Profit Before Expenses row
                $grossProfitValue = $sheet->getCell("E{$lastRow}")->getValue();
                $grossProfitPercentage = $totalRevenue > 0 ? ($grossProfitValue / $totalRevenue) * 100 : 0;
                $sheet->setCellValue('A'.($infoRow + 11), 'Gross Profit (Before Expenses)');
                $sheet->setCellValue('B'.($infoRow + 11), $grossProfitValue);
                $sheet->setCellValue('C'.($infoRow + 11), round($grossProfitPercentage, 1).'%');
                $sheet->getStyle('A'.($infoRow + 11).':C'.($infoRow + 11))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 11).':C'.($infoRow + 11))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E6F3EA'); // Very light green
                $sheet->getRowDimension($infoRow + 11)->setRowHeight(26);

                // Net Profit (After All Costs) row with distinct styling
                $totalAllCosts = $this->totalExpenses;
                $actualProfit = $grossProfitValue - ($totalAllCosts + $deliveryFees + $totalRefunded);
                $actualProfitPercentage = $totalRevenue > 0 ? ($actualProfit / $totalRevenue) * 100 : 0;
                $sheet->setCellValue('A'.($infoRow + 12), 'Net Profit (After All Costs)');
                $sheet->setCellValue('B'.($infoRow + 12), $actualProfit);
                $sheet->setCellValue('C'.($infoRow + 12), round($actualProfitPercentage, 1).'%');

                // Style for the actual profit row - make it stand out
                $sheet->getStyle('A'.($infoRow + 12).':C'.($infoRow + 12))->getFont()->setBold(true);
                $sheet->getStyle('A'.($infoRow + 12).':C'.($infoRow + 12))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A'.($infoRow + 12).':C'.($infoRow + 12))->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D5EAD8'); // Medium green for net profit
                $sheet->getRowDimension($infoRow + 12)->setRowHeight(26);

                // Add border to all cells in the summary table
                $summaryBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];

                $sheet->getStyle('A'.($infoRow + 4).':C'.($infoRow + 12))->applyFromArray($summaryBorder);

                // Format all cells in the summary table with currency format
                $sheet->getStyle('B'.($infoRow + 5).':B'.($infoRow + 12))->getNumberFormat()->setFormatCode('¥#,##0');

                // Add profit margin description
                $profitMarginText = 'Net Profit Margin: '.round($actualProfitPercentage, 1).'%';
                $sheet->setCellValue('A'.($infoRow + 14), $profitMarginText);
                $sheet->getStyle('A'.($infoRow + 14))->getFont()->setBold(true);
                $sheet->getRowDimension($infoRow + 14)->setRowHeight(28);

                // Generate report timestamp and information at the bottom
                $sheet->setCellValue('A'.($infoRow + 16), 'Report Generated: '.Carbon::now()->format('Y-m-d H:i:s'));
                $sheet->getRowDimension($infoRow + 16)->setRowHeight(22);

                $sheet->setCellValue('A'.($infoRow + 17), 'Financial Report');
                $sheet->getStyle('A'.($infoRow + 17))->getFont()->setItalic(true);
                $sheet->getStyle('A'.($infoRow + 17))->getFont()->setSize(12);
                $sheet->getRowDimension($infoRow + 17)->setRowHeight(22);
            },
        ];
    }
}
