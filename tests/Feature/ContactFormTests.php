<?php

namespace Tests\Feature;

use App\Mail\ContactReplyMail;
use App\Mail\NewContactNotificationMail;
use App\Models\Contact;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * docs/テストケース.md に基づく機能テスト。
 *
 * 自動化していないもの: 実ブラウザでのGoogle認可画面の操作そのもの、
 * 実際のメール受信確認（Mail::fake()でのアサーションに留める）、
 * ページネーションの見た目のサイズなど視覚的な確認。
 */
class ContactFormTests extends TestCase
{
    use RefreshDatabase;

    // ==============================
    // 1. お問い合わせフォーム（入力側）
    // ==============================

    /** 1-1-1 */
    public function test_input_form_shows_empty_fields_on_first_visit(): void
    {
        $response = $this->get('/contact');

        $response->assertOk();
        $response->assertSee('name="name" value=""', false);
        $response->assertSee('name="email" value=""', false);
    }

    /** 1-1-2, 1-3-2 */
    public function test_input_form_restores_values_after_returning_from_confirm(): void
    {
        $input = $this->validContactInput();

        $this->post('/contact/confirm', $input);
        $response = $this->get('/contact');

        $response->assertOk();
        $response->assertSee($input['name'], false);
        $response->assertSee($input['email'], false);
    }

    /** 1-2-1 */
    public function test_confirm_validation_fails_when_all_fields_are_empty(): void
    {
        $response = $this->post('/contact/confirm', []);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'body']);
    }

    /** 1-2-2 */
    public function test_confirm_validation_fails_when_name_exceeds_max_length(): void
    {
        $response = $this->post('/contact/confirm', $this->validContactInput([
            'name' => str_repeat('あ', 256),
        ]));

        $response->assertSessionHasErrors('name');
    }

    /** 1-2-3 */
    #[DataProvider('invalidEmailProvider')]
    public function test_confirm_validation_fails_for_invalid_email_format(string $invalidEmail): void
    {
        $response = $this->post('/contact/confirm', $this->validContactInput([
            'email' => $invalidEmail,
        ]));

        $response->assertSessionHasErrors('email');
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['abc'],
            ['abc@'],
            ['@example.com'],
            ['abc@@example.com'],
        ];
    }

    /** 1-2-4 */
    public function test_confirm_validation_fails_when_email_exceeds_max_length(): void
    {
        $longLocalPart = str_repeat('a', 250);

        $response = $this->post('/contact/confirm', $this->validContactInput([
            'email' => "{$longLocalPart}@example.com",
        ]));

        $response->assertSessionHasErrors('email');
    }

    /** 1-2-5 */
    public function test_confirm_validation_fails_when_subject_exceeds_max_length(): void
    {
        $response = $this->post('/contact/confirm', $this->validContactInput([
            'subject' => str_repeat('あ', 256),
        ]));

        $response->assertSessionHasErrors('subject');
    }

    /** 1-2-6 */
    public function test_confirm_validation_fails_when_body_exceeds_max_length(): void
    {
        $response = $this->post('/contact/confirm', $this->validContactInput([
            'body' => str_repeat('あ', 2001),
        ]));

        $response->assertSessionHasErrors('body');
    }

    /** 1-2-7, 1-3-1 */
    public function test_confirm_succeeds_with_valid_input_and_displays_values(): void
    {
        $input = $this->validContactInput();

        $response = $this->post('/contact/confirm', $input);

        $response->assertOk();
        $response->assertSessionHasNoErrors();
        $response->assertSee($input['name'], false);
        $response->assertSee($input['subject'], false);
    }

    /** 1-2-8: XSS対策 */
    public function test_confirm_screen_escapes_script_tags(): void
    {
        $response = $this->post('/contact/confirm', $this->validContactInput([
            'body' => '<script>alert(1)</script>',
        ]));

        $response->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;', false);
    }

    /** 1-4-1, 1-4-3 */
    public function test_store_saves_contact_and_redirects_to_thanks(): void
    {
        $input = $this->validContactInput();

        $response = $this->withSession(['contact.input' => $input])->post('/contact');

        $response->assertRedirect(route('contact.thanks'));
        $this->assertDatabaseHas('contacts', [
            'name' => $input['name'],
            'email' => $input['email'],
            'status' => Contact::STATUS_NEW,
        ]);
    }

    /** 1-4-2 */
    public function test_store_clears_session_input(): void
    {
        $input = $this->validContactInput();

        $this->withSession(['contact.input' => $input])->post('/contact');

        $this->assertNull(session('contact.input'));
    }

    /** 1-4-4 */
    public function test_store_without_session_input_redirects_with_error(): void
    {
        $response = $this->withSession(['contact.input' => null])->post('/contact');

        $response->assertRedirect(route('contact.create'));
        $response->assertSessionHasErrors('form');
        $this->assertDatabaseCount('contacts', 0);
    }

    /** 1-4-5 */
    public function test_store_sends_admin_notification_mail(): void
    {
        Mail::fake();
        config(['contact.admin_notification_email' => 'admin@example.com']);

        $input = $this->validContactInput();
        $this->withSession(['contact.input' => $input])->post('/contact');

        Mail::assertSent(NewContactNotificationMail::class, fn ($mail) => $mail->hasTo('admin@example.com'));
    }

    /** 1-4-6 */
    public function test_store_succeeds_even_if_notification_mail_fails(): void
    {
        Mail::shouldReceive('to')->andThrow(new Exception('SMTP接続エラー'));
        config(['contact.admin_notification_email' => 'admin@example.com']);

        $input = $this->validContactInput();
        $response = $this->withSession(['contact.input' => $input])->post('/contact');

        $response->assertRedirect(route('contact.thanks'));
        $this->assertDatabaseHas('contacts', ['email' => $input['email']]);
    }

    /** 1-4-7 */
    public function test_store_skips_notification_when_admin_email_not_configured(): void
    {
        Mail::fake();
        config(['contact.admin_notification_email' => null]);

        $input = $this->validContactInput();
        $this->withSession(['contact.input' => $input])->post('/contact');

        Mail::assertNothingSent();
    }

    /** 1-5-1 */
    public function test_thanks_page_shows_message(): void
    {
        $response = $this->get('/contact/thanks');

        $response->assertOk();
        $response->assertSee('お問い合わせありがとうございました');
    }

    /** 1-5-3 */
    public function test_root_redirects_to_contact_form(): void
    {
        $this->get('/')->assertRedirect('/contact');
    }

    private function validContactInput(array $overrides = []): array
    {
        return array_merge([
            'name' => 'テスト太郎',
            'email' => 'taro@example.com',
            'subject' => 'テスト件名',
            'body' => 'テスト本文です。',
        ], $overrides);
    }

    // ==============================
    // 2. 管理側
    // ==============================

    /** 2-1-1, 2-6-1 */
    public function test_guest_cannot_access_contacts_index(): void
    {
        $this->get('/admin/contacts')->assertRedirect(route('admin.login'));
    }

    /** 2-6-2 */
    public function test_guest_cannot_access_contact_detail(): void
    {
        $contact = $this->makeContact();

        $this->get("/admin/contacts/{$contact->id}")->assertRedirect(route('admin.login'));
    }

    /** 2-6-3 */
    public function test_guest_cannot_update_status(): void
    {
        $contact = $this->makeContact();

        $this->patch("/admin/contacts/{$contact->id}/status", ['status' => Contact::STATUS_RESOLVED])
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'status' => Contact::STATUS_NEW]);
    }

    /** 2-6-4 */
    public function test_guest_cannot_send_reply(): void
    {
        Mail::fake();
        $contact = $this->makeContact();

        $this->post("/admin/contacts/{$contact->id}/replies", ['body' => '返信'])
            ->assertRedirect(route('admin.login'));

        Mail::assertNothingSent();
        $this->assertDatabaseCount('contact_replies', 0);
    }

    /** 2-1-2 */
    public function test_login_screen_shows_google_login_button(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Googleでログインする');
    }

    /** 2-1-4, 2-1-7: 登録済みメールアドレスでのログイン成功とintendedへの復帰 */
    public function test_google_login_succeeds_for_registered_email_and_redirects_to_intended(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);
        $contact = $this->makeContact();

        // intended URLをセッションに保存させるため、まず未ログインで詳細ページにアクセスしておく
        $this->get("/admin/contacts/{$contact->id}");

        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => '12345',
            'email' => 'admin@example.com',
            'name' => 'Admin',
        ]));

        $response = $this->get('/admin/auth/google/callback');

        $response->assertRedirect("/admin/contacts/{$contact->id}");
        $this->assertAuthenticatedAs($user);
    }

    /** 2-1-5, 2-1-6: 未登録メールアドレスの拒否・自動作成防止 */
    public function test_google_login_rejects_unregistered_email(): void
    {
        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => '99999',
            'email' => 'unknown@example.com',
            'name' => 'Unknown',
        ]));

        $response = $this->get('/admin/auth/google/callback');

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    /** 2-1-8 */
    public function test_authenticated_user_is_redirected_away_from_login_screen(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/login')
            ->assertRedirect(route('admin.contacts.index'));
    }

    /** 2-1-9, 2-1-10 */
    public function test_logout_clears_session_and_blocks_further_access(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
        $this->get('/admin/contacts')->assertRedirect(route('admin.login'));
    }

    /** 2-2-1 */
    public function test_contacts_index_lists_newest_first(): void
    {
        $user = User::factory()->create();
        $this->makeContact(['name' => '古い問い合わせ', 'created_at' => now()->subDays(5)]);
        $this->makeContact(['name' => '新しい問い合わせ', 'created_at' => now()]);

        $response = $this->actingAs($user)->get('/admin/contacts');

        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(strpos($content, '新しい問い合わせ') < strpos($content, '古い問い合わせ'));
    }

    /** 2-2-2 */
    public function test_contacts_index_paginates_by_twenty(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 25; $i++) {
            $this->makeContact(['name' => "問い合わせ{$i}"]);
        }

        $response = $this->actingAs($user)->get('/admin/contacts');

        $response->assertOk();
        $response->assertSee('class="pagination"', false);
    }

    /** 2-2-5 */
    public function test_contacts_index_shows_empty_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/contacts');

        $response->assertSee('お問い合わせはまだありません。');
    }

    /** 2-2-6 */
    public function test_contacts_index_links_to_detail_page(): void
    {
        $user = User::factory()->create();
        $contact = $this->makeContact();

        $response = $this->actingAs($user)->get('/admin/contacts');

        $response->assertSee(route('admin.contacts.show', $contact), false);
    }

    /** 2-3-1 */
    public function test_search_filters_by_partial_name(): void
    {
        $user = User::factory()->create();
        $this->makeContact(['name' => '山田太郎']);
        $this->makeContact(['name' => '佐藤次郎']);

        $response = $this->actingAs($user)->get('/admin/contacts?name=山田');

        $response->assertSee('山田太郎');
        $response->assertDontSee('佐藤次郎');
    }

    /** 2-3-2, 2-3-3, 2-3-4 */
    public function test_search_filters_by_date_range(): void
    {
        $user = User::factory()->create();
        $this->makeContact(['name' => '対象外_古い', 'created_at' => '2026-01-01']);
        $this->makeContact(['name' => '対象_期間内', 'created_at' => '2026-01-10']);
        $this->makeContact(['name' => '対象外_新しい', 'created_at' => '2026-01-20']);

        $response = $this->actingAs($user)->get('/admin/contacts?'.http_build_query([
            'date_from' => '2026-01-05',
            'date_to' => '2026-01-15',
        ]));

        $response->assertSee('対象_期間内');
        $response->assertDontSee('対象外_古い');
        $response->assertDontSee('対象外_新しい');
    }

    /** 2-3-5 */
    public function test_search_validation_fails_when_date_to_before_date_from(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/contacts?'.http_build_query([
            'date_from' => '2026-02-01',
            'date_to' => '2026-01-01',
        ]));

        $response->assertSessionHasErrors('date_to');
    }

    /** 2-3-6 */
    public function test_search_filters_by_multiple_statuses(): void
    {
        $user = User::factory()->create();
        $this->makeContact(['name' => '新規分', 'status' => Contact::STATUS_NEW]);
        $this->makeContact(['name' => '対応中分', 'status' => Contact::STATUS_IN_PROGRESS]);
        $this->makeContact(['name' => '解決済み分', 'status' => Contact::STATUS_RESOLVED]);

        $response = $this->actingAs($user)->get('/admin/contacts?'.http_build_query([
            'status' => [Contact::STATUS_NEW, Contact::STATUS_IN_PROGRESS],
        ]));

        $response->assertSee('新規分');
        $response->assertSee('対応中分');
        $response->assertDontSee('解決済み分');
    }

    /** 2-3-7 */
    public function test_search_combines_conditions_with_and(): void
    {
        $user = User::factory()->create();
        $this->makeContact(['name' => '山田太郎', 'status' => Contact::STATUS_NEW, 'created_at' => '2026-01-10']);
        $this->makeContact(['name' => '山田花子', 'status' => Contact::STATUS_RESOLVED, 'created_at' => '2026-01-10']);
        $this->makeContact(['name' => '佐藤次郎', 'status' => Contact::STATUS_NEW, 'created_at' => '2026-01-10']);

        $response = $this->actingAs($user)->get('/admin/contacts?'.http_build_query([
            'name' => '山田',
            'status' => [Contact::STATUS_NEW],
        ]));

        $response->assertSee('山田太郎');
        $response->assertDontSee('山田花子');
        $response->assertDontSee('佐藤次郎');
    }

    /** 2-3-9 */
    public function test_search_clear_link_present(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/contacts?name=test');

        $response->assertSee('クリア');
        $response->assertSee(route('admin.contacts.index'), false);
    }

    /** 2-3-10 */
    public function test_search_with_no_results_shows_empty_message(): void
    {
        $user = User::factory()->create();
        $this->makeContact(['name' => '山田太郎']);

        $response = $this->actingAs($user)->get('/admin/contacts?name=存在しない名前');

        $response->assertSee('お問い合わせはまだありません。');
    }

    /** 2-4-1 */
    public function test_detail_page_shows_contact_information(): void
    {
        $user = User::factory()->create();
        $contact = $this->makeContact(['name' => '詳細確認太郎', 'body' => '詳細本文です。']);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertOk();
        $response->assertSee('詳細確認太郎');
        $response->assertSee('詳細本文です。');
    }

    /** 2-4-2 */
    public function test_detail_page_returns_404_for_missing_contact(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/contacts/999999')->assertNotFound();
    }

    /** 2-4-3, 2-4-4 */
    public function test_status_update_succeeds(): void
    {
        $user = User::factory()->create();
        $contact = $this->makeContact(['status' => Contact::STATUS_NEW]);

        $response = $this->actingAs($user)->patch("/admin/contacts/{$contact->id}/status", [
            'status' => Contact::STATUS_IN_PROGRESS,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'ステータスを更新しました。');
        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'status' => Contact::STATUS_IN_PROGRESS]);
    }

    /** 2-4-5 */
    public function test_status_update_rejects_invalid_value(): void
    {
        $user = User::factory()->create();
        $contact = $this->makeContact(['status' => Contact::STATUS_NEW]);

        $response = $this->actingAs($user)->patch("/admin/contacts/{$contact->id}/status", [
            'status' => 'hoge',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'status' => Contact::STATUS_NEW]);
    }

    /** 2-5-1, 2-5-2, 2-5-5 */
    public function test_reply_sends_mail_and_records_history(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $contact = $this->makeContact(['email' => 'inquirer@example.com', 'subject' => '元の件名']);

        $response = $this->actingAs($user)->post("/admin/contacts/{$contact->id}/replies", [
            'body' => 'ご返信内容です。',
        ]);

        $response->assertRedirect();
        Mail::assertSent(ContactReplyMail::class, function ($mail) use ($contact) {
            return $mail->hasTo('inquirer@example.com')
                && $mail->envelope()->subject === "Re: {$contact->subject}";
        });
        $this->assertDatabaseHas('contact_replies', [
            'contact_id' => $contact->id,
            'body' => 'ご返信内容です。',
        ]);
    }

    /** 2-5-3 */
    public function test_reply_validation_fails_when_body_empty(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $contact = $this->makeContact();

        $response = $this->actingAs($user)->post("/admin/contacts/{$contact->id}/replies", ['body' => '']);

        $response->assertSessionHasErrors('body');
        Mail::assertNothingSent();
    }

    /** 2-5-4 */
    public function test_reply_validation_fails_when_body_exceeds_max_length(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $contact = $this->makeContact();

        $response = $this->actingAs($user)->post("/admin/contacts/{$contact->id}/replies", [
            'body' => str_repeat('あ', 2001),
        ]);

        $response->assertSessionHasErrors('body');
    }

    /** 2-5-6, 2-5-7, 2-5-8 */
    public function test_reply_history_displayed_newest_first_and_accumulates(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $contact = $this->makeContact();

        $emptyResponse = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");
        $emptyResponse->assertSee('まだ返信はありません。');

        $this->actingAs($user)->post("/admin/contacts/{$contact->id}/replies", ['body' => '一件目の返信']);
        $this->actingAs($user)->post("/admin/contacts/{$contact->id}/replies", ['body' => '二件目の返信']);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");
        $content = $response->getContent();

        $response->assertSee('一件目の返信');
        $response->assertSee('二件目の返信');
        // 新しい順（二件目が先に表示される）ことを確認
        $this->assertTrue(strpos($content, '二件目の返信') < strpos($content, '一件目の返信'));
    }

    private function makeContact(array $overrides = []): Contact
    {
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $contact = Contact::create(array_merge([
            'name' => 'テスト太郎',
            'email' => 'contact-test@example.com',
            'subject' => 'テスト件名',
            'body' => 'テスト本文',
            'status' => Contact::STATUS_NEW,
        ], $overrides));

        if ($createdAt) {
            $contact->forceFill(['created_at' => $createdAt])->save();
        }

        return $contact;
    }
}
