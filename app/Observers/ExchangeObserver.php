<?php

namespace App\Observers;

use App\Models\Exchange;
use App\Models\ExchangeHistory;

class ExchangeObserver
{
    public function updated(Exchange $exchange)
    {
        $history = new ExchangeHistory;
        $history->exchange_id = $exchange->id;
        $history->rate = $exchange->rate;
        $history->save();
    }
}
