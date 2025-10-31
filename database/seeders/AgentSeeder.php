<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Agent::create(['name' => 'shabin', 'email' => 'shabinmohd369@gmail.com', 'password' => Hash::make('12345678')]);
        Agent::create(['name' => 'vikram', 'email' => 'agent2@example.com', 'password' => Hash::make('password2')]);
        Agent::create(['name' => 'leo', 'email' => 'agent3@example.com', 'password' => Hash::make('password3')]);
        Agent::create(['name' => 'tina', 'email' => 'agent4@example.com', 'password' => Hash::make('password4')]);
        Agent::create(['name' => 'vinod', 'email' => 'agent5@example.com', 'password' => Hash::make('password5')]);
    }
}
