@extends('layouts.admin')

@section('title', 'お問い合わせ一覧')

@section('content')
    <h1>お問い合わせ一覧</h1>

    <table>
        <thead>
            <tr>
                <th>お名前</th>
                <th>メールアドレス</th>
                <th>件名</th>
                <th>ステータス</th>
                <th>受付日時</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contacts as $contact)
                <tr>
                    <td><a href="{{ route('admin.contacts.show', $contact) }}">{{ $contact->name }}</a></td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->subject }}</td>
                    <td>{{ $contact->status }}</td>
                    <td>{{ $contact->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">お問い合わせはまだありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $contacts->links() }}
@endsection
