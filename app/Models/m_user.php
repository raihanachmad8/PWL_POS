<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class m_user extends Model
{
    protected $table = 'useri';
    protected $primaryKey = 'user_id';
    protected $timestamp = false;

    protected $fillable = ['user_id', 'username', 'nama', 'password', 'level_id'];
    

}
