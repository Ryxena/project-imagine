<?php

use App\Models\Notification;
use App\Models\User;

describe('user notifikasi', function () {
    it('lists only the logged-in user notifications with the unread count', function () {
        $user = User::factory()->penghuni()->create();
        Notification::factory()->for($user)->count(2)->create();
        Notification::factory()->for($user)->create(['dibaca' => true]);
        Notification::factory()->create();

        $this->actingAs($user)
            ->getJson('/penghuni/notifikasi')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('unread_count', 2);
    });

    it('marks one notification as read', function () {
        $user = User::factory()->penghuni()->create();
        $notification = Notification::factory()->for($user)->create();

        $this->actingAs($user)
            ->putJson("/penghuni/notifikasi/{$notification->id}")
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'dibaca' => true,
        ]);
    });

    it('marks all notifications as read', function () {
        $user = User::factory()->penghuni()->create();
        Notification::factory()->for($user)->count(3)->create();

        $this->actingAs($user)
            ->putJson('/penghuni/notifikasi')
            ->assertOk();

        expect(
            Notification::query()->where('user_id', $user->id)->where('dibaca', false)->count()
        )->toBe(0);
    });

    it('deletes a notification', function () {
        $user = User::factory()->penghuni()->create();
        $notification = Notification::factory()->for($user)->create();

        $this->actingAs($user)
            ->deleteJson("/penghuni/notifikasi/{$notification->id}")
            ->assertOk();

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    });

    it('returns 404 when touching another user notification', function () {
        $other = Notification::factory()->create();
        $user = User::factory()->penghuni()->create();

        $this->actingAs($user)
            ->putJson("/penghuni/notifikasi/{$other->id}")
            ->assertNotFound();

        $this->actingAs($user)
            ->deleteJson("/penghuni/notifikasi/{$other->id}")
            ->assertNotFound();
    });

    it('serves admin notifications on the admin route', function () {
        $admin = User::factory()->admin()->create();
        Notification::factory()->for($admin)->create();

        $this->actingAs($admin)
            ->getJson('/admin/notifikasi')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    });
});
