@extends('layouts.app')

@section('title', '送信完了')

@section('content')
    <h1>お問い合わせありがとうございました</h1>

    <p><a href="{{ route('contact.create') }}">お問い合わせフォームに戻る</a></p>
@endsection
