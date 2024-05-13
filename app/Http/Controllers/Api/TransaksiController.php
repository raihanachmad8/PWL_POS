<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => PenjualanModel::all()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'pembeli' => 'required',
            'penjualan_kode' => 'required',
            'penjualan_tanggal' => 'required',
            'detail' => 'required|array',
            'detail.*.barang_id' => 'required',
            'detail.*.jumlah' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {

            DB::beginTransaction();

            $penjualan = PenjualanModel::create([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal
            ]);


            foreach ($request->detail as $key => $value) {
                $hargaBarang = BarangModel::find($value['barang_id'])->harga_jual;

                $penjualan->detail()->create([
                    'barang_id' => $value['barang_id'],
                    'penjualan_id' => $penjualan->id,
                    'harga' => $hargaBarang * $value['jumlah'],
                    'jumlah' => $value['jumlah']
                ]);

                $stok = StokModel::find($value['barang_id']);

                if ($stok->stok_jumlah < $value['jumlah']) {
                    throw new Exception('Stok barang tidak mencukupi');
                }

                $stok->stok_jumlah = $stok->stok_jumlah - $value['jumlah'];
                $stok->save();
            }

            DB::commit();

            return response()->json([
                'code' => 201,
                'message' => 'Data detail transaksi berhasil tersimpan',
                'data' => $request->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $penjualan = PenjualanModel::with('detail')->find($id);

            if (!$penjualan) {
                throw new ModelNotFoundException();
            }

            return response()->json([
                'code' => 200,
                'message' => 'Data detail transaksi ditemukan',
                'data' => $penjualan
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'code' => 404,
                'message' => "Data Tidak Ditemukan"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'pembeli' => 'required',
            'penjualan_kode' => 'required',
            'penjualan_tanggal' => 'required',
            'detail' => 'required|array',
            'detail.*.barang_id' => 'required',
            'detail.*.jumlah' => 'required',
        ], [
            'required' => ':attribute harus diisi'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $penjualan = PenjualanModel::with('detail')->find($id);

            DB::beginTransaction();

            $penjualan->update([
                'user_id' => $request->user_id,
                'pembeli' => $request->pembeli,
                'penjualan_kode' => $request->penjualan_kode,
                'penjualan_tanggal' => $request->penjualan_tanggal,
            ]);


            foreach ($request->detail as $key => $value) {
                // Check if 'jumlah' key exists in $value array
                if (isset($value['jumlah'])) {
                    $detail = PenjualanDetailModel::where('penjualan_id', $id)
                        ->where('barang_id', $value['barang_id'])
                        ->first();

                    // Initialize $oldQuantity
                    $oldQuantity = 0;

                    if ($penjualan->detail->where('barang_id', $value['barang_id'])->where('penjualan_id', $id)->first()) {
                        $oldQuantity = $penjualan->detail
                            ->where('barang_id', $value['barang_id'])
                            ->where('penjualan_id', $id)
                            ->first()
                            ->jumlah;
                    }

                    if ($detail !== null) {
                        $detail->jumlah = $value['jumlah'];
                        $detail->harga = $value['jumlah'] * BarangModel::find($value['barang_id'])->harga_jual;
                        $detail->save();
                    } else {
                        PenjualanDetailModel::create([
                            'penjualan_id' => $penjualan->penjualan_id,
                            'barang_id' => $value['barang_id'],
                            'harga' => BarangModel::find($value['barang_id'])->harga_jual * $value['jumlah'],
                            'jumlah' => $value['jumlah']
                        ]);
                    }

                    // Update stok
                    $stok = StokModel::with(['barang'])->where('barang_id', $value['barang_id'])->first();
                    $newQuantity = $value['jumlah'];
                    $quantityDifference = $oldQuantity - $newQuantity;

                    $stok->stok_jumlah += $quantityDifference;

                    if ($stok->stok_jumlah < 0) {
                        throw new Exception('Stok barang tidak mencukupi');
                    }

                    $stok->save();
                } else {
                    // Handle the case where 'jumlah' key is not found in $value array
                    throw new Exception('Jumlah tidak ditemukan');
                }
            }


            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => "Data berhasil diperbarui",
                'data' => $request->all()
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'code' => 404,
                'message' => "Data Tidak Ditemukan"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $penjualan = PenjualanModel::with('detail')->find($id);

            DB::beginTransaction();

            foreach ($penjualan->detail as $key => $value) {
                $penjualan->detail[$key]->delete();
            }

            $penjualan->delete();

            DB::commit();

            return response()->json([
                'code' => 204,
                'message' => "Data berhasil dihapus",
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => "Data Tidak Ditemukan"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
