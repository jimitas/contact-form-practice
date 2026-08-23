@extends('layouts.admin')

@section('title', 'お問い合わせ詳細')

@section('content')
    <h1>お問い合わせ詳細</h1>

    <table>
        <tbody>
            <tr>
                <th>お名前</th>
                <td>{{ $contact->name }}</td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td>{{ $contact->email }}</td>
            </tr>
            <tr>
                <th>件名</th>
                <td>{{ $contact->subject }}</td>
            </tr>
            <tr>
                <th>本文</th>
                <td style="white-space: pre-wrap;">{{ $contact->body }}</td>
            </tr>
            <tr>
                <th>受付日時</th>
                <td>{{ $contact->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        </tbody>
    </table>

    <form method="POST" action="{{ route('admin.contacts.updateStatus', $contact) }}">
        @csrf
        @method('PATCH')

        <div class="form-row">
            <label for="status">ステータス</label>
            <select id="status" name="status">
                @foreach (\App\Models\Contact::statuses() as $value => $label)
                    <option value="{{ $value }}" @selected($contact->status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <button type="submit">更新する</button>
        </div>
    </form>

    <h1>メールで返信</h1>

    <form method="POST" action="{{ route('admin.contacts.replies.store', $contact) }}">
        @csrf

        <div class="form-row">
            <label for="body">返信本文</label>
            <textarea id="body" name="body" rows="8">{{ old('body') }}</textarea>
            @error('body')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <button type="submit">送信する</button>
        </div>
    </form>

    <h1>送信履歴</h1>

    @forelse ($contact->replies as $reply)
        <dl class="review">
            <dt>送信日時</dt>
            <dd>{{ $reply->created_at->format('Y-m-d H:i') }}</dd>

            <dt>本文</dt>
            <dd>{{ $reply->body }}</dd>
        </dl>
    @empty
        <p>まだ返信はありません。</p>
    @endforelse

    <p><a href="{{ route('admin.contacts.index') }}">一覧に戻る</a></p>
@endsection
