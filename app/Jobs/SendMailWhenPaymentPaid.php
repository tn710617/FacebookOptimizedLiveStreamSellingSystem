<?php

namespace App\Jobs;

use App\Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMailWhenPaymentPaid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $job;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paymentServiceOrder, $orderRelations)
    {
        $this->job = Helpers::mailWhenPaid($paymentServiceOrder, $orderRelations);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return $this->job;
    }
}
