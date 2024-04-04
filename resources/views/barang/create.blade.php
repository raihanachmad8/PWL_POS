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
            <form method="POST" action="{{ url('barang') }}" class="form-horizontal">
                @csrf
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Kategori</label>
                    <div class="col-11">
                        <select class="form-control" id="kategori_id" name="kategori_id" required>
                            <option value="">- Pilih Level -</option>
                            @foreach ($kategori as $item)
                                <option value="{{ $item->kategori_id }}" @if ($item->kategori_id == old('kategori_id')) selected @endif>
                                    {{ $item->kategori_kode }}- {{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Kode Barang</label>
                    <div class="col-11">
                        <input type="text" class="form-control" id="barang_kode" name="barang_kode"
                            value="{{ old('barang_kode') }}" required pattern="^BRG\d{3}$">
                        @error('barang_kode')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Nama Barang</label>
                    <div class="col-11">
                        <input type="text" class="form-control" id="barang_nama" name="barang_nama"
                            value="{{ old('barang_nama') }}" required>
                        @error('barang_nama')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Harga Beli</label>
                    <div class="col-11">
                        <input type="text" class="form-control" id="harga_beli" name="harga_beli"
                            value="{{ old('harga_beli') }}" required>
                        @error('harga_beli')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Harga Jual</label>
                    <div class="col-11">
                        <input type="text" class="form-control" id="harga_jual" name="harga_jual"
                            value="{{ old('harga_jual') }}" required>
                        @error('harga_jual')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label"></label>
                    <div class="col-11">
                        <button type="submit" class="btn btn-primary btn-sm" >Simpan</button>
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
        $(document).ready(function() {
            $('#harga_beli').on('input', function() {
                // Keuntungan harga jual adalah 8% dari harga beli
                var hargaBeli = parseFloat($('#harga_beli').val());
                var hargaJual = hargaBeli * 1.08;
                $('#harga_jual').val(hargaJual.toFixed(2)); // Set to 2 decimal places
                resetButton();
            });

            $('#harga_jual').on('input', function() {
                resetButton();
            });
        });

        function resetButton() {
            var hargaBeli = parseFloat($('#harga_beli').val());
            var hargaJual = parseFloat($('#harga_jual').val());
            if (hargaJual >= hargaBeli) {
                $('#harga_jual').removeClass('text-danger');
                $('button[type="submit"]').prop('disabled', false);
            } else {
                $('#harga_jual').addClass('text-danger');
                $('button[type="submit"]').prop('disabled', true);
            }
        }

    });
</script>
@endpush
