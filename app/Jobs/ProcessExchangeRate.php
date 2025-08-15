<?php

namespace App\Jobs;

use App\Lib\ExchangeRate as ExchangeRateService;
use App\Models\Admin\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessExchangeRate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rates = (new ExchangeRateService)->getUSDExchangeRates();
        foreach ($rates as $key => $value) {
            $rate = ExchangeRate::where('currency_code', $key)->first();

            if ($rate && $rate->automatic_update) {
                $rate->update([
                    'rate' => $value
                ]);
            }
        }
    }
}
