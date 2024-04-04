@extends('layouts.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form method="POST" action="{{ url('stok') }}" class="form-horizontal">
                @csrf
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Barang</label>
                    <div class="col-11">
                        <select class="form-control" id="barang_id" name="barang_id" required>
                            <option value="">- Pilih Level -</option>
                            @foreach ($barang as $item)
                                <option value="{{ $item->barang_id }}" @if ($item->barang_id == old('barang_id')) selected @endif>
                                    {{ $item->barang_kode }}- {{ $item->barang_nama }}</option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Pengguna</label>
                    <div class="col-11">
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">- Pilih Level -</option>
                            @foreach ($user as $item)
                                <option value="{{ $item->user_id }}" @if ($item->user_id == old('user_id')) selected @endif>
                                    {{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Jumlah Stok</label>
                    <div class="col-11">
                        <input type="number" min="0" class="form-control" id="stok_jumlah" name="stok_jumlah"
                            value="{{ old('stok_jumlah') }}" required>
                        @error('stok_jumlah')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Tanggal Stok</label>
                    <div class="col-11">
                        <input type="date" class="form-control" id="stok_tanggal" name="stok_tanggal"
                            value="{{ old('stok_tanggal') }}" required>
                        @error('stok_tanggal')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label"></label>
                    <div class="col-11">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        <a class="btn btn-sm btn-default ml-1" href="{{ url('user') }}">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('js')
    <script>
        $(document).ready(function() {

            $('#stok_jumlah').on('input', function() {
                var stokJumlah = parseInt($('#stok_jumlah').val());
                if (stokJumlah < 0) {
                    $('#stok_jumlah').addClass('text-danger');
                    $('button[type="submit"]').prop('disabled', true);
                    stokJumlah = 0;
                } else {
                    $('#stok_jumlah').removeClass('text-danger');
                    $('button[type="submit"]').prop('disabled', false);
                }
            });

            $('#stok_tanggal').on('input', function() {
                let stokTanggal = $('#stok_tanggal').val();
                if (stokTanggal <= new Date().toISOString().slice(0, 16)) {
                    $('#stok_tanggal').addClass('text-danger');
                } else {
                    $('#stok_tanggal').removeClass('text-danger');
                    $('button[type="submit"]').prop('disabled', false);
                }
            });
        });
    </script>
@endpush
