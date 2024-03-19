@extends('adminlte::page')
@section('title', 'Level')
@section('content_header')
    <h1>Level</h1>
@stop
@section('content')

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Tambah Data</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-sm-6">
                        <!-- select -->
                        <div class="form-group">
                            <label>Select</label>
                            <select class="form-control">
                                @foreach ($level as $l)
                                    <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>

            </form>
        </div>
        <!-- /.card-body -->
    </div>
@stop
@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
