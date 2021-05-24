<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'owner' => $this->user->email,
            'status' => $this->status,
            'bid' => $this->bid,
            'ending_at' => $this->ending_at,
            'highest_bidder' => $this->bidder ? $this->bidder->email : 'None',
            'images' => AuctionImagesResource::collection($this->images),
        ];
    }
}
