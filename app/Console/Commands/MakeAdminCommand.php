<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'user:make-admin {email}';
    protected $description = 'Make a user an admin';

    public function handle(): void
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found");
            return;
        }
        
        $user->update(['is_admin' => true]);
        
        $this->info("{$user->name} ({$email}) is now an admin!");
    }
}
