<?php

use App\Models\Pembayaran;
use App\Models\Penghunian;
use App\Models\Tagihan;
use App\Models\User;

describe('admin tagihan', function () {
    it('returns 401 when a guest lists tagihan', function () {
        $this->getJson('/admin/tagihan')->assertUnauthorized();
    });

    it('returns 403 when a penghuni lists admin tagihan', function () {
        $this->actingAs(User::factory()->penghuni()->create())
            ->getJson('/admin/tagihan')
            ->assertForbidden();
    });

    it('lists tagihan with the penghuni name and payment status', function () {
        $penghunian = Penghunian::factory()->create();
        $tagihan = Tagihan::factory()->for($penghunian)->create();
        Pembayaran::factory()->for($tagihan)->success()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson('/admin/tagihan')
            ->assertOk()
            ->assertJsonPath('data.0.id', $tagihan->id)
            ->assertJsonPath('data.0.status_pembayaran', 'lunas')
            ->assertJsonPath('data.0.penghunian.user.name', $penghunian->user->name);
    });

    it('filters the admin tagihan list by payment status', function () {
        $lunas = Tagihan::factory()->create();
        Pembayaran::factory()->for($lunas)->success()->create();
        Tagihan::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson('/admin/tagihan?status=lunas')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $lunas->id);
    });

    it('searches admin tagihan by penghuni name', function () {
        $budi = User::factory()->penghuni()->create(['name' => 'Budi Santoso']);
        $penghunianBudi = Penghunian::factory()->create(['user_id' => $budi->id]);
        $tagihanBudi = Tagihan::factory()->for($penghunianBudi)->create();
        Tagihan::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson('/admin/tagihan?search=Budi')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $tagihanBudi->id);
    });

    it('lists active penghuni for tagihan creation', function () {
        $aktif = Penghunian::factory()->create();
        Penghunian::factory()->tanpaKamar()->create();
        Penghunian::factory()->checkout()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson('/admin/tagihan/daftarpenghuni')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $aktif->id);
    });

    it('creates a tagihan and notifies the penghuni', function () {
        $penghunian = Penghunian::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/admin/tagihan', [
                'penghunian_id' => $penghunian->id,
                'bulan_tagihan' => now()->format('Y-m'),
                'jumlah' => 750000,
            ])
            ->assertCreated()
            ->assertJsonPath('message', 'Tagihan berhasil ditambahkan.');

        $this->assertDatabaseHas('tagihans', [
            'penghunian_id' => $penghunian->id,
            'bulan_tagihan' => now()->format('Y-m'),
            'jumlah' => 750000,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $penghunian->user_id,
            'tipe' => 'tagihan',
        ]);
    });

    it('rejects a duplicate tagihan for the same penghunian and bulan with 422', function () {
        $penghunian = Penghunian::factory()->create();
        $tagihan = Tagihan::factory()->for($penghunian)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/admin/tagihan', [
                'penghunian_id' => $penghunian->id,
                'bulan_tagihan' => $tagihan->bulan_tagihan,
                'jumlah' => 750000,
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Tagihan untuk bulan tersebut sudah ada.');
    });

    it('returns 422 when creating a tagihan with an empty payload', function () {
        $this->actingAs(User::factory()->admin()->create())
            ->postJson('/admin/tagihan', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['penghunian_id', 'bulan_tagihan', 'jumlah']);
    });

    it('updates a tagihan', function () {
        $tagihan = Tagihan::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->putJson("/admin/tagihan/{$tagihan->id}", [
                'bulan_tagihan' => '2026-10',
                'jumlah' => 900000,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Tagihan berhasil diperbarui.');

        $this->assertDatabaseHas('tagihans', [
            'id' => $tagihan->id,
            'bulan_tagihan' => '2026-10',
            'jumlah' => 900000,
        ]);
    });

    it('deletes a tagihan and its payments', function () {
        $tagihan = Tagihan::factory()->create();
        $pembayaran = Pembayaran::factory()->for($tagihan)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->deleteJson("/admin/tagihan/{$tagihan->id}")
            ->assertOk();

        $this->assertDatabaseMissing('tagihans', ['id' => $tagihan->id]);
        $this->assertDatabaseMissing('pembayarans', ['id' => $pembayaran->id]);
    });
});

describe('penghuni tagihan', function () {
    it('lists only the logged-in penghuni tagihan', function () {
        $mine = Tagihan::factory()->create();
        Tagihan::factory()->create();

        $this->actingAs($mine->penghunian->user)
            ->getJson('/penghuni/tagihan')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mine->id);
    });

    it('filters the penghuni tagihan list by status', function () {
        $penghunian = Penghunian::factory()->create();
        $belumBayar = Tagihan::factory()->for($penghunian)->create();
        $lunas = Tagihan::factory()->for($penghunian)->create();
        Pembayaran::factory()->for($lunas)->success()->create();

        $this->actingAs($penghunian->user)
            ->getJson('/penghuni/tagihan?status=belum_bayar')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $belumBayar->id);
    });

    it('shows the tagihan payment status to its penghuni', function (string $status, ?string $pembayaranState) {
        $tagihan = Tagihan::factory()->create();

        if ($pembayaranState !== null) {
            Pembayaran::factory()->for($tagihan)->{$pembayaranState}()->create();
        }

        $this->actingAs($tagihan->penghunian->user)
            ->getJson("/penghuni/tagihan/{$tagihan->id}")
            ->assertOk()
            ->assertJsonPath('data.status_pembayaran', $status);
    })->with([
        'belum bayar' => ['belum_bayar', null],
        'menunggu verifikasi' => ['menunggu_verifikasi', 'pending'],
        'lunas' => ['lunas', 'success'],
        'ditolak' => ['ditolak', 'failed'],
    ]);

    it('returns 404 when a penghuni views another penghuni tagihan', function () {
        $other = Tagihan::factory()->create();
        $mine = Penghunian::factory()->create();

        $this->actingAs($mine->user)
            ->getJson("/penghuni/tagihan/{$other->id}")
            ->assertNotFound();
    });
});
