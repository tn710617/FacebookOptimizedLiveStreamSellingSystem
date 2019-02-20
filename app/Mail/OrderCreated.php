<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreated extends Mailable {

    use Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orders.created')
            ->with([
                'buyer'            => $this->order->user->name,
                'order'            => $this->order->name,
                'item_name'        => $this->order->item_name,
                'item_description' => $this->order->item_description,
                'quantity'         => $this->order->quantity,
                'total_amount'     => $this->order->total_amount,
                'unit_price'       => $this->order->unit_price,
                'expiry_time'      => Carbon::parse($this->order->expiry_time)->addHours(8),
            ]);
    }
}
