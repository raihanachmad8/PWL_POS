<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';

    protected $fillable = ['level_id', 'username', 'nama', 'password'];

    public function level() : BelongsTo {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    public function stok() {
        return $this->hasMany(StokModel::class, 'user_id', 'user_id');
    }
}
