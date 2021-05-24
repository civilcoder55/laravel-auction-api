<?php

/**
 * @OA\Schema(
 *      title="Auction Image Upload request",
 *      description="Auction Image Upload request body data",
 *      type="object",
 *      required={"images"}
 * )
 */

class AuctionImageUploadRequest
{
    /**
     * @OA\Property(
     *      title="Images",
     *      description="Auction images",
     *      @OA\Items(type="file")
     * )
     *
     * @var array
     */
    public $images;

}
