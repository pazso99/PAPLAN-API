<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->ask('Username');
        $password = $this->secret('Password');
        $passwordConfirm = $this->secret('Confirm password');

        if (!$username || !$password || !$passwordConfirm) {
            $this->error('Invalid credentials');
            return;
        }
        if ($password !== $passwordConfirm) {
            $this->error('Invalid password confirmation');
            return;
        }

        User::create([
            'name' => $username,
            'password' => Hash::make($password)
        ]);

        $this->info('Successfully created user.');
    }
}
