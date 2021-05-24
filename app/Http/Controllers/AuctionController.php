<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuctionImageUploadRequest;
use App\Http\Resources\AuctionCollection;
use App\Http\Resources\AuctionResource;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class AuctionController extends Controller
{
    /**
     * @OA\Get(
     *      path="/auctions",
     *      operationId="getAuctionsList",
     *      tags={"Auctions"},
     *      summary="Get list of auctions",
     *      description="Returns list of auctions",
     *      security={{ "Bearer":{} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/AuctionResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        return new AuctionCollection(Auction::paginate(10));
    }

    /**
     * @OA\Get(
     *      path="/auction/{id}",
     *      operationId="getAuctionById",
     *      tags={"Auctions"},
     *      summary="Get Auction information",
     *      description="Returns Auction data",
     *      security={{ "Bearer":{} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Auction id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Auction")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function show(Auction $auction)
    {
        return response()->json(new AuctionResource($auction));
    }

    /**
     * @OA\Put(
     *      path="/auction/{id}",
     *      operationId="bidAuction",
     *      tags={"Auctions"},
     *      summary="bid on existing Auction",
     *      description="Returns updated auction data",
     *      security={{ "Bearer":{} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Auction id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BidAuctionRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Auction")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(Auction $auction, Request $request)
    {
        $data = $request->validate([
            'bid' => 'required|numeric|between:0,99999.9',
        ]);

        if ($auction->status == 'CLOSED') {
            return response()->json(['message' => "Auction Closed"], 400);
        }

        if (auth()->user()->email == $auction->user->email) {
            return response()->json(['message' => "you can't bid on your own auction"], 400);
        }
        if ($auction->bidder and auth()->user()->email == $auction->bidder->email) {
            return response()->json(['message' => "you are already highest bidder"], 400);
        }
        if ($auction->bid >= $request->bid) {
            return response()->json(['message' => "your bid is lower than highest"], 400);
        }
        $auction->update(['bid' => $data['bid'], 'bidder_id' => auth()->user()->id]);
        return response()->json(new AuctionResource($auction->fresh()));
    }

    /**
     * @OA\Post(
     *      path="/auction",
     *      operationId="storeAuction",
     *      tags={"Auctions"},
     *      summary="Store new auction",
     *      description="Returns Auction data",
     *      security={{ "Bearer":{} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreAuctionRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Auction")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $auction = auth()->user()->auctions()->create(['title' => $data['title'], 'ending_at' => Date::now()->addDay()])->fresh();
        return response()->json(new AuctionResource($auction), 201);
    }

    /**
     * @OA\Post(
     *      path="/auction/{id}/upload",
     *      operationId="uploadAuctionImage",
     *      tags={"Auctions"},
     *      summary="upload images on existing Auction",
     *      description="Returns updated auction data",
     *      security={{ "Bearer":{} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Auction id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *           @OA\MediaType(mediaType="multipart/form-data",@OA\Schema(ref="#/components/schemas/AuctionImageUploadRequest")),
     *
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Auction")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data"
     *      )
     * )
     */
    public function upload(AuctionImageUploadRequest $request, Auction $auction)
    {

        foreach ($request->images as $image) {
            $path = $image->store('public/images');
            $auction->images()->create(['path' => $path]);
        }

        return response()->json(new AuctionResource($auction->fresh()), 201);
    }

}
