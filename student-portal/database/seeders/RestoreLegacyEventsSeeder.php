<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class RestoreLegacyEventsSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Biometric Registration For Continuing Students',
                'description' => 'Students are to register for courses for the upcoming semester.',
                'location' => 'ACSES Department Office',
                'start_at' => '2026-10-12 10:00:00',
                'end_at' => '2026-10-14 16:00:00',
                'banner_path' => 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774764/IMG_9040.jpg',
            ],
            [
                'title' => 'Freshers Orientation & Medical Examination Of Freshers',
                'description' => 'An orientation program for new students, and all first year students are to be introduced to the department, faculty, and student clubs.',
                'location' => 'UMaT Main Auditorium',
                'start_at' => '2026-10-12 09:00:00',
                'end_at' => '2026-10-23 17:00:00',
                'banner_path' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => 'Annual Hackathon 2026: AI for Good',
                'description' => 'A hackathon bringing together students to solve real-world problems using artificial intelligence and robotics.',
                'location' => 'Main Auditorium',
                'start_at' => null,
                'end_at' => null,
                'time_label' => '09:00 AM - 06:00 PM',
                'banner_path' => 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774758/IMG_9404.jpg',
            ],
            [
                'title' => 'Innovation & Research Symposium 2026',
                'description' => 'Ecpects facilitators, researchers, and students to present their research findings, innovative projects, and technological advancements in a symposium format.',
                'location' => 'To be announced',
                'start_at' => null,
                'end_at' => null,
                'time_label' => null,
                'banner_path' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => 'Annual Hackathon 2025: AI for Good',
                'description' => 'Last year\'s hackathon brought together students to solve real-world problems using artificial intelligence and robotics.',
                'location' => 'Main Auditorium',
                'start_at' => '2025-10-15 09:00:00',
                'end_at' => '2025-10-15 18:00:00',
                'memories_link' => 'https://drive.google.com/drive/u/0/mobile/folders/18-VTlVSNvyiaA4OnHj6w3jaH8iE8oHU0?usp=drive_link',
                'banner_path' => 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777230/IMG_9081.jpg',
            ],
            [
                'title' => 'ACSES Annual Dinner and Awards Night',
                'description' => 'Last year\'s annual dinner and awards night celebrated the achievements of students, faculty, and staff in the department.',
                'location' => 'Main Auditorium',
                'start_at' => '2026-10-15 09:00:00',
                'end_at' => '2026-10-15 18:00:00',
                'memories_link' => 'https://bigmethphotography01.pixieset.com/acsesredcarpet/',
                'banner_path' => 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777046/IMG_9123.jpg',
            ],
            [
                'title' => 'Kakalika Freshers Akwaaba Night',
                'description' => 'Freshers Akwaaba Night is an annual event organized by the department to welcome new students and introduce them to the department, faculty, and student clubs.',
                'location' => 'Main Auditorium',
                'start_at' => '2026-10-15 09:00:00',
                'end_at' => '2026-10-15 18:00:00',
                'memories_link' => 'https://bigmethphotography01.pixieset.com/kakalikafreshersakwaaba/',
                'banner_path' => 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789776611/IMG_9253.jpg',
            ],
        ];

        foreach ($events as $event) {
            Event::query()->firstOrCreate(['title' => $event['title']], $event);
        }
    }
}
