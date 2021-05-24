<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class TokenResource extends JsonResource
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
            'name' => $this->name,
            'last_used_at' => $this->last_used_at ?: 'Never Used',
            'expiration' => Date::parse($this->created_at)->addMinutes(config('sanctum.expiration')),
        ];
    }
}
