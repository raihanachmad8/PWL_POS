<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index(): Response|RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            if ($user->level_id == '1') {
                return redirect()->intended('admin');
            } else if ($user->level_id == '2') {
                return redirect()->intended('manager');
            }
        }

        return response()->view('login');
    }

    public function proses_login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credential = $request->only('username', 'password');

        if (Auth::attempt($credential)) {

            $user = Auth::user();

            if ($user->level_id == '1') {
                return redirect()->intended('admin');
            } else if ($user->level_id == '2') {
                return redirect()->intended('manager');
            }

            return redirect()->intended('/');
        }


        return redirect('login')->withInput()->withErrors(['login_gagal' => 'Pastikan kembali username dan password yang dimasukkan sudah benar']);
    }

    public function register(): Response
    {
        return response()->view('register');
    }

    public function proses_register(Request $request): RedirectResponse
    {

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'username' => 'required | unique:m_user,username',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/register')->withErrors($validator)->withInput();
        }

        $request['level_id'] = '2';
        $request['password'] = Hash::make($request->password);

        UserModel::create($request->all());

        return redirect()->route('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();

        Auth::logout();

        return Redirect('login');
    }
}
