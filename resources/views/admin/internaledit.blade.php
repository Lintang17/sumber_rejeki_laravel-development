@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-radius:10px;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="mdi mdi-account-edit"></i>
                        Edit User
                    </h5>

                    <a href="{{ url('admin/internaldaftar') }}"
                       class="btn btn-light btn-sm"
                       style="border-radius:6px;">
                        <i class="mdi mdi-arrow-left"></i>
                        Back
                    </a>
                </div>

                <div class="card-body">

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('admin/internalupdate', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text"
                                   class="form-control"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email"
                                   class="form-control"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $user->role }}"
                                   readonly>
                            <small class="text-muted">Role tidak dapat diubah</small>
                        </div>

                        <!-- PASSWORD (CHECKBOX) -->
                        <div class="form-group">
                            <label>Password</label>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="change_password"
                                       name="change_password">
                                <label class="custom-control-label" for="change_password">
                                    Ubah password
                                </label>
                            </div>

                            <input type="password"
                                   class="form-control"
                                   id="password_field"
                                   name="password"
                                   placeholder="Masukkan password baru"
                                   disabled>
                        </div>

                        <div class="d-flex justify-content-between mt-4">

                            <a href="{{ url('admin/internaldaftar') }}"
                               class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i>
                                Back
                            </a>

                            <button type="submit"
                                    class="btn btn-success">
                                <i class="mdi mdi-content-save"></i>
                                Update
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('change_password');
    const passwordField = document.getElementById('password_field');

    checkbox.addEventListener('change', function () {
        if (this.checked) {
            passwordField.disabled = false;
            passwordField.focus();
        } else {
            passwordField.disabled = true;
            passwordField.value = '';
        }
    });
});
</script>
@endsection