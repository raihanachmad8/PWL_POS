<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $hargaBeli = rand(1000, 100000);
            $data = [
                'barang_id' => $i,
                'kategori_id' => rand(1, 5),
                'barang_kode' => 'BRG' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'barang_nama' => 'Barang ' . $i,
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaBeli * 1.1,
            ];
            DB::table('m_barang')->insert($data);
        }
    }
}
