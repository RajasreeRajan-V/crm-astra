<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lead;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Lead::create(['name' => 'hari', 'contact' => '1234567890', 'source' => 'Website']);
        Lead::create(['name' => 'smith', 'contact' => '0987654321', 'source' => 'Social Media']);
        Lead::create(['name' => 'kohli', 'contact' => '1231231234', 'source' => 'Email']);
        Lead::create(['name' => 'virat', 'contact' => '4564564567', 'source' => 'Referral']);
        Lead::create(['name' => 'steven', 'contact' => '7897897890', 'source' => 'Website']);
        Lead::create(['name' => 'steven', 'contact' => '7897697890', 'source' => 'Advertisement']);
        Lead::create(['name' => 'steven', 'contact' => '7897597890', 'source' => 'Advertisement']);
        Lead::create(['name' => 'steven', 'contact' => '7897497890', 'source' => 'Website']);
        Lead::create(['name' => 'steven', 'contact' => '7897397890', 'source' => 'Email']);
        Lead::create(['name' => 'steven', 'contact' => '7893897890', 'source' => 'Advertisement']);
        Lead::create(['name' => 'steven', 'contact' => '7895897890', 'source' => 'Email']);
        Lead::create(['name' => 'steven', 'contact' => '7897827890', 'source' => 'Social Media']);
        Lead::create(['name' => 'steven', 'contact' => '7897897490', 'source' => 'Referral']);
    }
}
