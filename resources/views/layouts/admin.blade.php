<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '管理ページ')</title>
    @include('layouts.partials.styles')
</head>
<body>
    <nav>
        @auth
            <a href="{{ route('admin.contacts.index') }}">お問い合わせ一覧</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        @endauth
    </nav>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @yield('content')
</body>
</html>
