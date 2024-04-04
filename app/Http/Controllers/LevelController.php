<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level']
        ];

        $page = (object) [
            'title' => 'Daftar level yang terdaftar dalam sistem',
        ];

        $activeMenu = 'level';
        return view('levels.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list()
    {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return datatables()->of($level)
            ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
            ->addColumn('aksi', function ($level) { // menambahkan kolom aksi
                $btn = '<a href="' . url('/level/' . $level->level_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="'
                    . url('/level/' . $level->level_id) . '">'
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
            'title' => 'Tambah Level',
            'list' => ['Home', 'Level', 'Create']
        ];

        $page = (object) [
            'title' => 'Tambah level baru',
        ];

        $activeMenu = 'level';
        return view('levels.create', ['breadcrumb' => $breadcrumb,'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level_kode' => 'required|string|regex:/^[A-Z]+$/|min:3|max:3|unique:m_level,level_kode,NULL,level_id',
            'level_nama' => 'required|string|min:3',
        ], [
            'level_kode.regex' => 'The level kode must be capital letter',
        ]);

        if ($validator->fails()) {
            return redirect('/level/create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data level gagal ditambahkan');
        }

        LevelModel::create($request->only(['level_kode', 'level_nama']));
        return redirect('/level')->with('success', 'Data level berhasil ditambahkan');
    }

    public function show($id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Level',
            'list' => ['Home', 'Level', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail level',
        ];

        $activeMenu = 'level';
        return view('levels.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'level' => $level]);
    }

    public function edit($id)
    {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit level',
        ];

        $activeMenu = 'level';
        return view('levels.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'level' => $level]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->only('level_kode', 'level_nama'), [
            'level_kode' => 'required|string|regex:/^[A-Z]+$/|min:3|max:3|unique:m_level,level_kode,' . $id . ',level_id',
            'level_nama' => 'required|string|min:3',
        ], [
            'level_kode.regex' => 'The level kode must be capital letter',
        ]);

        if ($validator->fails()) {
            return redirect('/level/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Data level gagal diubah');
        }

        LevelModel::find($id)->update($request->only(['level_kode', 'level_nama']));
        return redirect('/level')->with('success', 'Data level berhasil diubah');
    }

    public function destroy($id)
    {
        $check = LevelModel::find($id);
        if (!$check) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            LevelModel::destroy($id);
            return redirect('/level')->with('success', 'Data level berhasil dihapus');

        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

}
