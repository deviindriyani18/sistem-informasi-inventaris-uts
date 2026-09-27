<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    /**
     * Menampilkan semua transaksi.
     * Admin dan Petugas boleh melihat.
     */
    public function index()
    {
        $transaksis = Transaksi::with(['barang', 'user'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data transaksi berhasil diambil.',
            'data' => $transaksis,
        ], 200);
    }

    /**
     * Menampilkan detail transaksi.
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['barang', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi berhasil diambil.',
            'data' => $transaksi,
        ], 200);
    }

    /**
     * Membuat transaksi masuk/keluar sekaligus mengubah stok.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_id' => ['required', 'integer', 'exists:barangs,id'],
            'jenis' => ['required', 'in:masuk,keluar'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.exists' => 'Barang tidak ditemukan.',
            'jenis.required' => 'Jenis transaksi wajib dipilih.',
            'jenis.in' => 'Jenis transaksi hanya boleh masuk atau keluar.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $result = DB::transaction(function () use ($validated, $request) {

                // Mengunci data barang selama proses transaksi.
                $barang = Barang::where('id', $validated['barang_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$barang) {
                    throw new \Exception('Barang tidak ditemukan.');
                }

                // Jika barang keluar, stok tidak boleh kurang.
                if (
                    $validated['jenis'] === 'keluar' &&
                    $barang->stok < $validated['jumlah']
                ) {
                    throw new \RuntimeException(
                        'Stok tidak mencukupi. Stok saat ini: ' . $barang->stok
                    );
                }

                // Ubah stok.
                if ($validated['jenis'] === 'masuk') {
                    $barang->stok += $validated['jumlah'];
                } else {
                    $barang->stok -= $validated['jumlah'];
                }

                $barang->save();

                // Simpan transaksi.
                $transaksi = Transaksi::create([
                    'barang_id' => $barang->id,
                    'user_id' => $request->user()->id,
                    'jenis' => $validated['jenis'],
                    'jumlah' => $validated['jumlah'],
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                $transaksi->load(['barang', 'user']);

                return $transaksi;
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan dan stok berhasil diperbarui.',
                'data' => $result,
            ], 201);

        } catch (\RuntimeException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal diproses.',
                'data' => null,
            ], 500);
        }
    }
}