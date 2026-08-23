<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'お問い合わせフォーム')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Hiragino Kaku Gothic ProN", "Yu Gothic", sans-serif;
            max-width: 720px;
            margin: 40px auto;
            padding: 0 16px;
            color: #222;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f5f5f5;
            width: 8em;
        }
        dl.review dt {
            font-weight: bold;
            margin-top: 12px;
        }
        dl.review dd {
            margin: 4px 0 0 0;
            white-space: pre-wrap;
        }
        .form-row {
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 4px;
        }
        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
            font-size: 1rem;
        }
        .error {
            color: #c0392b;
            font-size: 0.9rem;
            margin-top: 4px;
        }
        .flash {
            background: #eafaf1;
            border: 1px solid #2ecc71;
            padding: 8px 12px;
            margin-bottom: 16px;
        }
        .buttons {
            margin-top: 24px;
        }
        button, .button-link {
            display: inline-block;
            padding: 8px 20px;
            font-size: 1rem;
            cursor: pointer;
        }
        nav {
            margin-bottom: 24px;
            font-size: 0.9rem;
        }
        nav a {
            margin-right: 12px;
        }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('contact.create') }}">お問い合わせフォーム</a>
        <a href="{{ route('admin.contacts.index') }}">管理ページ</a>
    </nav>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    @yield('content')
</body>
</html>
