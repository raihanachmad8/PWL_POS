<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => KategoriModel::all()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required',
            'kategori_nama' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ]);
        }

        $kategori = KategoriModel::create($request->all());
        return response()->json([
            'code' => 201,
            'message' => 'Data created',
            'data' => $kategori
        ]);
    }

    public function show(string $id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $kategori
        ]);
    }

    public function update(Request $request, string $id)
    {
        $kategori = KategoriModel::find($id);

        if (!$kategori) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required',
            'kategori_nama' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ]);
        }

        $kategori->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Data updated',
            'data' => $kategori
        ]);
    }

    public function destroy(string $id)
    {
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }
        $kategori->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Data terhapus'
        ]);
    }
}
