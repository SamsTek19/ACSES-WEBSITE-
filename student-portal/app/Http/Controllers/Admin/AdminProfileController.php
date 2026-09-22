<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\CreateSnapshotRequest;
use App\Http\Requests\Admin\UpdateAdminProfileRequest;
use App\Models\User;
use App\Services\Admin\AdminProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProfileController extends Controller
{
    public function __construct(private readonly AdminProfileService $profileService)
    {
    }

    public function index(Request $request): View
    {
        /** @var User|null $admin */
        $admin = $request->user('admin');

        abort_unless($admin instanceof User, 403);

        return view('dashboards.admin.profile.index', [
            'title' => 'Admin profile',
            'admin' => $admin,
            'others' => $this->profileService->otherAdmins($admin),
            'snapshots' => $this->profileService->recentSnapshots(),
        ]);
    }

    public function update(UpdateAdminProfileRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user('admin');

        $result = $this->profileService->updateProfile($admin, $request->validated());

        $messages = [];

        if ($result['profile_updated']) {
            $messages[] = __('Profile updated successfully.');
        }

        if ($result['password_updated']) {
            $messages[] = __('Password updated successfully.');
        }

        if (empty($messages)) {
            $messages[] = __('No changes were applied.');
        }

        return back()->with('status', implode(' ', $messages));
    }

    public function storeAdmin(CreateAdminRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user('admin');

        $newAdmin = $this->profileService->createAdmin($admin, $request->validated());

        return back()->with('status', __('Administrator :name was added.', ['name' => $newAdmin->fullname ?? $newAdmin->username]));
    }

    public function createSnapshot(CreateSnapshotRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user('admin');

        $snapshot = $this->profileService->createSnapshot(
            $admin,
            $request->validated()['type'],
            $request->validated()['notes'] ?? null
        );

        return back()->with('status', __('Snapshot :file generated successfully.', ['file' => $snapshot['filename']]));
    }

    public function downloadSnapshot(string $snapshot): StreamedResponse
    {
        $path = base64_decode($snapshot, true);

        abort_if(! is_string($path) || ! Str::startsWith($path, 'snapshots/'), 404);

        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
