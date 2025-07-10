{{-- @extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Kategori</h4>
                    {{-- Kalau mau tombol tambah kategori bisa dibuka komentar ini
                    <div class="text-right mb-3">
                        <a href="{{ url('admin/kategori/tambah') }}" class="btn btn-primary">+ Tambah Kategori</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    @if (auth()->user()->role == 'Admin')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kategori as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->nama }}</td>
                                        @if (auth()->user()->role == 'Admin')
                                            <td>
                                                <a href="{{ url('admin/kategori/edit', $value->idkategori) }}"
                                                    class="btn btn-success m-1">Edit</a>
                                                <form action="{{ url('admin/kategori/hapus', $value->idkategori) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger m-1"
                                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
--}}