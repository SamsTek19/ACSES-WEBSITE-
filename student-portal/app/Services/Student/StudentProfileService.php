<?php

namespace App\Services\Student;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentProfileService
{
    public function __construct(private readonly StudentEmailChangeService $emailChangeService)
    {
    }

    /**
     * Update the authenticated student's profile details.
     */
    public function update(User $student, array $data): array
    {
        $profileFields = ['fullname', 'phone_number', 'department', 'class', 'year'];
        $safe = Arr::only($data, $profileFields);

        $profileFile = $data['profile_picture'] ?? null;
        $croppedData = $data['profile_picture_cropped'] ?? null;
        $removePicture = filter_var($data['remove_profile_picture'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $profileUpdated = false;
        $passwordUpdated = false;
        $emailStatus = null;

        if ($removePicture) {
            $this->deleteProfilePicture($student);
            $safe['profile_picture'] = null;
        } elseif ($profileFile || $croppedData) {
            $safe['profile_picture'] = $this->storeProfilePicture($student, $profileFile, $croppedData);
        }

        $student->fill($safe);
        if ($student->isDirty($profileFields) || array_key_exists('profile_picture', $safe)) {
            $profileUpdated = true;
        }

        if (! empty($data['password'])) {
            $student->password = Hash::make($data['password']);
            $passwordUpdated = true;
        }

        if ($profileUpdated || $passwordUpdated) {
            $student->save();
        }

        if (array_key_exists('pending_email', $data)) {
            $newEmail = trim((string) ($data['pending_email'] ?? ''));

            if ($newEmail === '') {
                if ($student->pending_email) {
                    $this->emailChangeService->cancel($student);
                    $emailStatus = 'cancelled';
                }
            } else {
                $status = $this->emailChangeService->initiate($student, $newEmail);

                switch ($status) {
                    case 'taken':
                        throw ValidationException::withMessages([
                            'pending_email' => __('That email address is already in use.'),
                        ]);
                    case 'unchanged':
                        break;
                    default:
                        $emailStatus = $status;
                        break;
                }
            }
        }

        return [
            'profile_updated' => $profileUpdated,
            'password_updated' => $passwordUpdated,
            'email_status' => $emailStatus,
        ];
    }

    private function storeProfilePicture(User $student, $file = null, ?string $croppedData = null): string
    {
        if ($croppedData) {
            $extension = 'png';
            if (preg_match('/^data:image\/(\w+);base64,/', $croppedData, $matches)) {
                $extension = strtolower($matches[1]);
                $croppedData = substr($croppedData, strpos($croppedData, ',') + 1);
            }

            $binary = base64_decode($croppedData, true);
            if ($binary === false) {
                return $student->profile_picture ?? '';
            }

            Storage::disk('public')->makeDirectory('profiles');
            $path = 'profiles/' . Str::uuid() . '.' . $extension;
            $stored = Storage::disk('public')->put($path, $binary);

            if (! $stored) {
                return $student->profile_picture ?? '';
            }
        } elseif ($file) {
            Storage::disk('public')->makeDirectory('profiles');
            $path = $file->store('profiles', 'public');
        } else {
            return $student->profile_picture ?? '';
        }

        if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
            Storage::disk('public')->delete($student->profile_picture);
        }

        return $path;
    }

    private function deleteProfilePicture(User $student): void
    {
        if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
            Storage::disk('public')->delete($student->profile_picture);
        }
    }
}
