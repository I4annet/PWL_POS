<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data User</title>
</head>
<body>
    <h1>Daftar User</h1>
    <a href="/user">Kembali</a>
    <br><br>
    
    <h1>Form Tambah Data User</h1>
        <form method="post" action="/user/tambah_simpan/{{ $data->user_id}}">
        {{ csrf_field()}}
        {{ method_field('PUT')}}

        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan Username" value="{{ $data->username}}">
        <br>

        <label>Nama</label>
        <input type="text" name="nama" placeholder="Masukkan Nama" value="{{ $data->nama}}">
        <br>

        <label>Password</label>
        <input type="text" name="password" placeholder="Masukkan Password" value="{{ $data->password}}">
        <br>

        <label>Level ID</label>
        <input type="number" name="level_id" placeholder="Masukkan ID Level" value="{{ $data->level_id}}">  
        <br><br>

        <input type="submit" class="btn btn-success" value="Ubah">

        </form>
</body>
</html>