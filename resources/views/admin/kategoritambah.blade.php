{{-- -@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Kategori</h4>
                        <form class="forms-sample" action="{{ url('admin/kategori/simpan') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama Kategori</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    placeholder="Masukkan nama kategori" required>
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