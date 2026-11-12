<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetOwnerPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_command_stores_password_without_printing_it(): void
    {
        $password = 'a-unique-owner-password';

        $exitCode = Artisan::call('kafe:reset-owner', ['--password' => $password]);
        $owner = User::query()->where('email', 'owner@thekafe.com')->firstOrFail();

        $this->assertSame(0, $exitCode);
        $this->assertTrue(Hash::check($password, $owner->password));
        $this->assertSame('owner', $owner->admin_role);
        $this->assertStringNotContainsString($password, Artisan::output());
    }

    public function test_reset_command_rejects_short_passwords(): void
    {
        $this->artisan('kafe:reset-owner', ['--password' => 'short'])
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'owner@thekafe.com']);
    }
}
