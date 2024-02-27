<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $a = 1;
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $barangId = rand(1, 10);
                $jumlah = rand(1, 10);
                $barang = DB::table('m_barang')->where('barang_id', $barangId)->first();
                if ($barang) {
                    $data[] = [
                        'detail_id' => $a,
                        'penjualan_id' => $i,
                        'barang_id' => $barangId,
                        'harga' => $barang->harga_jual * $jumlah,
                        'jumlah' => $jumlah,
                    ];
                    $a++;
                }
            }
        }

        DB::table('t_penjualan_detail')->insert($data);
    }
}
