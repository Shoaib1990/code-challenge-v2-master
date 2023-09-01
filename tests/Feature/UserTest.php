<?php

namespace Tests\Feature;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    
     /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus($response->status(), 200);
        
    }
    
    public function test_api_returns_users()
    {
        $user = User::factory()->create();  // Create users in the database
        $response = $this->getJson('/api/users');
        $response->assertStatus($response->status(), 200);
        $response->assertJson([$user->toArray()]);

    }

    
    public function test_get_user_by_id()
    {
        $user = User::factory()->create();

        // Fetch the user by ID
        $retrievedUser = User::find($user->id);

        // Perform assertions
        $this->assertInstanceOf(User::class, $retrievedUser);
        $this->assertEquals($user->id, $retrievedUser->id);

    }

    public function test_get_user_by_nickname()
    {
        // Create a user with a specific nickname
        $user = User::factory()->create(['nickname' => 'bond']);

        // Retrieve the user by nickname
        $foundUser = User::where('nickname', 'bond')->first();

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($user->nickname, $foundUser->nickname);
    }


     public function test_user_store()
     {
        $response = $this->call('POST', '/api/users/store', [
            'name' => 'Shoaib',
            'email' => 'shoaib@test.com',
            'nickname' => 'bond',
            'password' => 'hello#world12'
        ]);
        
        $response->assertStatus($response->status(), 200);
     }

     public function test_user_update()
     {
        $response = $this->call('PUT', '/api/users/2', [
            'name' => 'Shoaib',
            'email' => 'shoaib@test.com',
            'nickname' => 'bond20',
            'password' => 'hello#world12'
        ]);
        
        $response->assertStatus($response->status(), 200);
     }
}
