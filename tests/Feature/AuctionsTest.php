<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuctionsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function can_register()
    {
        $this->postJson('/api/register', [
            'name' => $name = 'john doe',
            'email' => $email = 'jogndoe@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertStatus(201)
            ->assertJsonPath('data.name', $name)
            ->assertJsonPath('data.email', $email)
            ->assertJsonStructure([
                'data' => ['name', 'email', 'created_at'],
                'token' => ['token', 'expiration'],
            ]);
        $this->assertDatabaseHas('users', ['name' => $name, 'email' => $email]);

    }

    /** @test */
    public function valid_user_can_login()
    {
        $user = User::factory()->create();
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])->assertStatus(200)->assertJsonPath('data.email', $user->email)->assertJsonStructure(
            [
                'data' => ['name', 'email', 'created_at'],
                'token' => ['token', 'expiration'],
            ]
        );
    }

    /** @test */
    public function invalid_user_cant_login()
    {
        $this->postJson('/api/login', ['email' => 'someoneemail@test.com', 'password' => 'wrongpassword'])->assertStatus(401);
        $this->assertGuest();
    }

    /** @test */
    public function incorrect_user_details_cant_login()
    {
        $user = User::factory()->create();
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrongpassword'])->assertStatus(401);
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        Sanctum::actingAs(User::factory()->make());
        $this->postJson('/api/logout')->assertStatus(200);
    }

    /** @test */
    public function guest_cant_logout()
    {
        $this->postJson('/api/logout')->assertStatus(401);
    }

    /** @test */
    public function guest_cant_access_guarded_endpoints()
    {
        $this->postJson('/api/logout')->assertStatus(401);
        $this->postJson('/api/auction', ['title' => 'used new car'])->assertStatus(401);
        $this->getJson("/api/auction/-1")->assertStatus(401);
        $this->getJson("/api/auctions")->assertStatus(401);
    }

    /** @test */
    public function user_can_get_info()
    {
        Sanctum::actingAs($user = User::factory()->make());
        $this->getJson("/api/me")->assertStatus(200)->assertJsonStructure([
            'data' => ['name', 'email', 'created_at'],
            'issuedTokens' => ['*' => ['id', 'name', 'last_used_at', 'expiration']],
        ])->assertJsonPath('data.email', $user->email);
    }

    /** @test */
    public function can_get_all_auction_paginated()
    {
        Sanctum::actingAs(User::factory()->create());
        for ($i = 0; $i < 3; $i++) {
            Auction::factory()->create()->fresh();
        }
        $this->getJson("/api/auctions")->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'owner', 'status', 'bid', 'ending_at', 'highest_bidder'],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ["current_page", "from", "last_page", "path", "per_page", "to", "total"],
        ]);

    }

    /** @test */
    public function can_create_auction()
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/auction', ['title' => $title = 'used new car'])->assertStatus(201)->assertJson($auction = [
            'title' => $title,
            'status' => 'OPEN',
            'bid' => 0.0,
        ]);
        $this->assertDatabaseHas('auctions', $auction);
    }

    /** @test */
    public function cant_view_non_existing_auction()
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/auction/-1")->assertStatus(404);
    }

    /** @test */
    public function can_view_existing_auction()
    {
        $auction = Auction::factory()->create()->fresh();
        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/auction/$auction->id")->assertStatus(200)->assertExactJson([
            'id' => $auction->id,
            'title' => $auction->title,
            'owner' => $auction->user->email,
            'status' => $auction->status,
            'bid' => $auction->bid,
            'ending_at' => $auction->ending_at,
            'highest_bidder' => 'None',
        ]);
    }

    /** @test */
    public function can_place_bid()
    {
        $bidder = User::factory()->create();
        $auction = Auction::factory()->create()->fresh();
        Sanctum::actingAs($bidder);
        $this->patchJson("/api/auction/$auction->id/bid", ['bid' => 100])->assertStatus(200)->assertExactJson([
            'id' => $auction->id,
            'title' => $auction->title,
            'owner' => $auction->user->email,
            'status' => $auction->status,
            'bid' => "100",
            'ending_at' => $auction->ending_at,
            'highest_bidder' => $bidder->email,
        ]);
    }

    /** @test */
    public function owner_cant_place_bid()
    {
        $auction = Auction::factory()->create()->fresh();
        Sanctum::actingAs($auction->user);
        $this->patchJson("/api/auction/$auction->id/bid", ['bid' => 100])->assertStatus(400)->assertExactJson(['message' => "you can't bid on your own auction"]);
    }

    /** @test */
    public function highest_bidder_cant_place_bid_again()
    {
        $bidder = User::factory()->create();
        $auction = Auction::factory()->create()->fresh();
        Sanctum::actingAs($bidder);
        $this->patchJson("/api/auction/$auction->id/bid", ['bid' => 100])->assertStatus(200);
        $this->patchJson("/api/auction/$auction->id/bid", ['bid' => 100])->assertStatus(400)->assertExactJson(['message' => "you are already highest bidder"]);
    }

    /** @test */
    public function can_place_lower_bid()
    {
        $bidder = User::factory()->create();
        $auction = Auction::factory()->create(['bid' => 100])->fresh();
        Sanctum::actingAs($bidder);
        $this->patchJson("/api/auction/$auction->id/bid", ['bid' => 100])->assertStatus(400)->assertExactJson(['message' => "your bid is lower than highest"]);
    }

}
