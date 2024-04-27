<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => BarangModel::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ]);
        }
        $barang = BarangModel::create($request->all());
        return response()->json([
            'code' => 201,
            'message' => 'Data created',
            'data' => $barang
        ]);
    }

    public function show(string $id)
    {
        $barang = BarangModel::with('kategori')->find($id);

        if (!$barang) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $barang
        ]);
    }

    public function update(Request $request, string $id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required',
            'barang_nama' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Data tidak valid',
                'error' => $validator->errors()
            ]);
        }

        $barang->update($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'Data updated',
            'data' => $barang
        ]);
    }

    public function destroy(string $id)
    {
        $barang = BarangModel::find($id);
        if (!$barang) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found'
            ]);
        }
        $barang->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Data deleted'
        ]);
    }
}
