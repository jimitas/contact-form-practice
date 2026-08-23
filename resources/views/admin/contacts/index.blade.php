@extends('layouts.admin')

@section('title', 'お問い合わせ一覧')

@section('content')
    <h1>お問い合わせ一覧</h1>

    <form method="GET" action="{{ route('admin.contacts.index') }}">
        <div class="form-row">
            <label for="name">氏名（部分一致）</label>
            <input type="text" id="name" name="name" value="{{ old('name', $filters['name'] ?? '') }}">
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label for="date_from">受付日時（開始日）</label>
            <input type="date" id="date_from" name="date_from" value="{{ old('date_from', $filters['date_from'] ?? '') }}">
            @error('date_from')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label for="date_to">受付日時（終了日）</label>
            <input type="date" id="date_to" name="date_to" value="{{ old('date_to', $filters['date_to'] ?? '') }}">
            @error('date_to')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label>ステータス</label>
            @foreach (\App\Models\Contact::statuses() as $value => $label)
                <label>
                    <input
                        type="checkbox"
                        name="status[]"
                        value="{{ $value }}"
                        style="width: auto;"
                        @checked(in_array($value, old('status', $filters['status'] ?? []), true))
                    >
                    {{ $label }}
                </label>
            @endforeach
            @error('status')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <button type="submit">検索する</button>
            <a class="button-link" href="{{ route('admin.contacts.index') }}">クリア</a>
        </div>
    </form>

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

    {{ $contacts->links('pagination::bootstrap-4') }}
@endsection
