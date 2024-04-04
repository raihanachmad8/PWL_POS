<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok Barang',
            'list' => ['Home', 'Stok Barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang terdaftar dalam sistem',
        ];

        $user = UserModel::all();
        $barang = BarangModel::all();

        $activeMenu = 'stok';
        return view('stok.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'barang' => $barang, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $stok = StokModel::select('stok_id', 'barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal')
            ->with(['barang', 'user']);

        if ($request->filled('barang_id')) {
            $stok->where('barang_id', $request->barang_id);
        }

        if ($request->filled('user_id')) {
            $stok->where('user_id', $request->user_id);
        }

        return datatables()->of($stok)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($stok) { // menambahkan kolom aksi
                $btn = '<a href="' . url('/stok/' . $stok->stok_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/stok/' . $stok->stok_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'
                    . url('/stok/' . $stok->stok_id) . '">'
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
            'title' => 'Tambah Stok Barang',
            'list' => ['Home', 'Stok Barang', 'Create']
        ];

        $page = (object) [
            'title' => 'Tambah stok barang baru',
        ];

        $barang = BarangModel::all();
        $user = UserModel::all();

        $activeMenu = 'stok';
        return view('stok.create', ['breadcrumb' => $breadcrumb,'page' => $page, 'barang' => $barang, 'user' => $user, 'activeMenu' => $activeMenu]);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only('barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal'), [
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_jumlah' => 'required|numeric|min:0',
            'stok_tanggal' => 'required|date',
        ]);

        $request['stok_tanggal'] = Carbon::createFromFormat('Y-m-d', $request->stok_tanggal)
        ->setTime(Carbon::now()->format('H'), Carbon::now()->format('i'), Carbon::now()->format('s'));


        if ($validator->fails()) {
            return redirect('/stok/create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data stok gagal ditambahkan');
        }
        $barang = BarangModel::find($request->barang_id);
        $user = UserModel::find($request->user_id);

            $stok = StokModel::find($request->barang_id)->where('user_id', $request->user_id)->where('user_id', $request->user_id)->first();
            if ($stok) {
                $request['stok_jumlah'] += $stok->stok_jumlah;
                $stok->update($request->only(['barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal']));
            } else {
                StokModel::create($request->only(['barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal']));
            }
        return redirect('/stok')->with('success', 'Data stok berhasil ditambahkan');
    }

    public function show($id)
    {
        $stok = StokModel::find($id)->load('barang', 'user');

        $breadcrumb = (object) [
            'title' => 'Detail Stok Barang',
            'list' => ['Home', 'Stok Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail stok barang',
        ];

        $activeMenu = 'stok';
        return view('stok.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'stok' => $stok]);
    }

    public function edit($id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::all();
        $user = UserModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit Stok Barang',
            'list' => ['Home', 'Stok Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit stok barang',
        ];

        $activeMenu = 'stok';
        return view('stok.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'barang' => $barang, 'user' => $user, 'stok' => $stok]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->only('barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal'), [
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_jumlah' => 'required|numeric|min:0',
            'stok_tanggal' => 'required|date',
        ]);

        $request['stok_tanggal'] = Carbon::createFromFormat('Y-m-d', $request->stok_tanggal)
        ->setTime(Carbon::now()->format('H'), Carbon::now()->format('i'), Carbon::now()->format('s'));


        if ($validator->fails()) {
            return redirect('/stok/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data stok gagal diubah');
        }

        StokModel::find($id)->update($request->only(['barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal']));
        return redirect('/stok')->with('success', 'Data stok berhasil diubah');
    }

    public function destroy($id)
    {
        $check = StokModel::find($id);
        if (!$check) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        try {
            StokModel::destroy($id);
            return redirect('/stok')->with('success', 'Data stok berhasil dihapus');

        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/stok')->with('error', 'Data stok gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
