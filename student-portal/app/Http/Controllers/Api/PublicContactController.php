<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Suggestion::create([
            'user_id' => null,
            'sender_name' => isset($validated['name']) ? trim($validated['name']) : null,
            'sender_email' => isset($validated['email']) ? trim($validated['email']) : null,
            'category' => 'general',
            'subject' => trim((string) $validated['subject']),
            'message' => trim((string) $validated['message']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you. Your message has been sent to the ACSES team.',
        ], 201);
    }
}
