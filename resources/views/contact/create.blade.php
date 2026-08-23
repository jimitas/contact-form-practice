@extends('layouts.app')

@section('title', 'お問い合わせフォーム')

@section('content')
    <h1>お問い合わせフォーム</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.confirm') }}">
        @csrf

        <div class="form-row">
            <label for="name">お名前</label>
            <input type="text" id="name" name="name" value="{{ old('name', $input['name'] ?? '') }}">
        </div>

        <div class="form-row">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email', $input['email'] ?? '') }}">
        </div>

        <div class="form-row">
            <label for="subject">件名</label>
            <input type="text" id="subject" name="subject" value="{{ old('subject', $input['subject'] ?? '') }}">
        </div>

        <div class="form-row">
            <label for="body">本文</label>
            <textarea id="body" name="body" rows="8">{{ old('body', $input['body'] ?? '') }}</textarea>
        </div>

        <div class="buttons">
            <button type="submit">確認画面へ</button>
        </div>
    </form>
@endsection
