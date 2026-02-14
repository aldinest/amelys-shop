@extends('layouts.app')

@section('content')

<form action="{{ route('admin.store') }}" method="POST">
    @csrf

    <input type="text" name="name" placeholder="Nama">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Password">

    <select name="role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit">Simpan</button>
</form>


@endsection