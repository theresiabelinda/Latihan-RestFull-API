<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Unit Test Scenario
     */
    //Register:
    // 1. Berhasil Register
    // 2. Gagal Register
    // 3. Username Telah dipakai

    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'test',
        ])->assertStatus(201) #harapan
        ->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test",
            ]
        ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username field is required."
                    ],
                    'password' => [
                        "The password field is required."
                    ],
                    'name' => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterAlreadyExists()
    {
        $this->testRegisterSuccess(); #username: test
        $this->post('/api/users', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'test',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username already exists."
                    ]
                ]
            ]);
    }

    public function testLoginSuccess(){
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test",
                ]
            ]);

        $user = User::where('username', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginUsernameNotFound(){
        $this->post('/api/users/login', [
            'username' => 'test3',
            'password' => 'test3'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Username or password wrong."
                    ]
                ]
            ]);
    }

    public function testLoginUsernameEmpty(){
        $this->post('/api/users/login', [
            'username' => '',
            'password' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ]
                ]
            ]);
    }

    public function testLoginPasswordWrong()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'blabla'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Username or password wrong."
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test",
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                "error" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => 'blabla'
        ])->assertStatus(401)
            ->assertJson([
                "error" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'test')->first();
        $this->patch('/api/users/current',
            [
                'name' => 'pbe',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "pbe",
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->patch('/api/users/current',
            [
                'name' => 'pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|pbepbepbe|',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testLogoutFailed(){
        $this->seed(UserSeeder::class);
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'blabla'
        ])->assertStatus(401)
            ->assertJson([
                "error" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
}
