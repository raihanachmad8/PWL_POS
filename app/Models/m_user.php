<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class m_user extends Model
{
    protected $table = 'useri';
    protected $primaryKey = 'user_id';
    protected $timestamp = false;

    protected $fillable = ['user_id', 'username', 'nama', 'password', 'level_id'];

    public function level() {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }
}
