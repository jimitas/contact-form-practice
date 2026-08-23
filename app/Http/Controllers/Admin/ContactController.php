<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateContactStatusRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * 管理ページでのお問い合わせ一覧・詳細・ステータス管理を扱うコントローラー。
 */
class ContactController extends Controller
{
    /**
     * お問い合わせ一覧を表示する。
     */
    public function index(): View
    {
        $contacts = Contact::latest()->paginate(20);

        return view('admin.contacts.index', ['contacts' => $contacts]);
    }

    /**
     * お問い合わせ詳細を表示する。
     */
    public function show(Contact $contact): View
    {
        return view('admin.contacts.show', ['contact' => $contact]);
    }

    /**
     * お問い合わせのステータスを更新する。
     */
    public function updateStatus(UpdateContactStatusRequest $request, Contact $contact): RedirectResponse
    {
        $contact->update(['status' => $request->validated('status')]);

        return back()->with('status', 'ステータスを更新しました。');
    }
}
