<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {


        // $user = UserModel::all();
        // return view('user', ['data' => $user]);

        // $user = UserModel::with('level')->get();
        // return view('user', ['data' => $user]);

        return view('users.index');
    }

    public function tambah() {
        return view('user_tambah');
    }

    public function tambah_simpan(Request $request) {
        // UserModel::create([
        //     'username' => $request->username,
        //     'nama' => $request->nama,
        //     'password' => Hash::make($request->password),
        //     'level_id' => $request->level_id
        // ]);

        $validate = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'unique:m_user', 'min:5'],
            'password' => ['required', 'string', 'min:8'],
            'level_id' => ['required', 'integer'],
        ]);

        $validate['password'] = Hash::make($validate['password']);

        UserModel::create($validate);

        return redirect('/user');
    }

    public function ubah($id) {
        $user = UserModel::find($id);
        return view('user_ubah', ['data' => $user]);
    }

    public function ubah_simpan($id, Request $request) {
        $user = UserModel::find($id);

        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->level_id = $request->level_id;

        $user->save();
        return redirect('/user');
    }

    public function hapus($id) {
        $user = UserModel::find($id);
        $user->delete();

        return redirect('/user');
    }
}
