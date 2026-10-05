<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\Staff;
use App\Support\StaffNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_notifications_page_renders_bulk_actions_and_read_state(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        Message::create([
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'message' => 'Please confirm my booking.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.notifications'))
            ->assertOk()
            ->assertSee('id="markAllReadButton"', false)
            ->assertSee('id="clearAllNotificationsButton"', false)
            ->assertSee('id="notificationConfirmDialog"', false)
            ->assertSee('data-notification-state="unread"', false);
    }

    public function test_admin_can_mark_notifications_read_without_marking_them_replied(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $message = Message::create([
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'message' => 'Please confirm my booking.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect(route('admin.notifications'));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => true,
            'is_replied' => false,
        ]);
    }

    public function test_admin_can_clear_notifications_and_their_replies(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $message = Message::create([
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'message' => 'Please confirm my booking.',
        ]);
        MessageReply::create([
            'message_id' => $message->id,
            'reply' => 'Your booking is confirmed.',
            'replied_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.notifications.clear-all'))
            ->assertRedirect(route('admin.notifications'));

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_replies', 0);
    }

    public function test_admin_sidebar_shows_actual_unread_module_count(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin', 'is_active' => true]);
        StaffNotificationService::notifyAdmins('New reservation', 'A booking needs review.', [
            'reference' => 'admin-sidebar-reservation:1',
            'url' => route('admin.reservations'),
            'type' => 'reservation',
            'module' => 'reservations',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.notifications'))
            ->assertOk()
            ->assertSee('1 unread reservations', false)
            ->assertSee('class="sidebar-notification-badge"', false);

        $this->getJson(route('admin.notification-feed.index'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('data.0.title', 'New reservation');
    }
}