<?php

namespace Database\Seeders\Fakes;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactMessage::factory(10)->create();
        ContactMessage::factory(10)->requiresConfirmation()->create();
        ContactMessage::factory(10)->requiresConfirmation()->confirmed()->create();
    }
}
