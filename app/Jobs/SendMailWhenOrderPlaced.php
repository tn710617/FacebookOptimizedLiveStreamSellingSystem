<?php

namespace App\Jobs;

use App\Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMailWhenOrderPlaced implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $job;
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order, $FB_email, $Local_email)
    {
        $this->order = $order;
        $this->FB_email = $FB_email;
        $this->Local_email = $Local_email;
        $this->job = Helpers::mailWhenOrderPlaced($order, $FB_email, $Local_email);
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
