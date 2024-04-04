<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    use HasFactory;
    protected $table = 'm_level';
    protected $primaryKey = 'level_id';

    protected $fillable = [
        'level_kode',
        'level_nama',
    ];

    public function user() {
        return $this->hasMany(UserModel::class, 'level_id', 'level_id');
    }

    public function m_user() {
        return $this->hasMany(m_user::class, 'level_id', 'level_id');
    }
}
