<?php

namespace App\Notifications;

use App\Mail\AuctionClosedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AuctionNotify extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $auction;
    public $head;
    public function __construct($auction, $head)
    {
        $this->auction = $auction;
        $this->head = $head;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new AuctionClosedMail($this->auction, $notifiable->name, $this->head))->to($notifiable->email);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
