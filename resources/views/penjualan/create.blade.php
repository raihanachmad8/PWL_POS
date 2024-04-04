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
            <form method="POST" action="{{ url('transaksi') }}" class="form-horizontal">
                @csrf
                <div class="row">
                    <div class="form-group col">
                        <label class="col control-label col-form-label">Pembeli</label>
                        <div class="col">
                            <input type="text" class="form-control" id="pembeli" name="pembeli"
                                value="{{ old('pembeli') }}" required>
                            @error('pembeli')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col">
                        <label class="col control-label col-form-label">Dibuat oleh: </label>
                        <div class="col">
                            <select class="form-control " id="" name="user_id" required>
                                <option value="" selected>Pilih User</option>
                                @foreach ($user as $u)
                                    <option value="{{ $u->user_id }}" @if (old('user_id') == $u->user_id)
                                        selected
                                    @endif >{{ $u->nama }}</option>
                                @endforeach
                            </select>
                            @error('pembeli')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="col control-label col-form-label">Kode Penjualan</label>
                        <div class="col">
                            <input type="text" class="form-control" id="penjualan_kode" name="penjualan_kode"
                                value="{{ $kodePenjualan }}" readonly>
                            @error('penjualan_kode')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col control-label col-form-label">Tanggal</label>
                        <div class="col">
                            <input type="date" class="form-control" id="penjualan_tanggal" name="penjualan_tanggal"
                                value="{{ old('penjualan_tanggal') ?? $today}}" required>
                            @error('penjualan_tanggal')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <h5 class="pt-3">Detail Transaksi</h5>
                <hr>
                <div class="form-group detail-transaksi">
                    <div class="form-group mt-3 form-barang bg-light p-3 rounded">
                        <div class="row">
                            <div class="col">
                                <div class="col">
                                    <button class="delete-card-button btn btn-danger btn-sm float-right"
                                        onclick="deleteCard(this)" type="button"><i class="bi bi-trash mr-2"></i>Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="col control-label col-form-label">Barang</label>
                                <div class="col">
                                    <select class="form-control barang" id="" name="barang_id[]" required>
                                        <option value="" selected>Pilih Barang</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('barang_id')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="col control-label col-form-label">Jumlah
                                    <span class="text-danger stok"></span>
                                </label>
                                <div class="col">
                                    <input type="number" class="form-control jumlah" id="jumlah" name="jumlah[]" min="1"
                                        value="" required placeholder="Min. 1">
                                    <small class="jumlah-validation text-danger"></small>
                                    @error('jumlah')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="col control-label col-form-label">Harga</label>
                                <div class="col">
                                    <input type="text" class="form-control harga" id="harga"
                                        value="" readonly>
                                    @error('harga')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="col control-label col-form-label">Total</label>
                                <div class="col">
                                    <input type="text" class="form-control total" id="total" name="harga[]"
                                        value="" readonly>
                                    @error('harga')
                                        <small class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <button class="add-new-product btn btn-primary btn-sm" type="button" disabled><i
                            class="bi bi-plus-lg mr-2"></i>Tambah Barang</button>
                </div>
                <div class="col-sm-6 pt-5">
                    <button type="submit" class="btn btn-primary btn-sm save" disabled><i
                            class="bi bi-floppy mr-2"></i>Simpan</button>
                    <a class="btn btn-sm btn-default ml-1" href="{{ url('transaksi') }}"><i
                            class="bi bi-arrow-90deg-left mr-2"></i>Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('js')
    <script>
        let barang = {};
        let selectedId = [];
        let unSelectedId = [];
        let childTotal = $('.detail-transaksi').children().length;
        let totalBarang = $('.barang').children().length - 1;

        $(window).on('load', function() {
            $('.barang').val('');
            $('.jumlah').val('');
            $('.harga').val('');
            $('.total').val('');
            $('.add-new-product').prop('disabled', true);

            $('.barang option').each(function() {
                unSelectedId.push($(this).val());
            });

            // Hapus ID yang sudah dipilih dari unSelectedId
            selectedId.forEach(id => {
                unSelectedId = unSelectedId.filter(item => item !== id);
            });
        });

        $(document).ready(function() {

            checkChildCount();

            $(document).on('change', '.barang', async function() {
                childTotal = $('.detail-transaksi').children().length;
                let element = $(this);
                let id = $(this).val();

                if (id) {
                    await fetchData(id);
                }

                showData(barang, element);

                if (!(selectedId.includes(id))) {
                    selectedId.push(id);
                }

                unSelectedId = unSelectedId.filter(item => item !== id);

                disableSelectedOption(selectedId);
                enableSelectedOption();
                disabledAddButton(childTotal, totalBarang);

                $('.save').prop('disabled', false);
                $(this).closest('.form-barang').find('.jumlah').trigger('change');
            });

            $(document).on('change', '.jumlah', function() {
                let nilaiJumlah = $(this).val();

                if (isNumber(nilaiJumlah) && parseInt(nilaiJumlah) > 0) {
                    if (parseInt(nilaiJumlah) > barang.data.barang_stok[0].sisa) {
                        $(this).closest('.form-barang').find('.jumlah').addClass('is-invalid');
                        $(this).closest('.form-barang').find('.jumlah-validation').text(
                            'Jumlah melebihi stok');
                        $('.add-new-product').prop('disabled', true);
                    } else {
                        let total = $(this).closest('.form-barang').find('.harga').val() * nilaiJumlah;
                        $(this).closest('.form-barang').find('.total').val(total);
                        $(this).closest('.form-barang').find('.jumlah').removeClass('is-invalid');
                        $('.add-new-product').prop('disabled', false);
                    }
                } else {
                    $(this).closest('.form-barang').find('.total').val(0);
                    $(this).closest('.form-barang').find('.jumlah').addClass('is-invalid');
                    $(this).closest('.form-barang').find('.jumlah-validation').text(
                        'Jumlah harus berupa angka');
                    $('.add-new-product').prop('disabled', true);
                }
            });

            $(document).on('click', '.add-new-product', function() {
                $('.detail-transaksi').append(card_detail());
                checkChildCount();
            });
        });

        const disableSelectedOption = (selectedId) => {
            $('.barang').not(':disabled').each(function() {
                selectedId.forEach(id => {
                    let option = $(this).find('option[value="' + id + '"]');
                    option.hide();
                });
            });
        }



        const enableSelectedOption = () => {
            $('.barang').not(':disabled').each(function() {
                unSelectedId.forEach(id => {
                    let option = $(this).find('option[value="' + id + '"]');
                    option.show();
                });
            });
        }

        const deleteCard = (element) => {
            selectedId = selectedId.filter(id => id !== element.id);
            unSelectedId.push(element.id);
            $(element).closest('.form-barang').remove();
            enableSelectedOption(element.id);
            checkChildCount();
            disabledAddButton(childTotal, totalBarang);
            enableSelectedOption();
        }

        const fetchData = async (id) => {
            try {
                barang = await $.get(`/api/barang/${id}`, function(response) {
                    return response.data;
                });
            } catch (error) {
                console.log(error);
            }
        }

        const showData = (item, element) => {
            element.closest('.form-barang').find('.stok').text(`(${item.data.barang_stok[0].stok_jumlah} tersedia)`);
            element.closest('.form-barang').find('.harga').val(item.data.barang_harga);
            element.closest('.form-barang').find('.jumlah').val(1);
            element.closest('.form-barang').find('.delete-card-button').attr('id', item.data.barang_id);
        }

        const card_detail = () => {
            $('.add-new-product').prop('disabled', true);
            let newCard = $('.form-barang').eq(0).clone();
            newCard.find('input').val('');
            newCard.find('.stok').text('');
            return newCard;
        }

        const isNumber = (input) => {
            let regex = /[^0-9]/g;
            return !regex.test(input);
        }

        const disabledAddButton = (card, totalBarang) => {
            if (card == totalBarang) {
                $('.add-new-product').hide();
            } else {
                $('.add-new-product').show();
            }
        }

        const checkChildCount = () => {
            childTotal = $('.detail-transaksi').children().length;
            if (childTotal == 1) {
                $('.delete-card-button').prop('disabled', true);
            } else {
                $('.delete-card-button').prop('disabled', false);
            }
        }
    </script>
@endpush
