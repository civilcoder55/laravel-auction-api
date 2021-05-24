<?php

namespace App\Jobs;

use App\Models\Auction;
use App\Notifications\AuctionNotify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;

class ProcessAuction implements ShouldQueue
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
        $auctionsToClose = Auction::where(['status' => 'OPEN', ['ending_at', '<=', Date::now()]])->get();
        foreach ($auctionsToClose as $auction) {
            $auction->update(['status' => 'CLOSED']);
            if ($auction->bid == 0.0) {
                $auction->user->notify(new AuctionNotify($auction, "Your Auction  [$auction->title] has been closed with no bids ."));
            } else {
                $auction->user->notify(new AuctionNotify($auction, "Your Auction [$auction->title] has been sold."));
                $auction->bidder->notify(new AuctionNotify($auction, "Your bid on [$auction->title] has won the Auction."));
            }
        }
    }
}
