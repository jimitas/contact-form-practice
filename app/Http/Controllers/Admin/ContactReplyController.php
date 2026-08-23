<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContactReplyRequest;
use App\Mail\ContactReplyMail;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

/**
 * お問い合わせへの返信送信を扱うコントローラー。
 */
class ContactReplyController extends Controller
{
    /**
     * お問い合わせ者へ返信メールを送信し、履歴を保存する。
     */
    public function store(StoreContactReplyRequest $request, Contact $contact): RedirectResponse
    {
        $body = $request->validated('body');

        Mail::to($contact->email)->send(new ContactReplyMail($contact, $body));

        $contact->replies()->create(['body' => $body]);

        return back()->with('status', '返信を送信しました。');
    }
}
