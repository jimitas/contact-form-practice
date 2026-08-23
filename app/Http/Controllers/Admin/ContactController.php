<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchContactsRequest;
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
     * お問い合わせ一覧を検索条件で絞り込んで表示する。
     */
    public function index(SearchContactsRequest $request): View
    {
        $filters = $request->validated();

        $contacts = Contact::query()
            ->when($filters['name'] ?? null, fn ($query, $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['status'] ?? null, fn ($query, $statuses) => $query->whereIn('status', $statuses))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.contacts.index', ['contacts' => $contacts, 'filters' => $filters]);
    }

    /**
     * お問い合わせ詳細と返信履歴を表示する。
     */
    public function show(Contact $contact): View
    {
        $contact->load(['replies' => fn ($query) => $query->latest()]);

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
