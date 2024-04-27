<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => UserModel::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'username' => 'required|unique:m_user',
            'password' => 'required|confirmed',
            'level_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ]);
        }

        $request->merge([
            'password' => Hash::make($request->password)
        ]);

        $user = UserModel::create($request->all());
        return response()->json([
            'code' => 201,
            'message' => 'Data created',
            'data' => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
                'request' => $request->all()
            ]);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'username' => 'required|unique:m_user,username,' . $user->user_id . ',user_id',
            'password' => 'required|confirmed',
            'level_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ]);
        }

        $request->merge([
            'password' => Hash::make($request->password)
        ]);

        $user->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Data updated',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = UserModel::find($id);
        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }

        $user->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Data deleted'
        ]);
    }
}
