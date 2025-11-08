<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RemoveAdminCommand extends Command
{
    protected $signature = 'user:remove-admin {email}';
    protected $description = 'Remove admin role from a user';

    public function handle(): void
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found");
            return;
        }
        
        $user->update(['is_admin' => false]);
        
        $this->info("{$user->name} ({$email}) is no longer an admin!");
    }
}
