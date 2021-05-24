<?php

/**
 * @OA\Schema(
 *     title="AuctionResource",
 *     description="Auction resource",
 *     @OA\Xml(
 *         name="AuctionResource"
 *     )
 * )
 */
class AuctionResource
{
    /**
     * @OA\Property(
     *     title="Data",
     *     description="Data wrapper"
     * )
     *
     * @var \App\Virtual\Models\Auction[]
     */
    private $data;
}
