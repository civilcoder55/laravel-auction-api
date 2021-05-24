<?php

/**
 * @OA\Schema(
 *      title="Login User request",
 *      description="Login User request body data",
 *      type="object",
 *      required={"email","password"}
 * )
 */

class LoginUserRequest
{

    /**
     * @OA\Property(
     *      title="email",
     *      description="Email of the User Account",
     *      format="email",
     *      example="john@test.com"
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *      title="password",
     *      description="Password of the User Account",
     *      format="password",
     *      example="password"
     * )
     *
     * @var string
     */
    public $password;

}
