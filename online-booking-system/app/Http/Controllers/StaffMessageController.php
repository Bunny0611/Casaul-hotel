<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffMessage;
use App\Models\Message;
use App\Support\StaffNotificationService;
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

        $staffMessage = StaffMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $validated['recipient_id'],
            'body' => $validated['message'],
        ]);

        $recipient = Staff::findOrFail($validated['recipient_id']);
        StaffNotificationService::notifyUsers($recipient, 'New staff message', $sender->name . ' sent you a message.', [
            'reference' => 'staff-message:' . $staffMessage->id,
            'url' => route($recipient->role . '.messages', ['staff_id' => $sender->id]),
            'type' => 'message',
            'module' => 'messages',
            'related_id' => $staffMessage->id,
            'related_type' => StaffMessage::class,
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

        $staffMessage = StaffMessage::create([
            'sender_id' => $sender->id,
            'recipient_id' => $validated['recipient_id'],
            'body' => $body,
        ]);

        $recipient = Staff::findOrFail($validated['recipient_id']);
        StaffNotificationService::notifyUsers($recipient, 'Guest message forwarded', $sender->name . ' forwarded a guest message to you.', [
            'reference' => 'staff-message:' . $staffMessage->id,
            'url' => route($recipient->role . '.messages', ['staff_id' => $sender->id]),
            'type' => 'message',
            'module' => 'messages',
            'related_id' => $staffMessage->id,
            'related_type' => StaffMessage::class,
        ]);

        return redirect()->route('employee.messages', [
            'staff_id' => $validated['recipient_id'],
        ])->with('success', 'Guest message forwarded to staff.');
    }
}