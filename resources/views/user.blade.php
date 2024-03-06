<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data User</title>
</head>
<body>
    <h1>Data Level Pengguna</h1>
    <a href="{{ route('/user/tambah') }}">Tambah User</a>
    <table border="1" cellpadding="2" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>ID Level Pengguna</th>
            <th>Kode Level</th>
            <th>Nama Level</th>
            <th>Aksi</th>
        </tr>
        @foreach($data as $d)
        <tr>
            <td>{{ $d->user_id }}</td>
            <td>{{ $d->username}}</td>
            <td>{{ $d->nama}}</td>
            <td>{{ $d->level_id}}</td>
            <td>{{ $d->level->level_kode}}</td>
            <td>{{ $d->level->level_nama}}</td>
            <td><a href="{{ route('/user/ubah', $d->user_id) }}">Ubah</a> | <a href="{{ route('/user/hapus', $d->user_id) }}">Hapus</a></td>
        </tr>
        @endforeach
    </table>
</body>
</html>
