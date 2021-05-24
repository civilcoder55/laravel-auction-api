<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuctionClosedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data = [];

    public function __construct($auction, $name, $head)
    {
        $this->data['title'] = $auction->title;
        $this->data['bid'] = $auction->bid;
        $this->data['created_at'] = $auction->created_at;
        $this->data['ending_at'] = $auction->ending_at;
        $this->data['name'] = $name;
        $this->data['head'] = $head;
    }

    public function build()
    {
        return $this->markdown('AuctionClosed', $this->data);
    }
}
