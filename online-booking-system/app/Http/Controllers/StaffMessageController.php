<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffMessage;
use App\Models\Message;
use Illuminate\Http\Request;

class StaffMessageController extends Controller
{
    public function store(Request $request)
    {
        $sender = $request->user();
        abort_unless($sender instanceof Staff, 403);

        $validated = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:staff_users,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        abort_if((int) $validated['recipient_id'] === $sender->id, 422, 'You cannot message yourself.');

        StaffMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $validated['recipient_id'],
            'body' => $validated['message'],
        ]);

        return redirect()->route($sender->role . '.messages', [
            'staff_id' => $validated['recipient_id'],
        ])->with('success', 'Staff message sent.');
    }

    public function forwardGuestMessage(Request $request, int $id)
    {
        $sender = $request->user();
        abort_unless($sender instanceof Staff && $sender->role === 'employee', 403);

        $validated = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:staff_users,id'],
            'note' => ['required', 'string', 'max:1000'],
        ]);

        abort_if((int) $validated['recipient_id'] === $sender->id, 422, 'You cannot forward a message to yourself.');

        Message::findOrFail($id);
        $body = "Guest issue handoff:\n\n{$validated['note']}";

        StaffMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $validated['recipient_id'],
            'body' => $body,
        ]);

        return redirect()->route('employee.messages', [
            'staff_id' => $validated['recipient_id'],
        ])->with('success', 'Guest message forwarded to staff.');
    }
}