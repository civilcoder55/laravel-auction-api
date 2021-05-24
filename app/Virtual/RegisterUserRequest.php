<?php

/**
 * @OA\Schema(
 *      title="Register User request",
 *      description="Register User request body data",
 *      type="object",
 *      required={"name","email","password","password_confirmation"}
 * )
 */

class RegisterUserRequest
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new User",
     *      example="john doe"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="email",
     *      description="Email of the new User Account",
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
     *      description="Password of the new User Account",
     *      format="password",
     *      example="password"
     * )
     *
     * @var string
     */
    public $password;

    /**
     * @OA\Property(
     *      title="password confirmation",
     *      description="Password confirmation of the new User Account",
     *      format="password",
     *      example="password"
     * )
     *
     * @var string
     */
    public $password_confirmation;
}
