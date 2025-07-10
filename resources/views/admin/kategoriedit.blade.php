{{-- @extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ubah Kategori</h4>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form class="forms-sample" method="POST" action="{{ url('admin/kategori/update', $kategori->idkategori) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="nama">Nama Kategori</label>
                            <input type="text" name="nama" value="{{ $kategori->nama }}" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                        <a href="{{ url('admin/kategori') }}" class="btn btn-light">Batal</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
--}}