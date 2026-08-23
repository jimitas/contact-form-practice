@extends('layouts.admin')

@section('title', '管理ログイン')

@section('content')
    <h1>管理ログイン</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="buttons">
        <a class="button-link" href="{{ route('admin.auth.google.redirect') }}">Googleでログインする</a>
    </div>
@endsection
