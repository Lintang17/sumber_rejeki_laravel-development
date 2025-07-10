{{-- @extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Ubah Supplier</h4>

                        @if (session('success'))
                            <div class="alert alert-success">
                            {{ session('success') }}</div>
                        @endif

                        <form class="forms-sample" method="POST" action="{{ url('admin/supplier/update', $supplier->idsupplier) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="nama">Nama Supplier</label>
                                <input type="text" name="nama" value="{{ $supplier->nama }}" class="form-control" name="nama" required>
                            </div>

                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="3" required>{{ $supplier->alamat }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="notelp">No Telepon</label>
                                <input type="text" name="telepon" value="{{ $supplier->telepon }}" class="form-control" name="notelp" required>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <a href="{{ url('admin/supplier') }}" class="btn btn-light">Batal</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
--}}