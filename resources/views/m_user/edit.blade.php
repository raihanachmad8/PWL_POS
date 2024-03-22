@extends('layouts.app')

@section('subtitle', 'Edit User')
@section('content_header_title', 'User')
@section('content_header_subtitle', 'Edit User')

@section('content')
    <div class="col">
        <!-- general form elements -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Ubah Data User</h3>
            </div>
            <form action="/m_user/{{ $useri->user_id }}" method="POST">
                @method('PUT')
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" placeholder="Masukkan Nama" value="{{ $useri->nama }}">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" name="username" placeholder="Masukkan Username"
                                value="{{ $useri->username }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Password" value="{{ $useri->password }}">
                        </div>
                        <div class="col-sm-6">
                            <!-- select -->
                            <div class="form-group">
                                <label for="level">Level</label>
                                <select class="form-control @error('level_id') is-invalid @enderror" name="level_id"
                                    id="level_id">
                                    <option value="" disabled>Pilih Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->level_id }}"
                                            @if ($level->level_id == $useri->level_id) selected @endif>{{ $level->level_nama}}
                                        </option>
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
