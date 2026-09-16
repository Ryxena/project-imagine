<?php

use App\Models\Notification;
use App\Models\Pembayaran;
use App\Models\Penghunian;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

describe('admin verifikasi pembayaran', function () {
    it('returns 401 when a guest lists pembayaran', function () {
        $this->getJson('/admin/verifikasipembayaran')->assertUnauthorized();
    });

    it('returns 403 when a penghuni verifies a payment', function () {
        $pembayaran = Pembayaran::factory()->create();

        $this->actingAs(User::factory()->penghuni()->create())
            ->putJson("/admin/verifikasipembayaran/{$pembayaran->id}", ['status_verifikasi' => 'success'])
            ->assertForbidden();
    });

    it('lists pembayaran with the tagihan and penghuni', function () {
        $pembayaran = Pembayaran::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson('/admin/verifikasipembayaran')
            ->assertOk()
            ->assertJsonPath('data.0.id', $pembayaran->id)
            ->assertJsonPath('data.0.tagihan.penghunian.user.name', $pembayaran->tagihan->penghunian->user->name);
    });

    it('filters pembayaran by verification status', function () {
        $pending = Pembayaran::factory()->create();
        Pembayaran::factory()->success()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->getJson('/admin/verifikasipembayaran?status=pending')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $pending->id);
    });

    it('approves a payment and notifies the penghuni', function () {
        $pembayaran = Pembayaran::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->putJson("/admin/verifikasipembayaran/{$pembayaran->id}", ['status_verifikasi' => 'success'])
            ->assertOk()
            ->assertJsonPath('data.status_verifikasi', 'success');

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status_verifikasi' => 'success',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $pembayaran->tagihan->penghunian->user_id,
            'judul' => 'Pembayaran Diterima',
        ]);
    });

    it('requires a rejection reason when rejecting with 422', function () {
        $pembayaran = Pembayaran::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->putJson("/admin/verifikasipembayaran/{$pembayaran->id}", ['status_verifikasi' => 'failed'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['alasan_penolakan']);
    });

    it('rejects a payment and notifies the penghuni with the reason', function () {
        $pembayaran = Pembayaran::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->putJson("/admin/verifikasipembayaran/{$pembayaran->id}", [
                'status_verifikasi' => 'failed',
                'alasan_penolakan' => 'Bukti pembayaran tidak terbaca.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status_verifikasi', 'failed');

        $this->assertDatabaseHas('pembayarans', [
            'id' => $pembayaran->id,
            'status_verifikasi' => 'failed',
            'alasan_penolakan' => 'Bukti pembayaran tidak terbaca.',
        ]);
        expect(
            Notification::query()
                ->where('user_id', $pembayaran->tagihan->penghunian->user_id)
                ->where('judul', 'Pembayaran Ditolak')
                ->where('pesan', 'like', '%Bukti pembayaran tidak terbaca.%')
                ->exists()
        )->toBeTrue();
    });

    it('rejects verifying an already verified payment with 422', function () {
        $pembayaran = Pembayaran::factory()->success()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->putJson("/admin/verifikasipembayaran/{$pembayaran->id}", ['status_verifikasi' => 'failed'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Pembayaran ini sudah diverifikasi.');
    });

    it('deletes a payment and its bukti file', function () {
        Storage::fake('public');
        $pembayaran = Pembayaran::factory()->create([
            'bukti_pembayaran' => 'pembayaran/bukti/hapus.png',
        ]);
        Storage::disk('public')->put('pembayaran/bukti/hapus.png', 'dummy');

        $this->actingAs(User::factory()->admin()->create())
            ->deleteJson("/admin/verifikasipembayaran/{$pembayaran->id}")
            ->assertOk();

        Storage::disk('public')->assertMissing('pembayaran/bukti/hapus.png');
        $this->assertDatabaseMissing('pembayarans', ['id' => $pembayaran->id]);
    });
});

describe('penghuni pembayaran', function () {
    it('uploads a bukti pembayaran and notifies the admins', function () {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $tagihan = Tagihan::factory()->create();

        $this->actingAs($tagihan->penghunian->user)
            ->post('/penghuni/pembayaran', [
                'tagihan_id' => $tagihan->id,
                'bukti_pembayaran' => buktiImage(),
                'tanggal_pembayaran' => now()->toDateString(),
            ])
            ->assertCreated();

        $pembayaran = Pembayaran::query()->where('tagihan_id', $tagihan->id)->first();
        expect($pembayaran->status_verifikasi)->toBe('pending');
        Storage::disk('public')->assertExists($pembayaran->bukti_pembayaran);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'judul' => 'Pembayaran Baru',
        ]);
    });

    it('accepts a pdf bukti pembayaran', function () {
        Storage::fake('public');
        $tagihan = Tagihan::factory()->create();

        $this->actingAs($tagihan->penghunian->user)
            ->post('/penghuni/pembayaran', [
                'tagihan_id' => $tagihan->id,
                'bukti_pembayaran' => buktiPdf(),
                'tanggal_pembayaran' => now()->toDateString(),
            ])
            ->assertCreated();

        $pembayaran = Pembayaran::query()->where('tagihan_id', $tagihan->id)->first();
        expect($pembayaran)->not->toBeNull();
        Storage::disk('public')->assertExists($pembayaran->bukti_pembayaran);
    });

    it('rejects a second upload while a payment is pending with 422', function () {
        $tagihan = Tagihan::factory()->create();
        Pembayaran::factory()->for($tagihan)->create();

        $this->actingAs($tagihan->penghunian->user)
            ->post('/penghuni/pembayaran', [
                'tagihan_id' => $tagihan->id,
                'bukti_pembayaran' => buktiImage(),
                'tanggal_pembayaran' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Tagihan ini sudah memiliki pembayaran yang menunggu verifikasi atau sudah lunas.');
    });

    it('allows a re-upload after a payment was rejected', function () {
        Storage::fake('public');
        $tagihan = Tagihan::factory()->create();
        Pembayaran::factory()->for($tagihan)->failed()->create();

        $this->actingAs($tagihan->penghunian->user)
            ->post('/penghuni/pembayaran', [
                'tagihan_id' => $tagihan->id,
                'bukti_pembayaran' => buktiImage('ulang.png'),
                'tanggal_pembayaran' => now()->toDateString(),
            ])
            ->assertCreated();

        expect(Pembayaran::query()->where('tagihan_id', $tagihan->id)->count())->toBe(2);
    });

    it('returns 404 when uploading for another penghuni tagihan', function () {
        $other = Tagihan::factory()->create();
        $mine = Penghunian::factory()->create();

        $this->actingAs($mine->user)
            ->post('/penghuni/pembayaran', [
                'tagihan_id' => $other->id,
                'bukti_pembayaran' => buktiImage(),
                'tanggal_pembayaran' => now()->toDateString(),
            ])
            ->assertNotFound();
    });

    it('cancels a pending payment and deletes the bukti', function () {
        Storage::fake('public');
        $tagihan = Tagihan::factory()->create();
        $pembayaran = Pembayaran::factory()->for($tagihan)->create([
            'bukti_pembayaran' => 'pembayaran/bukti/batal.png',
        ]);
        Storage::disk('public')->put('pembayaran/bukti/batal.png', 'dummy');

        $this->actingAs($tagihan->penghunian->user)
            ->deleteJson("/penghuni/pembayaran/{$pembayaran->id}")
            ->assertOk();

        Storage::disk('public')->assertMissing('pembayaran/bukti/batal.png');
        $this->assertDatabaseMissing('pembayarans', ['id' => $pembayaran->id]);
    });

    it('forbids cancelling a verified payment with 422', function () {
        $tagihan = Tagihan::factory()->create();
        $pembayaran = Pembayaran::factory()->for($tagihan)->success()->create();

        $this->actingAs($tagihan->penghunian->user)
            ->deleteJson("/penghuni/pembayaran/{$pembayaran->id}")
            ->assertStatus(422)
            ->assertJsonPath('message', 'Pembayaran sudah diverifikasi dan tidak dapat dibatalkan.');
    });
});
