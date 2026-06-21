@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-10 offset-md-1 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Barang Masuk Showroom</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('admin/showroomsimpan') }}" method="POST" id="form-showroom">
                        @csrf

                        <div class="form-group">
                            <label for="idproduksi">Pilih Produk Produksi</label>
                                <select name="idproduksi" id="idproduksi" class="form-control" required>
                                    <option value="">-- Pilih Produk Produksi --</option>
                                    @foreach ($produksi as $item)
                                        @php
                                            $disabled = in_array($item->idproduksi, $produkYangSudahMasuk);
                                        @endphp

                                        <option value="{{ $item->idproduksi }}"
                                            {{ $disabled ? 'disabled' : '' }}
                                            style="{{ $disabled ? 'color:#999; background-color:#f2f2f2;' : '' }}">
                                         {{ $item->namaproduk }}
                                            | Stok: {{ $item->stok }} pcs
                                            | Selesai: {{ date('d-m-Y', strtotime($item->tanggalselesai)) }}

                                            {{ $disabled ? '(SUDAH DI SHOWROOM)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            <small id="peringatan-duplicate" class="text-danger mt-2" style="display: none;">
                                ⚠️ Produk ini sudah pernah ditambahkan ke showroom!
                            </small>
                            <small id="peringatan-stokhabis" class="text-warning mt-2" style="display: none;">
                                ⚠️ Stok produksi untuk produk ini kosong!
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="tanggalmasuk">Tanggal Masuk Showroom</label>
                            <input type="date" class="form-control" name="tanggalmasuk" required>
                        </div>

                        <button type="submit" class="btn btn-success" id="btnSimpan">Simpan</button>
                        <a href="{{ url('admin/showroomdaftar') }}" class="btn btn-light">Kembali</a>
                    </form>

                    {{-- Modal konfirmasi jika produk sudah masuk showroom --}}
                    <div class="modal fade" id="konfirmasiModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Produk Sudah Masuk Showroom</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            Produk ini sudah pernah ditambahkan ke showroom. Apakah Anda yakin ingin menambahkan lagi?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="lanjutSubmit">Lanjutkan</button>
                          </div>
                        </div>
                      </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const produkDuplikat = @json($produkYangSudahMasuk); // dari controller
    const stokProduksiMap = @json($produksi->pluck('stok', 'idproduksi')); // Map id => stok

    let selectedId = null;

    $('#idproduksi').on('change', function () {
        selectedId = parseInt($(this).val());

        // 1. Duplikat check
        if (produkDuplikat.includes(selectedId)) {
            $('#peringatan-duplicate').show();
        } else {
            $('#peringatan-duplicate').hide();
        }

        // 2. Cek stok habis
        const stok = stokProduksiMap[selectedId] || 0;
        if (stok <= 0) {
            $('#peringatan-stokhabis').show();
        } else {
            $('#peringatan-stokhabis').hide();
        }

        // 3. Enable tombol simpan jika ada produk terpilih
        $('#btnSimpan').prop('disabled', false);
    });

    $('#form-showroom').on('submit', function (e) {
        selectedId = parseInt($('#idproduksi').val());
        if (produkDuplikat.includes(selectedId)) {
            e.preventDefault(); // hentikan form submit
            $('#konfirmasiModal').modal('show'); // tampilkan modal
        }
    });

    $('#lanjutSubmit').on('click', function () {
        $('#konfirmasiModal').modal('hide');
        $('#form-showroom')[0].submit(); // lanjutkan submit
    });
</script>
@endpush
