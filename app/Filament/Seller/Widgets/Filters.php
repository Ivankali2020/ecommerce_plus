<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\CheckFilamentSellerUser;
use App\Models\Admin;
use App\Models\Seller;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class Filters extends Widget implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.seller.widgets.filters';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -1;

    protected static ?string $pollingInterval = null;

    public $admin_id = null;
    public $year = null;
    public $month = null;

    public function mount(): void
    {
        // $this->year = Carbon::now()->format('Y');
        // $this->month = Carbon::now()->format('F');
    }

    public function applyFilters(): void
    {
        $this->dispatch('updateAdminId', $this->admin_id);
        $this->dispatch('updateYear', $this->year);
        $this->dispatch('updateMonth', $this->month);
    }

    public function resetFilters(): void
    {
        $this->admin_id = null;
        $this->year = Carbon::now()->format('Y');
        $this->month = null;
        
        // Dispatch events to update other widgets
        $this->dispatch('updateAdminId', $this->admin_id);
        $this->dispatch('updateYear', $this->year);
        $this->dispatch('updateMonth', $this->month);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    // ->compact()
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('admin_id')
                                ->label(__('filament/widgets/filters.admin_label'))
                                ->options(Seller::where('id',CheckFilamentSellerUser::getSellerId())->orWhere('parent_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id')->toArray())
                                ->searchable()->preload(),
                            
                            Select::make('year')
                                ->label(__('filament/widgets/filters.year_label'))
                                ->options($this->getYearOptions())->searchable()->preload()
                                ->default(Carbon::now()->format('Y')),
                                
                            Select::make('month')
                                ->label(__('filament/widgets/filters.month_label'))
                                ->searchable()->preload()
                                ->options($this->getMonthOptions()),
                        ]),
                    ])
                    ->extraAttributes(['class' => 'mb-0'])
                    ->headerActions([
                        Action::make('apply')->size('sm')
                            ->label(__('filament/widgets/filters.apply_button'))
                            ->color('primary')
                            ->action('applyFilters')
                            ->icon('heroicon-o-funnel'),
                        
                        Action::make('reset')
                            ->outlined()->size('sm')
                            ->label(__('filament/widgets/filters.reset_button'))
                            ->color('danger')
                            ->action('resetFilters')
                            ->icon('heroicon-o-x-mark'),
                    ]),
            ]);
    }

    protected function getYearOptions(): array
    {
        $years = [];
        $currentYear = Carbon::now()->format('Y');
        
        for ($i = $currentYear - 2; $i <= $currentYear + 5; $i++) {
            $years[$i] = $i;
        }
        
        return $years;
    }

    protected function getMonthOptions(): array
    {
        $months = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate(null, $i, 1)->format('F');
        }
        
        return $months;
    }
}
