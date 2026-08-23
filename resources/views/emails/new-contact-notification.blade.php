新しいお問い合わせを受け付けました。

お名前: {{ $contact->name }}
メールアドレス: {{ $contact->email }}
件名: {{ $contact->subject }}

本文:
----------------------------------------
{{ $contact->body }}
----------------------------------------

詳細はこちらから確認できます。
{{ route('admin.contacts.show', $contact) }}
