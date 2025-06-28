<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LaporanType;
use App\Models\Information;
use App\Models\UserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        
        // Seed the test database
        $this->seed([
            \Database\Seeders\LaporanTypeSeeder::class,
            \Database\Seeders\InformationSeeder::class,
        ]);
    }

    public function test_full_api_flow()
    {
        // Step 1: Register a new user
        $this->info('Step 1: Registering new user...');
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone_number' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);
        
        if ($response->status() !== 201) {
            $this->fail('User registration failed: ' . $response->content());
        }
        
        $this->assertEquals(201, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->assertArrayHasKey('user', $response->json('data'));
        $this->assertArrayHasKey('token', $response->json('data'));
        
        // Verify the user gets 'warga' role by default
        $userData = $response->json('data.user');
        $this->assertEquals('warga', $userData['role'], 'User should get warga role by default');
        
        $token = $response->json('data.token');
        $this->info('User registered successfully with role: ' . $userData['role'] . '. Token: ' . substr($token, 0, 20) . '...');

        // Step 2: Login with the registered user
        $this->info('Step 2: Logging in...');
        $loginData = [
            'phone_number' => '081234567890',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $loginData);
        
        if ($response->status() !== 200) {
            $this->fail('Login failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->assertArrayHasKey('token', $response->json('data'));
        
        $token = $response->json('data.token');
        $this->info('Login successful. Token: ' . substr($token, 0, 20) . '...');

        // Step 3: Get Laporan Types
        $this->info('Step 3: Getting laporan types...');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/laporan-types');
        
        if ($response->status() !== 200) {
            $this->fail('Get laporan types failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('Laporan types retrieved successfully. Count: ' . count($response->json('data')));

        // Step 4: Get Information
        $this->info('Step 4: Getting information...');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/information');
        
        if ($response->status() !== 200) {
            $this->fail('Get information failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('Information retrieved successfully. Count: ' . count($response->json('data')));

        // Step 5: Create User Request
        $this->info('Step 5: Creating user request...');
        $laporanTypes = LaporanType::all();
        if ($laporanTypes->isEmpty()) {
            $this->fail('No laporan types available for testing');
        }
        
        $laporanType = $laporanTypes->first();
        $file = UploadedFile::fake()->create('document.pdf', 100);
        
        $requestData = [
            'title' => 'Test Request',
            'description' => 'This is a test request',
            'type' => 'permintaan',
            'laporan_type_id' => $laporanType->id,
            'lampiran' => [ $file ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/requests', $requestData);
        
        if ($response->status() !== 201) {
            $this->fail('Create user request failed: ' . $response->content());
        }
        
        $this->assertEquals(201, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('User request created successfully. ID: ' . $response->json('data.id'));

        // Step 6: Get User Requests
        $this->info('Step 6: Getting user requests...');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/requests');
        
        if ($response->status() !== 200) {
            $this->fail('Get user requests failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('User requests retrieved successfully. Count: ' . count($response->json('data')));

        // Step 7: Get Profile
        $this->info('Step 7: Getting user profile...');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/profile');
        
        if ($response->status() !== 200) {
            $this->fail('Get profile failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('Profile retrieved successfully. Name: ' . $response->json('data.name'));

        // Step 8: Get Statistics
        $this->info('Step 8: Getting statistics...');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/requests/statistics');
        
        if ($response->status() !== 200) {
            $this->fail('Get statistics failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('Statistics retrieved successfully');

        // Step 9: Logout
        $this->info('Step 9: Logging out...');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/logout');
        
        if ($response->status() !== 200) {
            $this->fail('Logout failed: ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->info('Logout successful');

        $this->info('All API flow tests passed successfully!');
    }

    public function test_information_endpoint_specifically()
    {
        // Create a user and authenticate
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone_number' => '081234567891',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        // Test information endpoint specifically
        $this->info('Testing information endpoint specifically...');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/information');
        
        $this->info('Information endpoint response status: ' . $response->status());
        $this->info('Information endpoint response: ' . $response->content());
        
        if ($response->status() !== 200) {
            $this->fail('Information endpoint failed with status ' . $response->status() . ': ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
    }

    public function test_get_information_by_laporan_type()
    {
        // Create a user and authenticate
        $user = User::factory()->create([
            'email' => 'test-laporan-type@example.com',
            'phone_number' => '081234567893',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        // Get a laporan type to test with
        $laporanType = LaporanType::first();
        if (!$laporanType) {
            $this->fail('No laporan types available for testing');
        }

        $this->info('Testing get information by laporan type...');
        $this->info('Using laporan type ID: ' . $laporanType->id);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/information/laporan-type/' . $laporanType->id);
        
        $this->info('Get information by laporan type response status: ' . $response->status());
        $this->info('Get information by laporan type response: ' . $response->content());
        
        if ($response->status() !== 200) {
            $this->fail('Get information by laporan type failed with status ' . $response->status() . ': ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('Information by laporan type retrieved successfully. Count: ' . count($response->json('data')));
    }

    public function test_get_information_detail_by_id()
    {
        // Create a user and authenticate
        $user = User::factory()->create([
            'email' => 'test-detail@example.com',
            'phone_number' => '081234567894',
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        // Get an information record to test with
        $information = Information::first();
        if (!$information) {
            $this->fail('No information records available for testing');
        }

        $this->info('Testing get information detail by ID...');
        $this->info('Using information ID: ' . $information->id);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/information/' . $information->id);
        
        $this->info('Get information detail response status: ' . $response->status());
        $this->info('Get information detail response: ' . $response->content());
        
        if ($response->status() !== 200) {
            $this->fail('Get information detail failed with status ' . $response->status() . ': ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->info('Information detail retrieved successfully. Title: ' . $response->json('data.title'));
    }

    public function test_admin_information_access()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'phone_number' => '081234567892',
            'role' => 'admin',
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;

        $this->info('Testing information endpoint as admin...');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/information');
        
        $this->info('Admin information endpoint response status: ' . $response->status());
        $this->info('Admin information endpoint response: ' . $response->content());
        
        if ($response->status() !== 200) {
            $this->fail('Admin information endpoint failed with status ' . $response->status() . ': ' . $response->content());
        }
        
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('data', $response->json());
    }

    public function test_registration_assigns_warga_role_by_default()
    {
        $this->info('Testing that registration assigns warga role by default...');
        
        $userData = [
            'name' => 'Warga Test User',
            'email' => 'warga-test@example.com',
            'phone_number' => '081234567895',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);
        
        $this->assertEquals(201, $response->status());
        $this->assertArrayHasKey('data', $response->json());
        $this->assertArrayHasKey('user', $response->json('data'));
        
        $userData = $response->json('data.user');
        $this->assertEquals('warga', $userData['role'], 'Newly registered user should have warga role by default');
        
        $this->info('Registration test passed: User has role: ' . $userData['role']);
        
        // Also verify in database
        $user = User::where('email', 'warga-test@example.com')->first();
        $this->assertNotNull($user, 'User should be created in database');
        $this->assertEquals('warga', $user->role, 'User in database should have warga role');
    }

    protected function info($message)
    {
        fwrite(STDERR, "[INFO] " . $message . "\n");
    }
} 