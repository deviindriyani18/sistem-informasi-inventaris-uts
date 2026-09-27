<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diambil.',
            'data' => $barangs,
        ], 200);
    }

    public function show(Barang $barang)
    {
        $barang->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil diambil.',
            'data' => $barang,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'kode_barang' => [
    'required',
    'string',
    'max:255',
    'regex:/\S/',
    'unique:barangs,kode_barang',
],
            'nama_barang' => [
    'required',
    'string',
    'max:255',
    'regex:/\S/',
],
            'stok' => ['required', 'integer', 'min:0'],
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak ditemukan.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $barang = Barang::create($validator->validated());

        $barang->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan.',
            'data' => $barang,
        ], 201);
    }

    public function update(Request $request, Barang $barang)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'kode_barang' => [
                'required',
                'string',
                'max:255',
                'unique:barangs,kode_barang,' . $barang->id,
            ],
            'nama_barang' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak ditemukan.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $barang->update($validator->validated());

        $barang->load('kategori');

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diperbarui.',
            'data' => $barang,
        ], 200);
    }

    public function destroy(Barang $barang)
    {
        if ($barang->transaksis()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak dapat dihapus karena sudah memiliki transaksi.',
                'data' => null,
            ], 409);
        }

        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus.',
            'data' => null,
        ], 200);
    }
}