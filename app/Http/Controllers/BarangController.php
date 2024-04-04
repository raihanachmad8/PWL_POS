<?php

namespace App\Http\Controllers;

use App\Http\Resources\BarangResource;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object) [
            'title' => 'Daftar barang yang terdaftar dalam sistem',
        ];

        $kategori = KategoriModel::all();

        $activeMenu = 'barang';
        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $barang = BarangModel::select('barang_id', 'kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')->with('kategori');

        if (request()->kategori_id) {
            $barang->where('kategori_id', request()->kategori_id);
        }

        return datatables()->of($barang)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($barang) { // menambahkan kolom aksi
                $btn = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'
                    . url('/barang/' . $barang->barang_id) . '">'
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
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Create']
        ];

        $page = (object) [
            'title' => 'Tambah barang baru',
        ];

        $kategori = KategoriModel::all();

        $activeMenu = 'barang';
        return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual'), [
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|regex:/^BRG\d{3}$/|min:6|max:6|unique:m_barang,barang_kode,NULL,barang_id',
            'barang_nama' => 'required|string|min:3',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ], [
            'barang_kode.regex' => 'The kategori kode must be start with BRG and followed by 3 digits number',
        ]);

        if ($validator->fails()) {
            return redirect('/barang/create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data barang gagal diubah');
        }



        BarangModel::create($request->only(['kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual']));
        return redirect('/barang')->with('success', 'Data barang berhasil ditambahkan');
    }

    public function show($id)
    {
        $barang = BarangModel::find($id)->load('kategori');

        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail barang',
        ];

        $activeMenu = 'barang';
        return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'barang' => $barang]);
    }

    public function edit($id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit barang',
        ];

        $activeMenu = 'barang';
        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'barang' => $barang, 'kategori' => $kategori]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->only('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual'), [
            'kategori_id' => 'required|exists:m_kategori,kategori_id',
            'barang_kode' => 'required|string|regex:regex:/^BRG\d{3}$/|min:3|max:3|unique:m_barang,barang_kode,NULL,barang_id',
            'barang_nama' => 'required|string|min:3',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ], [
            'barang_kode.regex' => 'The kategori kode must be start with BRG and followed by 3 digits number',
        ]);

        if ($validator->fails()) {
            return redirect('/barang/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data barang gagal diubah');
        }

        BarangModel::find($id)->update($request->only(['kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual']));
        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

    public function destroy($id)
    {
        $check = BarangModel::find($id);
        if (!$check) {
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            BarangModel::destroy($id);
            return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function get(int $id)
    {
        $barang = BarangModel::with([
            'stok' => function ($query) {
                $query->orderBy('stok_tanggal', 'desc')->first();
            }
        ])->find($id);


        return [
            'data' => [

                'barang_id' => $barang->barang_id,
                'barang_nama' => $barang->barang_nama,
                'barang_harga' => $barang->harga_jual,
                'barang_stok' => $barang->stok
            ]
        ];
    }
}
