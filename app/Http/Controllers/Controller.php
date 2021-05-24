<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Laravel AuctionAPI Demo Documentation",
     *      description="AuctionAPI Demo Project",
     *      @OA\Contact(
     *          email=""
     *      ),
     *      @OA\License(
     *          name="",
     *          url=""
     *      )
     * )
     *  @OA\SecurityScheme(
     *   securityScheme="Bearer",
     *   type="apiKey",
     *   in="header",
     *   name="Authorization"
     * )
     *
     *
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="local API Server"
     * )

     *
     *
     */
}
