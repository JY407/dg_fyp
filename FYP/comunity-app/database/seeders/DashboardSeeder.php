<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Visitor;
use App\Models\VisitorLocation;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create some Residents
        User::factory()->create([
            'name' => 'John Resident',
            'email' => 'john@example.com',
            'user_type' => 'tenant',
            'unit_number' => '01-01',
            'block' => 'A',
            'street' => 'Clementi Ave 1'
        ]);

        User::factory()->create([
            'name' => 'Jane Owner',
            'email' => 'jane@example.com',
            'user_type' => 'owner',
            'unit_number' => '02-05',
            'block' => 'B',
            'street' => 'Clementi Ave 1'
        ]);

        // 2. Create Announcements
        Announcement::create([
            'title' => 'Lift Maintenance Schedule',
            'content' => 'Lifts at Block A will be undergoing maintenance on 30th Jan from 10am to 2pm.',
            'published_at' => now(),
        ]);

        Announcement::create([
            'title' => 'Community BBQ Night',
            'content' => 'Join us for a BBQ night at the clubhouse this Saturday!',
            'published_at' => now()->subDays(2),
        ]);

        // 3. Create a Test Visitor
        $visitor = Visitor::create([
            'user_id' => 1, // distinct from admin
            'name' => 'Michael Visitor',
            'ic_number' => 'S1234567A',
            'vehicle_number' => 'SLA1234Z',
            'visit_purpose' => 'Delivery',
            'expected_arrival' => now(),
            'pass_code' => 'TESTPASS123',
            'status' => 'pending',
            'latitude' => 1.3521,
            'longitude' => 103.8198,
            'location_address' => 'Upper Thomson Road',
            'location_captured_at' => now(),
        ]);

        // Add a location history point
        VisitorLocation::create([
            'visitor_id' => $visitor->id,
            'latitude' => 1.3521,
            'longitude' => 103.8198,
        ]);
    }
}
