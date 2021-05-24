<?php
/**
 * @OA\Schema(
 *     title="Auction",
 *     description="Auction model",
 *     @OA\Xml(
 *         name="Auction"
 *     )
 * )
 */
class Auction
{

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var integer
     */
    private $id;

    /**
     * @OA\Property(
     *      title="Title",
     *      description="Title of the new Auction",
     *      example="used ps4 controller"
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @OA\Property(
     *      title="Owner",
     *      description="Auction owner email",
     *      example="owner@test.com"
     * )
     *
     * @var string
     */
    public $owner;

    /**
     * @OA\Property(
     *      title="Status",
     *      enum={"OPEN", "CLOSED"},
     *      description="Auction status",
     *      example="OPEN"
     * )
     *
     * @var string
     */
    public $status;

    /**
     * @OA\Property(
     *      title="Bid",
     *      description="Auction highest bid",
     *      example="50"
     * )
     *
     * @var integer
     */
    public $bid;

    /**
     * @OA\Property(
     *     title="Ending at",
     *     description="Auction Ending Date",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $ending_at;

    /**
     * @OA\Property(
     *      title="Highest Bidder",
     *      description="Auction highest bidder email",
     *      example="bidder@test.com"
     * )
     *
     * @var string
     */
    public $highest_bidder;

    /**
     * @OA\Property(
     *      title="Images",
     *      description="Auction images",
     *      @OA\Items(type="object",
     *      @OA\Property(property="id",description="Image ID",type="integer"),
     *      @OA\Property(property="url",description="Image URL",type="string"),
     * ),
     * example=""
     * )
     *
     * @var array
     */
    public $images;
}
