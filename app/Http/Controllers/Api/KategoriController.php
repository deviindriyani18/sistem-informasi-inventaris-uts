<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    /**
     * Menampilkan semua kategori.
     * Admin dan Petugas boleh melihat.
     */
    public function index()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil.',
            'data' => $kategoris,
        ], 200);
    }

    /**
     * Menambahkan kategori.
     * Hanya Admin.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => ['required', 'string', 'max:255'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $kategori = Kategori::create([
            'nama_kategori' => $validator->validated()['nama_kategori'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $kategori,
        ], 201);
    }

    /**
     * Menampilkan satu kategori.
     * Admin dan Petugas boleh melihat.
     */
    public function show(Kategori $kategori)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil.',
            'data' => $kategori,
        ], 200);
    }

    /**
     * Mengubah kategori.
     * Hanya Admin.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => ['required', 'string', 'max:255'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $kategori->update([
            'nama_kategori' => $validator->validated()['nama_kategori'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $kategori->fresh(),
        ], 200);
    }

    /**
     * Menghapus kategori.
     * Hanya Admin.
     */
    public function destroy(Kategori $kategori)
    {
        // Jika kategori masih dipakai oleh barang,
        // database akan menolak penghapusan karena foreign key.
        if ($kategori->barangs()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh barang.',
                'data' => null,
            ], 409);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
            'data' => null,
        ], 200);
    }
}