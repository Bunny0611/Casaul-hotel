<?php

namespace App\Support;

use App\Models\Staff;
use App\Models\StaffMessage;

class StaffMessageInbox
{
    public function for(Staff $staff): array
    {
        $contacts = Staff::query()
            ->whereKeyNot($staff->id)
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $messages = StaffMessage::query()
            ->with(['sender:id,name,role', 'recipient:id,name,role'])
            ->where(fn ($query) => $query->where('sender_id', $staff->id)->orWhere('recipient_id', $staff->id))
            ->orderBy('created_at')
            ->get();

        $messagesByContact = $messages->groupBy(fn (StaffMessage $message) =>
            $message->sender_id === $staff->id ? $message->recipient_id : $message->sender_id
        );

        $conversations = $contacts->mapWithKeys(function (Staff $contact) use ($messagesByContact) {
            $thread = $messagesByContact->get($contact->id, collect());

            return [$contact->id => (object) [
                'contact' => $contact,
                'messages' => $thread,
                'latest_message' => $thread->last(),
            ]];
        });

        return [
            'staffConversations' => $conversations,
            'staffConversationData' => $conversations->mapWithKeys(function ($conversation, $contactId) use ($staff) {
                return [$contactId => [
                    'name' => $conversation->contact->name,
                    'role' => $conversation->contact->role,
                    'messages' => $conversation->messages->map(fn (StaffMessage $message) => [
                        'body' => $message->body,
                        'sender' => $message->sender->name,
                        'sender_role' => $message->sender->role,
                        'sent_at' => $message->created_at?->format('M j, Y g:i A'),
                        'mine' => $message->sender_id === $staff->id,
                    ])->values(),
                ]];
            }),
        ];
    }
}