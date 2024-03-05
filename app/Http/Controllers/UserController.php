<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {


        $user = UserModel::create([
            'username' => 'manager11',
            'nama' => 'Manager11',
            'password' => Hash::make('12345'),
            'level_id' => 2
        ]);

        $user->username = 'manager12';

        $user->save();

        $user->wasChanged();

        $user->wasChanged();
        $user->wasChanged('username');
        $user->wasChanged('nama');
        $user->wasChanged(['nama', 'username']);

        dd($user->wasChanged(['nama', 'username']));
    }
}
