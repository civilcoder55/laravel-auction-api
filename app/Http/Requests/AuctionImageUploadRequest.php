<?php

namespace App\Http\Requests;

use App\Rules\AuctionImagesLimit;
use Illuminate\Foundation\Http\FormRequest;

class AuctionImageUploadRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize()
    {
        return $this->user()->id == $this->route('auction')->user_id;
    }

    public function rules()
    {
        return [
            'images' => ['bail', 'required', 'array', 'min:1', 'max:5', new AuctionImagesLimit],
            'images.*' => ['bail', 'required', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
        ];
    }
}
