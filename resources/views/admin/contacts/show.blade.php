@extends('layouts.app')

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

    <p><a href="{{ route('admin.contacts.index') }}">一覧に戻る</a></p>
@endsection
