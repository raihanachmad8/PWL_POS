<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanModel extends Model
{
    use HasFactory;

    protected $table = 't_penjualan';
    protected $primaryKey = 'penjualan_id';

    protected $fillable = ['user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal'];

    public function user() {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function detail() {
        return $this->hasMany(PenjualanDetailModel::class, 'penjualan_id', 'penjualan_id');
    }
}
