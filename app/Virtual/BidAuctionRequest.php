<?php

/**
 * @OA\Schema(
 *      title="Bid Auction request",
 *      description="Bid Auction request body data",
 *      type="object",
 *      required={"bid"}
 * )
 */

class BidAuctionRequest
{
    /**
     * @OA\Property(
     *      title="Bid",
     *      description="your bid on auction",
     *      example="50"
     * )
     *
     * @var integer
     */
    public $bid;

}
