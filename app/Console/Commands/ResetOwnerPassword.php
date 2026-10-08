<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetOwnerPassword extends Command
{
    protected $signature = 'kafe:reset-owner {--password=password : New password} {--email=owner@thekafe.com : Owner email}';

    protected $description = 'Create or reset the café owner admin account';

    public function handle(): int
    {
        $email = $this->option('email');
        $plain = $this->option('password');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Cafe Owner',
                'password' => Hash::make($plain),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->info("Owner ready: {$user->email}");
        $this->line('Password: '.$plain);

        return self::SUCCESS;
    }
}
