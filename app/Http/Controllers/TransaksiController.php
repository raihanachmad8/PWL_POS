<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar  Transaksi Penjualan',
            'list' => ['Home', ' Transaksi Penjualan']
        ];

        $page = (object) [
            'title' => 'Daftar penjualan yang terdaftar dalam sistem',
        ];

        $user = UserModel::all();

        $activeMenu = 'penjualan';
        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')->with(['user']);

        if ($request->filled('user_id')) {
            $penjualan->where('user_id', $request->user_id);
        }

        return datatables()->of($penjualan)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($penjualan) { // menambahkan kolom aksi
                $btn = '<a href="' . url('/transaksi/' . $penjualan->penjualan_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/transaksi/' . $penjualan->penjualan_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'
                    . url('/transaksi/' . $penjualan->penjualan_id) . '">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Transaksi Penjualan',
            'list' => ['Home', 'Transaksi Penjualan', 'Create']
        ];

        $page = (object) [
            'title' => 'Tambah transaksi baru',
        ];

        $user = UserModel::all();
        $barang = BarangModel::all();
        $kodePenjualan = 'TRX' . PenjualanModel::count() + 1;


        $today = Carbon::now()->format('Y-m-d');
        $activeMenu = 'penjualan';
        return view('penjualan.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'today' => $today, 'barang' => $barang, 'user' => $user, 'kodePenjualan' => $kodePenjualan, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->only('user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal', 'barang_id', 'jumlah', 'harga',), [
            'penjualan_kode' => ['required', 'max:50', 'unique:t_penjualan,penjualan_kode'],
            'penjualan_tanggal' => ['required', 'date'],
            'user_id' => ['required', 'exists:m_user,user_id'],
            'pembeli' => ['required', 'string', 'max:50'],
            'barang_id' => ['required', 'array', 'min:1', 'exists:m_barang,barang_id'],
            'jumlah' => ['required', 'array', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
        ]);

        if ($validator->fails()) {
            dd($validator->errors());
            return redirect('/transaksi/create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data penjualan gagal ditambahkan terdapat kesalahan pada inputan');
        }

        if (count($request['barang_id']) !== count($request['jumlah']) || count($request['jumlah']) !== count($request['harga'])) {
            return redirect('/transaksi/create')
                ->withInput()
                ->with('error', 'Data penjualan gagal ditambahkan, jumlah barang, jumlah, dan harga tidak sesuai');
        }

        $request['penjualan_tanggal'] = Carbon::createFromFormat('Y-m-d', $request->penjualan_tanggal)
            ->setTime(Carbon::now()->format('H'), Carbon::now()->format('i'), Carbon::now()->format('s'));

        try {

            DB::beginTransaction();

            $penjualan = new PenjualanModel();
            $penjualan->penjualan_kode = $request['penjualan_kode'];
            $penjualan->penjualan_tanggal = $request['penjualan_tanggal'];
            $penjualan->user_id = $request['user_id'];
            $penjualan->pembeli = $request['pembeli'];
            $penjualan->save();

            foreach ($request['barang_id'] as $key => $barang_id) {
                $detail = new PenjualanDetailModel();
                $detail->penjualan_id = $penjualan->penjualan_id;
                $detail->barang_id = $barang_id;
                $detail->jumlah = $request['jumlah'][$key];
                $detail->harga = $request['harga'][$key];
                $detail->save();

                $stok = StokModel::find($barang_id);

                if ($stok->stok_jumlah < $request['jumlah'][$key]) {
                    throw new Exception('Stok barang tidak mencukupi');
                }

                $stok->stok_jumlah = $stok->stok_jumlah - $request['jumlah'][$key];
                $stok->save();
            }

            DB::commit();

            return redirect('/transaksi')->with('success', 'Data transaksi berhasil ditambahkan');
        } catch (Exception $e) {
            DB::rollback();

            return redirect('/transaksi')->with('error', 'Data transaksi gagal ditambahkan');
        }
    }

    public function show($id)
    {
        $penjualan = PenjualanModel::with(['user',  'detail.barang'])->find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Transaksi Penjualan',
            'list' => ['Home', 'Transaksi Penjualan', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail tranasaksi penjualan',
        ];

        $activeMenu = 'penjualan';
        return view('penjualan.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'penjualan' => $penjualan]);
    }

    public function edit($id)
    {
        $user = UserModel::all();
        $barang = BarangModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit Penjualan',
            'list' => ['Home', 'Penjualan', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit stok barang',
        ];

        $penjualan = PenjualanModel::find($id)->load('detail.barang');

        $activeMenu = 'penjualan';
        return view('penjualan.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'barang' => $barang, 'user' => $user, 'penjualan' => $penjualan]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->only('user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal', 'barang_id', 'jumlah', 'harga',), [
            'penjualan_kode' => ['required', 'max:50', 'unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id'],
            'penjualan_tanggal' => ['required', 'date'],
            'pembeli' => ['required', 'string', 'max:50'],
            'barang_id' => ['required', 'array', 'min:1', 'exists:m_barang,barang_id'],
            'jumlah' => ['required', 'array', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
        ]);


        if ($validator->fails()) {
            return redirect('/transaksi/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data penjualan gagal diubah terdapat kesalahan pada inputan');
        }
        $request['penjualan_tanggal'] = Carbon::createFromFormat('Y-m-d', $request->penjualan_tanggal)
            ->setTime(Carbon::now()->format('H'), Carbon::now()->format('i'), Carbon::now()->format('s'));

        try {

            DB::beginTransaction();

            $penjualan = PenjualanModel::find($id);
            $penjualan->update($request->only(['pembeli', 'penjualan_kode', 'penjualan_tanggal']));

            // Menghitung perubahan stok untuk setiap barang dalam transaksi
            foreach ($request['barang_id'] as $key => $barang_id) {
                $detail = PenjualanDetailModel::where('penjualan_id', $id)->where('barang_id', $barang_id)->first();
                $oldQuantity = $penjualan->detail->where('barang_id', $barang_id)->where('penjualan_id', $id)->first()->jumlah;

                if ($detail) {
                    // Jika detail penjualan ditemukan, update nilai-nilainya
                    $detail->jumlah = $request['jumlah'][$key];
                    $detail->harga = $request['harga'][$key];
                    $detail->save();
                } else {
                    // Jika detail penjualan tidak ditemukan, tambahkan detail baru
                    $newDetail = new PenjualanDetailModel();
                    $newDetail->penjualan_id = $penjualan->penjualan_id;
                    $newDetail->barang_id = $barang_id;
                    $newDetail->jumlah = $request['jumlah'][$key];
                    $newDetail->harga = $request['harga'][$key];
                    $newDetail->save();
                }


                $stok = StokModel::with(['barang'])->find($barang_id);


                $newQuantity = $request['jumlah'][$key];
                $quantityDifference = $oldQuantity - $newQuantity;

                // Memperbarui stok barang
                $stok->stok_jumlah += $quantityDifference;

                if ($stok->stok_jumlah < 0) {
                    throw new Exception('Stok barang tidak mencukupi');
                }

                $stok->save();
            }

            DB::commit();
            return redirect('/transaksi')->with('success', 'Data transaksi berhasil diubah');
        } catch (Exception $e) {
            DB::rollback();
            return redirect('/transaksi')->with('error', 'Data transaksi gagal diubah');
        }
    }

    public function destroy($id)
    {
        $penjualan = PenjualanModel::find($id);

        if (!$penjualan) {
            return redirect('/transaksi')->with('error', 'Data transaksi tidak ditemukan');
        }

        try {
            DB::beginTransaction();

            // Ambil semua detail transaksi yang terhubung dengan transaksi yang akan dihapus
            $penjualanDetails = PenjualanDetailModel::where('penjualan_id', $id)->get();

            // Kembalikan semua stok untuk setiap detail transaksi yang dihapus
            foreach ($penjualanDetails as $detail) {
                $stok = StokModel::find($detail->barang_id);
                $stok->stok_jumlah += $detail->jumlah;
                $stok->save();
            }

            // Hapus detail transaksi
            PenjualanDetailModel::where('penjualan_id', $id)->delete();

            // Hapus transaksi
            $penjualan->delete();

            DB::commit();

            return redirect('/transaksi')->with('success', 'Data transaksi berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return redirect('/transaksi')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
