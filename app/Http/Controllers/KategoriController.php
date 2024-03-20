<?php

namespace App\Http\Controllers;

use App\DataTables\KategoriDataTable;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index(KategoriDataTable $dataTable) {
        // $data = [
        //     'kategori_kode' => 'SNK',
        //     'kategori_nama' => 'Snack/Makanan Ringan',
        //     'created_at' => now()
        // ];
        // DB::table('m_kategori')->insert($data);
        // return 'Insert data baru berhasil';

        // $row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->update(['kategori_nama' => 'Camilan']);
        // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';

        // $row = DB::table('m_kategori')->where('kategori_kode', 'SNK')->delete();
        // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';

        // $data = DB::table('m_kategori')->get();
        // return view('kategori', ['data' => $data]);

        return $dataTable->render('kategori.index');
    }

    public function create() {
        return view('kategori.create');
    }

    public function store(Request $request) {
        // KategoriModel::create([
        //         'kategori_kode' => $request->kategori_kode,
        //         'kategori_nama' => $request->kategori_nama
        //     ]);

        $validated = $request->validate([
            'kategori_kode' => 'bail|required|max:3|unique:m_kategori',
            'kategori_nama' => 'required|max:50'
        ]);

        KategoriModel::create($validated);

        return redirect('/kategori');
    }

    public function edit($id) {
        $data = KategoriModel::find($id);
        return view('kategori.edit', ['data' => $data]);
    }

    public function update(Request $request, $id) {
        $data = KategoriModel::find($id);
        $data->kategori_kode = $request->kategori_kode;
        $data->kategori_nama = $request->kategori_nama;
        $data->save();
        return redirect('/kategori');
    }

    public function destroy($id) {
        KategoriModel::destroy($id);
        return redirect('/kategori');
    }
}
