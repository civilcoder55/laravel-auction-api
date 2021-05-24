<?php

/**
 * @OA\Schema(
 *      title="Store Auction request",
 *      description="Store Auction request body data",
 *      type="object",
 *      required={"title"}
 * )
 */

class StoreAuctionRequest
{
    /**
     * @OA\Property(
     *      title="title",
     *      description="Title of the new Auction",
     *      example="used ps4 controller"
     * )
     *
     * @var string
     */
    public $title;
}
