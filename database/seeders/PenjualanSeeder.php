<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // make 10 transaksi penjualan
        for ($i = 1; $i <= 10; $i++) {
            $data = [
                'penjualan_id' => $i,
                'user_id' => rand(1, 3),
                'pembeli' => rand(1, 10),
                'penjualan_kode' => 'TRX' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'penjualan_tanggal' => date('Y-m-d H:i:s'),
            ];
            DB::table('t_penjualan')->insert($data);
        }
    }
}
