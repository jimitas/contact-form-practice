@extends('layouts.admin')

@section('title', '管理ログイン')

@section('content')
    <h1>管理ログイン</h1>

    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf

        <div class="form-row">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <button type="submit">ログイン</button>
        </div>
    </form>
@endsection
