{{-- @extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Supplier</h4>
                    {{-- <div class="text-right mb-3">
                        <a href="{{ url('admin/supplier/tambah') }}" class="btn btn-primary">+ Tambah Supplier</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>No Telepon</th>
                                    @if (auth()->user()->role == 'Admin')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplier as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->nama }}</td>
                                        <td>{{ $value->alamat }}</td>
                                        <td>{{ $value->telepon }}</td>
                                        @if (auth()->user()->role == 'Admin')
                                            <td>
                                                <a href="{{ url('admin/supplier/edit', $value->idsupplier) }}"
                                                    class="btn btn-success m-1">Edit</a>
                                                <form action="{{ url('admin/supplier/hapus', $value->idsupplier) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger m-1"
                                                        onclick="return confirm('Yakin ingin menghapus supplier ini?')">Hapus</button>
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