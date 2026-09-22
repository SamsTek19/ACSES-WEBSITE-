<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use App\Notifications\StudentEventCreatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminEventController extends Controller
{
    public function index(): View
    {
        $perPageOptions = [10, 25, 50, 100];
        $perPage = (int) request('per_page', 10);

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $events = Event::query()
            ->orderByDesc('start_at')
            ->paginate($perPage)
            ->appends([
                'per_page' => $perPage,
            ]);

        return view('dashboards.admin.events.index', [
            'title' => 'Manage events',
            'events' => $events,
            'perPageOptions' => $perPageOptions,
            'currentPerPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('dashboards.admin.events.create', [
            'title' => 'Create event',
            'event' => new Event(),
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::guard('admin')->id();

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');

            $extension = strtolower($image->getClientOriginalExtension() ?: 'jpg');
            $fileName = 'event-' . Str::uuid()->toString() . '.' . $extension;
            $directory = public_path('assets/images/events');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $image->move($directory, $fileName);
            $data['banner_path'] = 'assets/images/events/' . $fileName;
        }

        unset($data['banner_image'], $data['remove_banner']);

        $event = Event::create($data);

        User::query()
            ->where('role', 'student')
            ->select(['user_id', 'fullname', 'username', 'email'])
            ->chunkById(200, function ($students) use ($event) {
                Notification::send($students, new StudentEventCreatedNotification($event));
            });

        return redirect()->route('admin.events.index')
            ->with('status', __('Event created successfully.'));
    }

    public function edit(Event $event): View
    {
        return view('dashboards.admin.events.edit', [
            'title' => 'Edit event',
            'event' => $event,
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('banner_image')) {
            if ($event->banner_path && ! Str::startsWith($event->banner_path, ['http://', 'https://', '/'])) {
                $existingPath = public_path($event->banner_path);
                if (is_file($existingPath)) {
                    @unlink($existingPath);
                }
            }

            $image = $request->file('banner_image');

            $extension = strtolower($image->getClientOriginalExtension() ?: 'jpg');
            $fileName = 'event-' . Str::uuid()->toString() . '.' . $extension;
            $directory = public_path('assets/images/events');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $image->move($directory, $fileName);
            $data['banner_path'] = 'assets/images/events/' . $fileName;
        } elseif ($request->boolean('remove_banner')) {
            if ($event->banner_path && ! Str::startsWith($event->banner_path, ['http://', 'https://', '/'])) {
                $existingPath = public_path($event->banner_path);
                if (is_file($existingPath)) {
                    @unlink($existingPath);
                }
            }

            $data['banner_path'] = null;
        }

        unset($data['banner_image'], $data['remove_banner']);

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('status', __('Event updated successfully.'));
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('status', __('Event deleted.'));
    }
}
