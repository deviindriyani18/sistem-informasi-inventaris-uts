<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan database seed.
     */
    public function run(): void
    {
        // =========================
        // DATA USER
        // =========================

        $admin = User::create([
            'name' => 'Admin Inventaris',
            'email' => 'admin@inventaris.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $petugas = User::create([
            'name' => 'Petugas Inventaris',
            'email' => 'petugas@inventaris.test',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
        ]);

        // =========================
        // DATA KATEGORI
        // =========================

        $elektronik = Kategori::create([
            'nama_kategori' => 'Elektronik',
        ]);

        $atk = Kategori::create([
            'nama_kategori' => 'ATK',
        ]);

        $peralatan = Kategori::create([
            'nama_kategori' => 'Peralatan Kantor',
        ]);

        // =========================
        // DATA BARANG
        // =========================

        $laptop = Barang::create([
            'kategori_id' => $elektronik->id,
            'kode_barang' => 'ELK-001',
            'nama_barang' => 'Laptop',
            'stok' => 10,
        ]);

        $printer = Barang::create([
            'kategori_id' => $elektronik->id,
            'kode_barang' => 'ELK-002',
            'nama_barang' => 'Printer',
            'stok' => 5,
        ]);

        $pulpen = Barang::create([
            'kategori_id' => $atk->id,
            'kode_barang' => 'ATK-001',
            'nama_barang' => 'Pulpen',
            'stok' => 50,
        ]);

        $kursi = Barang::create([
            'kategori_id' => $peralatan->id,
            'kode_barang' => 'PRL-001',
            'nama_barang' => 'Kursi Kantor',
            'stok' => 20,
        ]);

        // =========================
        // DATA TRANSAKSI
        // =========================

        Transaksi::create([
            'barang_id' => $laptop->id,
            'user_id' => $admin->id,
            'jenis' => 'masuk',
            'jumlah' => 10,
            'tanggal' => '2026-09-27',
            'keterangan' => 'Stok awal laptop',
        ]);

        Transaksi::create([
            'barang_id' => $printer->id,
            'user_id' => $admin->id,
            'jenis' => 'masuk',
            'jumlah' => 5,
            'tanggal' => '2026-09-27',
            'keterangan' => 'Stok awal printer',
        ]);

        Transaksi::create([
            'barang_id' => $pulpen->id,
            'user_id' => $petugas->id,
            'jenis' => 'keluar',
            'jumlah' => 5,
            'tanggal' => '2026-09-27',
            'keterangan' => 'Digunakan untuk kegiatan kantor',
        ]);

        Transaksi::create([
            'barang_id' => $kursi->id,
            'user_id' => $petugas->id,
            'jenis' => 'keluar',
            'jumlah' => 2,
            'tanggal' => '2026-09-27',
            'keterangan' => 'Digunakan untuk ruang kerja',
        ]);
    }
}