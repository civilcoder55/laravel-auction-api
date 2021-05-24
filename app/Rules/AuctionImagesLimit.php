<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AuctionImagesLimit implements Rule
{

    public function passes($attribute, $value)
    {
        if (5 - request()->route('auction')->images->count() < count($value)) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return 'Auction can have only 5 images';
    }
}
