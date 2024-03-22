@extends('layouts.app')

@section('subtitle', 'Tambah User')
@section('content_header_title', 'User')
@section('content_header_subtitle', 'Tambah User')

@section('content')


    <div class="col">
        <!-- general form elements -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tambah Data User</h3>
            </div>
            <form action="/m_user" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Masukkan Nama" value="{{ old('nama') }}">
                            @error('nama')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username" placeholder="Masukkan Username"
                                value="{{ old('username') }}">
                                @error('username')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Password">
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <!-- select -->
                            <div class="form-group">
                                <label for="level">Level</label>
                                <select class="form-control @error('level_id') is-invalid @enderror" name="level_id"
                                    id="level_id">
                                    <option value="" disabled selected>Pilih Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
