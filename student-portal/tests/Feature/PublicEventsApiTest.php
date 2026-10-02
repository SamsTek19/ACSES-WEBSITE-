<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicEventsApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['cors.allowed_origins' => ['http://localhost:5173']]);

        Schema::dropIfExists('events');
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('time_label', 80)->nullable();
            $table->string('category')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('memories_link')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('banner_alt')->nullable();
            $table->timestamps();
        });

        Schema::dropIfExists('suggestions');
        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('category');
            $table->string('subject', 160);
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->string('status', 40)->default('pending');
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('suggestions');

        parent::tearDown();
    }

    public function test_public_site_can_read_admin_managed_event_details(): void
    {
        $startAt = now()->addDays(4)->setTime(10, 0);
        $endAt = $startAt->copy()->addHours(2);

        Event::query()->create([
            'title' => 'Admin-created workshop',
            'description' => 'Workshop details',
            'location' => 'Main Auditorium',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'category' => 'Workshop',
            'cta_url' => 'https://example.com/register',
        ]);

        $pastStartAt = now()->subDays(4)->setTime(10, 0);
        Event::query()->create([
            'title' => 'Recent past event',
            'start_at' => $pastStartAt,
            'end_at' => $pastStartAt->copy()->addHours(2),
            'category' => 'Lecture',
            'memories_link' => 'https://example.com/event-memories',
        ]);

        Event::query()->create([
            'title' => 'Unscheduled symposium',
            'time_label' => '09:00 AM - 06:00 PM',
            'category' => 'Symposium',
        ]);

        $this->withHeaders(['Origin' => 'http://localhost:5173'])
            ->getJson('/api/public/events')
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=30, public, stale-while-revalidate=120')
            ->assertHeader('Vary', 'Origin')
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
            ->assertJsonCount(3)
            ->assertJsonPath('0.title', 'Admin-created workshop')
            ->assertJsonPath('0.category', 'Upcoming')
            ->assertJsonPath('0.eventType', 'Workshop')
            ->assertJsonPath('0.location', 'Main Auditorium')
            ->assertJsonPath('0.link', 'https://example.com/register')
            ->assertJsonPath('0.memoriesLink', null)
            ->assertJsonPath('0.time', '10:00 AM - 12:00 PM')
            ->assertJsonPath('1.title', 'Recent past event')
            ->assertJsonPath('1.category', 'Past')
            ->assertJsonPath('1.memoriesLink', 'https://example.com/event-memories')
            ->assertJsonPath('1.link', null)
            ->assertJsonPath('2.title', 'Unscheduled symposium')
            ->assertJsonPath('2.category', 'Unscheduled')
            ->assertJsonPath('2.date', 'To be announced')
            ->assertJsonPath('2.time', '09:00 AM - 06:00 PM');

        Event::query()->create([
            'title' => 'Newly published event',
            'start_at' => now()->addDay()->setTime(10, 0),
            'category' => 'Seminar',
        ]);

        $this->getJson('/api/public/events')
            ->assertOk()
            ->assertJsonCount(4)
            ->assertJsonPath('0.title', 'Newly published event');
    }

    public function test_public_site_contact_form_is_submitted_to_the_backend(): void
    {
        $this->withHeaders(['Origin' => 'http://localhost:5173'])
            ->postJson('/api/public/contact', [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'subject' => 'Admissions question',
                'message' => 'I want to know the next intake date.',
            ])
            ->assertCreated()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173')
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Thank you. Your message has been sent to the ACSES team.');

        $this->assertDatabaseHas('suggestions', [
            'sender_name' => 'Jane Doe',
            'sender_email' => 'jane@example.com',
            'subject' => 'Admissions question',
            'category' => 'general',
            'message' => 'I want to know the next intake date.',
        ]);
    }
}