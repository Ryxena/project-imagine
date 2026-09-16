<?php

use App\Models\Pengumuman;
use App\Models\User;

describe('admin pengumuman', function () {
    it('returns 401 when a guest lists pengumuman', function () {
        $this->getJson('/admin/pengumuman')->assertUnauthorized();
    });

    it('returns 403 when a penghuni creates a pengumuman', function () {
        $this->actingAs(User::factory()->penghuni()->create())
            ->postJson('/admin/pengumuman', [
                'judul' => 'Jadwal Pembayaran',
                'type' => 'umum',
                'deskripsi' => 'Bayar sebelum tanggal 10.',
                'tanggal_publish' => now()->toDateString(),
            ])
            ->assertForbidden();
    });

    it('creates a pengumuman as the logged-in admin', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson('/admin/pengumuman', [
                'judul' => 'Jadwal Pembayaran',
                'type' => 'umum',
                'deskripsi' => 'Bayar sebelum tanggal 10.',
                'tanggal_publish' => now()->toDateString(),
            ])
            ->assertCreated()
            ->assertJsonPath('data.admin.name', $admin->name);

        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Jadwal Pembayaran',
            'admin_id' => $admin->id,
        ]);
    });

    it('returns 422 when creating a pengumuman with an empty payload', function () {
        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/admin/pengumuman', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['judul', 'type', 'deskripsi', 'tanggal_publish']);
    });

    it('rejects an invalid type with 422', function () {
        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/admin/pengumuman', [
                'judul' => 'Jadwal Pembayaran',
                'type' => 'darurat',
                'deskripsi' => 'Bayar sebelum tanggal 10.',
                'tanggal_publish' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    });

    it('shows a pengumuman', function () {
        $pengumuman = Pengumuman::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson("/admin/pengumuman/{$pengumuman->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $pengumuman->id);
    });

    it('updates a pengumuman', function () {
        $pengumuman = Pengumuman::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->putJson("/admin/pengumuman/{$pengumuman->id}", ['judul' => 'Judul Baru'])
            ->assertOk();

        $this->assertDatabaseHas('pengumuman', [
            'id' => $pengumuman->id,
            'judul' => 'Judul Baru',
        ]);
    });

    it('deletes a pengumuman', function () {
        $pengumuman = Pengumuman::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->deleteJson("/admin/pengumuman/{$pengumuman->id}")
            ->assertOk();

        $this->assertDatabaseMissing('pengumuman', ['id' => $pengumuman->id]);
    });
});

describe('penghuni pengumuman', function () {
    it('lists pengumuman to the penghuni', function () {
        Pengumuman::factory()->count(2)->create();

        $this->actingAs(User::factory()->penghuni()->create())
            ->getJson('/penghuni/pengumuman')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    });
});
