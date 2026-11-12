<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetOwnerPassword extends Command
{
    protected $signature = 'kafe:reset-owner {--password= : New password} {--email=owner@thekafe.com : Owner email}';

    protected $description = 'Create or reset the café owner admin account';

    public function handle(): int
    {
        $email = $this->option('email');
        $plain = $this->option('password') ?: $this->secret('New owner password');

        if (! is_string($plain) || mb_strlen($plain) < 12) {
            $this->error('Provide a unique owner password of at least 12 characters.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Cafe Owner',
                'password' => Hash::make($plain),
                'is_admin' => true,
                'admin_role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        $this->info("Owner ready: {$user->email}");

        return self::SUCCESS;
    }
}
