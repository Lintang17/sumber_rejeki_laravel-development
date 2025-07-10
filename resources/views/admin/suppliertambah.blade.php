{{-- @extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Supplier</h4>
                        <form class="forms-sample" action="{{ url('admin/supplier/simpan') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama Supplier</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    placeholder="Masukkan nama supplier" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" name="alamat" id="alamat" rows="3"
                                    placeholder="Masukkan alamat supplier" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Nomor Telepon</label>
                                <input type="tel" class="form-control" name="telepon" id="telepon"
                                    placeholder="08xxxxxxxxxx" required>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
@endsection
--}}