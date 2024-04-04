@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ url('/transaksi/' . $penjualan->penjualan_id) }}" class="form-horizontal">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="form-group col">
                        <label class="col control-label col-form-label">Pembeli</label>
                        <div class="col">
                            <input type="text" class="form-control" id="pembeli" name="pembeli"
                                value="{{ $penjualan->pembeli ?? old('pembeli') }}" required>
                            @error('pembeli')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col">
                        <label class="col control-label col-form-label">Dibuat Oleh: </label>
                        <div class="col">
                            <input type="text" class="form-control" id="pembeli" name="pembeli"
                                value="{{$penjualan->user->nama}}" readonly>
                            @error('user_id')
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
                                value="{{ $penjualan->penjualan_kode }}" readonly>
                            @error('penjualan_kode')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="col control-label col-form-label">Tanggal</label>
                        <div class="col">
                            <input type="date" class="form-control" id="penjualan_tanggal" name="penjualan_tanggal"
                                value="{{ date('Y-m-d', strtotime($penjualan->penjualan_tanggal)) ?? date('Y-m-d') }}"
                                required>
                            @error('penjualan_tanggal')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <h5 class="pt-3">Detail Transaksi</h5>
                <hr>
                <div class="form-group detail-transaksi">
                    @foreach ($penjualan->detail as $detail)
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
                                            <option value="">Pilih Barang</option>
                                            @foreach ($barang as $b)
                                                <option value="{{ $b->barang_id }}"
                                                    @if ($b->barang_id == $detail->barang_id) selected @endif>
                                                    {{ $b->barang_nama }}
                                                </option>
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
                                        <input type="text" class="form-control jumlah" id="jumlah" name="jumlah[]"
                                            value=""
                                            data-old="" required placeholder="Min. 1">
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
                                        @php
                                            $total = $detail->barang->harga_jual * (old('jumlah') ?? $detail->jumlah);
                                        @endphp
                                        <input type="text" class="form-control total"
                                            data-old-total="" id="total" name="harga[]"
                                            value="{{ $total }}" readonly>
                                        @error('harga')
                                            <small class="form-text text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-sm-6">
                    <button class="add-new-product btn btn-primary btn-sm" type="button">Tambah Barang</button>
                </div>
                <div class="col-sm-6 pt-5">
                    <button type="submit" class="btn btn-primary btn-sm"><i
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
        let selectedId = [...@json($penjualan->detail->pluck('barang_id'))];
        let oldTransaksi = @json($penjualan);
        let oldJumlah = 0;
        let childTotal = $('.detail-transaksi').children().length;
        let totalBarang = $('.barang').children().length - 1;

        $(window).on('load', function() {
            $('.barang').trigger('change');
            childTotal = $('.detail-transaksi').children().length;
            totalBarang = Math.floor(($('.barang').children().length - 1) / childTotal);
        });

        $(document).ready(function() {

            checkChildCount();

            $(document).on('change', '.barang', async function() {
                let element = $(this);
                let id = $(this).val();

                if (id) {
                    barang = await fetchData(id);
                }

                showData(barang, element);

                if (!(selectedId.includes(id))) {
                    selectedId.push(id);
                }

                disableSelectedOption(selectedId, id);
                disabledAddButton(childTotal, totalBarang);

                $(this).closest('.form-barang').find('.jumlah').trigger('change');
            });

            $(document).on('change', '.jumlah', function() {
                let oldValue = $(this).data('old') || 0;
                let newValue = $(this).val() || 0;
                let oldTotal = $(this).closest('.form-barang').find('.total').data('old-total') || 0;
                let difference = newValue - oldValue;

                if (!isNaN(newValue) && validateQuantityChange($(this), difference)) {
                    updateTotalPrice($(this), difference, oldValue);
                } else {
                    displayQuantityError($(this), difference);
                }
            });

            $(document).on('click', '.add-new-product', function() {
                $('.detail-transaksi').append(card_detail());
                checkChildCount();
            });
        });

        const validateQuantityChange = (element, difference) => {
            let stok = element.closest('.form-barang').find('.stok').data('stok');

            if (difference < stok) {
                element.closest('.form-barang').find('.stok').removeClass('is-invalid');
                element.closest('.form-barang').find('.jumlah-validation').text('');
                $('.add-new-product').prop('disabled', false);

                return true;
            }
        }

        const displayQuantityError = (element, difference) => {
            let stok = element.closest('.form-barang').find('.stok').data('stok');

            if (difference > stok) {
                element.closest('.form-barang').find('.stok').addClass('is-invalid');
                element.closest('.form-barang').find('.jumlah-validation').text('Jumlah melebihi stok');
                $('.add-new-product').prop('disabled', true);

                return false;
            }
        }

        const disabledAddButton = (card, totalBarang) => {
            if (card == totalBarang) {
                $('.add-new-product').hide();
            } else {
                $('.add-new-product').show();
            }
        }

        const updateTotalPrice = (element, difference, oldValue) => {
            let total = element.closest('.form-barang').find('.harga').val() * (Math.abs(difference) === 0 ? oldValue :
                difference + oldValue);

            element.closest('.form-barang').find('.total').val(total);
        }

        const disableSelectedOption = (selectedId, barangId) => {
            $('.barang option[value="' + barangId + '"]').not(':disabled').hide();
        }

        const enableSelectedOption = (id) => {
            $('.barang option[value="' + id + '"]').show();
        }

        const deleteCard = (element) => {
            selectedId = selectedId.filter(id => id !== element.id);
            $(element).closest('.form-barang').remove();
            enableSelectedOption(element.id);
            checkChildCount();
            disabledAddButton(childTotal, totalBarang);
        }

        const fetchData = async (id) => {
            try {
                let response = await $.get(`/api/barang/${id}`, function(response) {
                    return response.data;
                });

                return response;
            } catch (error) {
                console.log(error);
            }
        }

        const showData = (item, element) => {
            let jumlah = oldTransaksi.detail.find(detail => detail.barang_id == item.data.barang_id);
            element.closest('.form-barang').find('.stok').text(`(${item.data.barang_stok[0].sisa} tersedia)`)
                .data('stok', item.data.barang_stok[0].sisa);
            element.closest('.form-barang').find('.harga').val(item.data.barang_harga);
            element.closest('.form-barang').find('.jumlah').val(jumlah ? jumlah.jumlah : 1);
            element.closest('.form-barang').find('.delete-card-button').attr('id', item.data.barang_id);

            disabledJumlah(element, item.data.barang_stok[0].sisa);
            updateTotalPrice(element, 0, (jumlah ? jumlah.jumlah : 1));
        }

        const disabledJumlah = (element, stok) => {
            if (stok === 0) {
                element.closest('.form-barang').find('.stok').addClass('is-invalid');
                element.closest('.form-barang').find('.jumlah-validation').text('Stok habis');
                // element.closest('.form-barang').find('.jumlah').prop('disabled', true);
            }
        }

        const card_detail = () => {
            $('.add-new-product').prop('disabled', true);
            let newCard = $('.form-barang').eq(0).clone();
            newCard.find('input').val('');
            newCard.find('.barang').val('');
            newCard.find('.stok').text('');
            return newCard;
        }

        const isNumber = (input) => {
            let regex = /[^0-9]/g;
            return !regex.test(input);
        }

        const checkChildCount = () => {
            childTotal = $('.detail-transaksi').children().length;
            if (childTotal <= 1) {
                $('.delete-card-button').prop('disabled', true);
            } else {
                $('.delete-card-button').prop('disabled', false);
            }
        }
    </script>
@endpush
