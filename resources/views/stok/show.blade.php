@extends('layouts.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @empty($stok)
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover table-sm">
                    <tr>
                        <th>ID</th>
                        <td>{{ $stok->stok_id }}</td>
                    </tr>
                    <tr>
                        <th>Kode Barang</th>
                        <td>{{ $stok->barang->barang_kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $stok->barang->barang_nama }}</td>
                    </tr>
                    <tr>
                        <th>Nama Pengguna</th>
                        <td>{{ $stok->user->nama }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Stok</th>
                        <td>{{ $stok->stok_jumlah }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Stok</th>
                        <td>{{ $stok->stok_tanggal }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pembuatan</th>
                        <td>{{ $stok->created_at }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Update</th>
                        <td>{{ $stok->updated_at }}</td>
                    </tr>
                </table>
            @endempty
            <a href="{{ url('stok') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('js')
@endpush
