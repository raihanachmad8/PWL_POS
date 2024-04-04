@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @empty($penjualan)
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
            @else
                <h2 class="text-primary font-weight-bold">Transaksi : {{ $penjualan->penjualan_kode }}</h2>
                <div class="rounded p-4 bg-black mt-3">
                    <div class="row">
                        <div class="col">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="h2 text-primary font-weight-bold">Dokoni Store</th>
                                        <td class="text-right">Jl. Ayam Goreng Upin Ipin No. 123</td>
                                    </tr>
                                    <tr>
                                        <th class="h6">dokonistore@businesses.com</th>
                                        <td class="text-right">Kota Malang</td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td class="text-right">(082) 19283197</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="rounded p-4 mt-3 bg-light">
                    <div class="row">
                        <div class="col">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th class="h4 font-weight-bold">Transaksi</th>
                                        <td class="text-right h4 font-weight-bold">Pegawai</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $penjualan->pembeli }}</td>
                                        <td class="text-right">Dibuat oleh: <span
                                                class="font-weight-bold">{{ $penjualan->user->nama }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $penjualan->penjualan_kode }}</td>
                                        <td class="text-right">Sebagai: <span
                                                class="font-weight-bold">{{ $penjualan->user->level->level_name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{ date('d-m-Y - H:i:s', strtotime($penjualan->penjualan_tanggal)) }}</td>
                                        @if ($penjualan->updated_by != null)
                                            <td class="text-right">Diedit oleh: <span
                                                    class="font-weight-bold">{{ $penjualan->updatedBy->nama }}</span>
                                            </td>
                                        @endif
                                    </tr>
                                    @if ($penjualan->updated_by != null)
                                        <tr>
                                            <td></td>
                                            <td class="text-right">Diedit pada: <span
                                                    class="font-weight-bold">{{ $penjualan->updated_at }}<span
                                                        class="font-weight-bold"></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="rounded p-4 mt-3 bg-light">
                    <div class="row">
                        <div class="col">
                            <h5 class="font-weight-bold">Detail Transaksi</h5>
                            <p class="text-muted">Detail pembelian yang sudah dilakukan</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table class="table table-borderless overflow-auto">
                                <thead>
                                    <tr class="border-bottom">
                                        <th scope="col">Barang</th>
                                        <th scope="col" class="text-center">Jumlah Barang</th>
                                        <th scope="col" class="text-center">Harga Satuan</th>
                                        <th scope="col" class="text-center">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penjualan->detail as $detail)
                                        <tr>
                                            <td>{{ $detail->barang->barang_nama }}</td>
                                            <td class="text-center">{{ $detail->jumlah }}</td>
                                            <td class="text-center">{{ $detail->barang->harga_jual }}</td>
                                            <td class="text-center">{{ $detail->harga }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3 overflow-auto">
                        <div class="col ">
                            <div class="col-5 float-right justify-content-center items-align-center d-flex">
                                <div class="col-9 pr-4">
                                    <table class="table table-md table-borderless">
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                @php
                                                    $subtotal = $penjualan->detail->sum('harga');
                                                @endphp
                                                <td class="text-right">{{ $subtotal }}</td>
                                            </tr>
                                            <tr>
                                                <th>Diskon</th>
                                                <td class="text-right">-</td>
                                            </tr>
                                            <tr>
                                                <th>Pajak</th>
                                                <td class="text-right">-</td>
                                            </tr>
                                            <tr class="border-top">
                                                <th>Total</th>
                                                <td class="text-right font-weight-bold">{{ $subtotal }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endempty
            <a href="{{ url('transaksi') }}" class="btn btn-sm btn-primary mt-2 float-right"><i
                    class="bi bi-arrow-90deg-left mr-2"></i>Kembali</a>
        </div>
    </div>
@endsection

@push('css')
@endpush
@push('js')
@endpush
