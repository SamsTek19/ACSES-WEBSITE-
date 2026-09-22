<?php

namespace App\Services\Auth;

use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeviceDetectionService
{
    /**
     * Generate a device fingerprint from the current request.
     * Combines user agent, IP address (partial), and accept headers for uniqueness.
     */
    public function generateFingerprint(Request $request): string
    {
        $components = [
            $request->userAgent() ?? 'unknown',
            $request->header('Accept-Language', 'unknown'),
            $request->header('Accept-Encoding', 'unknown'),
            // Use partial IP to allow some flexibility (e.g., dynamic IPs within same ISP)
            $this->getPartialIp($request->ip()),
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Get a partial IP address (first 3 octets for IPv4, first 4 groups for IPv6).
     * This provides some location consistency without being too strict.
     */
    private function getPartialIp(?string $ip): string
    {
        if (! $ip) {
            return 'unknown';
        }

        // IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return implode('.', array_slice($parts, 0, 3)) . '.x';
        }

        // IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            return implode(':', array_slice($parts, 0, 4)) . ':x:x:x:x';
        }

        return $ip;
    }

    /**
     * Generate a human-readable device name from the user agent.
     */
    public function parseDeviceName(Request $request): string
    {
        $userAgent = $request->userAgent() ?? '';

        // Detect browser
        $browser = 'Unknown Browser';
        if (preg_match('/Edg\/[\d.]+/', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/Chrome\/[\d.]+/', $userAgent) && ! str_contains($userAgent, 'Edg')) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/Firefox\/[\d.]+/', $userAgent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/Safari\/[\d.]+/', $userAgent) && ! str_contains($userAgent, 'Chrome')) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera|OPR\//', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect OS
        $os = 'Unknown OS';
        if (str_contains($userAgent, 'Windows NT 10')) {
            $os = 'Windows 10/11';
        } elseif (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Mac OS X')) {
            $os = 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $os = 'iOS';
        }

        return "{$browser} on {$os}";
    }

    /**
     * Check if the current device is trusted for the given user.
     */
    public function isTrustedDevice(User $user, Request $request): bool
    {
        $fingerprint = $this->generateFingerprint($request);

        return TrustedDevice::where('user_id', $user->getAuthIdentifier())
            ->where('device_fingerprint', $fingerprint)
            ->exists();
    }

    /**
     * Trust the current device for a user.
     */
    public function trustDevice(User $user, Request $request): TrustedDevice
    {
        $fingerprint = $this->generateFingerprint($request);

        // Update existing or create new
        $device = TrustedDevice::updateOrCreate(
            [
                'user_id' => $user->getAuthIdentifier(),
                'device_fingerprint' => $fingerprint,
            ],
            [
                'device_name' => $this->parseDeviceName($request),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 512),
                'last_used_at' => now(),
            ]
        );

        return $device;
    }

    /**
     * Update the last used timestamp for a trusted device.
     */
    public function touchDevice(User $user, Request $request): void
    {
        $fingerprint = $this->generateFingerprint($request);

        TrustedDevice::where('user_id', $user->getAuthIdentifier())
            ->where('device_fingerprint', $fingerprint)
            ->update(['last_used_at' => now()]);
    }

    /**
     * Revoke all trusted devices for a user.
     */
    public function revokeAllDevices(User $user): void
    {
        TrustedDevice::where('user_id', $user->getAuthIdentifier())->delete();
    }

    /**
     * Get all trusted devices for a user.
     */
    public function getUserDevices(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return TrustedDevice::where('user_id', $user->getAuthIdentifier())
            ->orderByDesc('last_used_at')
            ->get();
    }
}
