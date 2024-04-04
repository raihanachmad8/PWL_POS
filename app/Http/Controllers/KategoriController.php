<?php

namespace App\Http\Controllers;

use App\DataTables\KategoriDataTable;
use App\Http\Requests\StoreRequest;
use App\Models\KategoriModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem',
        ];

        $activeMenu = 'kategori';
        return view('kategoris.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        return datatables()->of($kategori)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($kategori) { // menambahkan kolom aksi
                $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'
                    . url('/kategori/' . $kategori->kategori_id) . '">'
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
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Create']
        ];

        $page = (object) [
            'title' => 'Tambah kategori baru',
        ];

        $activeMenu = 'kategori';
        return view('kategoris.create', ['breadcrumb' => $breadcrumb,'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_kode' => 'required|string|regex:/^[A-Z]+$/|min:3|max:3|unique:m_kategori,kategori_kode,NULL,kategori_id',
            'kategori_nama' => 'required|string|min:3',
        ], [
            'kategori_kode.regex' => 'The kategori kode must be capital letter',
        ]);

        if ($validator->fails()) {
            return redirect('/kategori/create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data kategori gagal ditambahkan');
        }

        KategoriModel::create($request->only(['kategori_kode', 'kategori_nama']));
        return redirect('/kategori')->with('success', 'Data kategori berhasil ditambahkan');
    }

    public function show($id)
    {
        $kategori = KategoriModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Kategori',
            'list' => ['Home', 'Kategori', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail kategori',
        ];

        $activeMenu = 'kategori';
        return view('kategoris.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'kategori' => $kategori]);
    }

    public function edit($id)
    {
        $kategori = KategoriModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit kategori',
        ];

        $activeMenu = 'kategori';
        return view('kategoris.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'kategori' => $kategori]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->only('kategori_kode', 'kategori_nama'), [
            'kategori_kode' => 'required|string|regex:/^[A-Z]+$/|min:3|max:3|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
            'kategori_nama' => 'required|string|min:3',
        ], [
            'kategori_kode.regex' => 'The kategori kode must be capital letter',
        ]);

        if ($validator->fails()) {
            return redirect('/kategori/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data kategori gagal diubah');
        }

        KategoriModel::find($id)->update($request->only(['kategori_kode', 'kategori_nama']));
        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
    }

    public function destroy($id)
    {
        $check = KategoriModel::find($id);
        if (!$check) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        try {
            KategoriModel::destroy($id);
            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');

        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
