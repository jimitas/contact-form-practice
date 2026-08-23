@extends('layouts.app')

@section('title', '入力内容の確認')

@section('content')
    <h1>入力内容の確認</h1>

    <dl class="review">
        <dt>お名前</dt>
        <dd>{{ $input['name'] }}</dd>

        <dt>メールアドレス</dt>
        <dd>{{ $input['email'] }}</dd>

        <dt>件名</dt>
        <dd>{{ $input['subject'] }}</dd>

        <dt>本文</dt>
        <dd>{{ $input['body'] }}</dd>
    </dl>

    <div class="buttons">
        <a class="button-link" href="{{ route('contact.create') }}">戻る</a>

        <form method="POST" action="{{ route('contact.store') }}" style="display: inline;">
            @csrf
            <button type="submit">送信する</button>
        </form>
    </div>
@endsection
