<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Penghunian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManajemenPenghuniController extends Controller
{
    /**
     * GET /api/penghunians
     *
     * Search:
     * /api/penghunians?search=andi
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $penghunians = Penghunian::query()
            ->join('users', 'penghunians.user_id', '=', 'users.id')
            ->leftJoin('kamars', 'penghunians.kamar_id', '=', 'kamars.id')
            ->select([
                'penghunians.id',
                'penghunians.user_id',
                'penghunians.last_kamar_id',
                'penghunians.kamar_id',

                'users.image',
                'users.name',
                'users.email',
                'users.no_hp',
                'users.status',

                'kamars.nomor_kamar',
                'kamars.tipe_kamar',
                'kamars.harga',

                'penghunians.tanggal_masuk',
                'penghunians.tanggal_checkout',
                'penghunians.created_at',
                'penghunians.updated_at',
            ])->when($search, function ($query, $search) {
                $query->where('users.name', 'like', "%{$search}%");
            })->latest('penghunians.created_at')->get();

        return view('admin.penghuni.ManajemenPenghuni', compact('penghunians'));
    }

    public function showKamar()
    {
        $kamar = Kamar::query()
            ->whereDoesntHave('penghunian', function ($query) {
                $query->whereNull('tanggal_checkout');
            })
            ->select([
                'id',
                'nomor_kamar',
            ])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kamar tersedia berhasil diambil.',
            'data' => $kamar,
        ]);
    }

    public function assignKamar(Request $request, Penghunian $penghunian)
    {
        if ($penghunian->kamar_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Penghuni sudah memiliki kamar.',
            ], 422);
        }

        $validated = $request->validate([
            'kamar_id' => ['required', 'exists:kamars,id',],
            'tanggal_masuk' => ['required', 'date',],
        ]);

        $kamarSudahTerisi = Penghunian::query()
            ->where('kamar_id', $validated['kamar_id'])
            ->whereNull('tanggal_checkout')
            ->exists();

        if ($kamarSudahTerisi) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar tersebut sedang ditempati.',
            ], 422);
        }

        $penghunian->update([
            'kamar_id' => $validated['kamar_id'],
            'last_kamar_id' => $validated['kamar_id'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil diberikan kepada penghuni.',
            'data' => $penghunian,
        ]);
    }

    public function checkoutKamar(Request $request, Penghunian $penghunian)
    {
        if ($penghunian->kamar_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Penghuni belum memiliki kamar.',
            ], 422);
        }

        $penghunian->update([
            'kamar_id' => null,
            'tanggal_checkout' => now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penghuni berhasil checkout dari kamar.',
            'data' => $penghunian->fresh(),
        ]);
    }

    /**
     * GET /api/penghunians/create
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Form create penghuni.',
            'data' => [
                'fields' => [
                    'name',
                    'email',
                    'password',
                    'image',
                    'no_hp',
                    'status',
                    'kamar_id',
                    'tanggal_masuk',
                    'tanggal_checkout',
                ],
            ],
        ]);
    }

    /**
     * POST /api/penghunians
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // USER
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'no_hp' => ['required', 'string', 'max:20'],
            'status' => ['nullable', Rule::in(['aktif', 'non-aktif'])],
            // PENGHUNIAN || debug
            // 'kamar_id' => ['nullable', 'exists:kamars,id'],
            // 'tanggal_masuk' => ['required', 'date'],
            // 'tanggal_checkout' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
        ]);

        try {
            $penghunian = DB::transaction(function () use ($validated) {
                $imagePath = null;
                if (isset($validated['image'])) {
                    $imagePath = $validated['image']->store(
                        'user/profile',
                        'public'
                    );
                }

                // Membuat USER
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'image' => $imagePath,
                    'no_hp' => $validated['no_hp'],
                    'status' => $validated['status'] ?? 'aktif',
                    'role' => 'user',
                ]);

                // Membuat PENGHUNIAN
                return Penghunian::create([
                    'user_id' => $user->id,
                    // 'kamar_id' => $validated['kamar_id'] ?? null,
                    // 'tanggal_masuk' => $validated['tanggal_masuk'],
                    // 'tanggal_checkout' => $validated['tanggal_checkout'] ?? null,
                ]);
            });

            $penghunian->load(['user', 'kamar']);

            return response()->json([
                'success' => true,
                'message' => 'Data penghuni berhasil ditambahkan.',
                'data' => $penghunian,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data penghuni gagal ditambahkan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/penghunians/{penghunian}
     */
    // public function show(Penghunian $penghunian)
    // {
    //     $penghunian->load([
    //         'user',
    //         'kamar',
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Detail penghuni berhasil diambil.',
    //         'data' => $penghunian,
    //     ]);
    // }


    /**
     * GET /api/penghunians/{penghunian}/edit
     */
    public function edit(Penghunian $penghunian)
    {
        $penghunian->load([
            'user',
            'kamar',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data edit penghuni berhasil diambil.',
            'data' => $penghunian,
        ]);
    }

    /**
     * PUT/PATCH /api/penghunians/{penghunian}
     */
    public function update(Request $request, Penghunian $penghunian)
    {
        $user = $penghunian->user;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'no_hp' => ['required', 'string', 'max:20'],
            'status' => ['required', Rule::in(['aktif', 'non-aktif'])],

            'kamar_id' => ['nullable', 'exists:kamars,id'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_checkout' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_masuk'
            ],
        ]);

        try {
            DB::transaction(function () use ($validated, $user, $penghunian) {

                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'no_hp' => $validated['no_hp'],
                    'status' => $validated['status'],
                ];

                /*
                * Jika upload image baru:
                * 1. Simpan image baru
                * 2. Hapus image lama
                * 3. Update database dengan path baru
                */
                if (isset($validated['image'])) {

                    $oldImage = $user->image;

                    $newImage = $validated['image']->store(
                        'user/profile',
                        'public'
                    );

                    $userData['image'] = $newImage;

                    if ($oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make(
                        $validated['password']
                    );
                }

                $user->update($userData);

                $penghunian->update([
                    'kamar_id' => $validated['kamar_id'] ?? null,
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'tanggal_checkout' => $validated['tanggal_checkout'] ?? null,
                ]);
            });

            $penghunian->load(['user', 'kamar']);

            return response()->json([
                'success' => true,
                'message' => 'Data penghuni berhasil diperbarui.',
                'data' => $penghunian,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data penghuni gagal diperbarui.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/penghunians/{penghunian}
     */
    public function destroy(Penghunian $penghunian)
    {
        try {
            DB::transaction(function () use ($penghunian) {

                $user = $penghunian->user;

                $image = $user?->image;

                $penghunian->delete();

                if ($user) {
                    $user->delete();
                }

                if ($image) {
                    Storage::disk('public')->delete($image);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Data penghuni berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data penghuni gagal dihapus.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
