<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

/**
 * デモ用のお問い合わせデータを大量投入するシーダー。
 */
class ContactSeeder extends Seeder
{
    // 名字
    private const LAST_NAMES = [
        '佐藤', '鈴木', '高橋', '田中', '伊藤', '渡辺', '山本', '中村', '小林', '加藤',
        '吉田', '山田', '佐々木', '山口', '松本', '井上', '木村', '林', '斎藤', '清水',
        '山崎', '森', '池田', '橋本', '阿部', '石川', '前田', '藤田', '後藤', '岡田',
    ];

    // 名前（男性）
    private const FIRST_NAMES_MALE = [
        '翔太', '陽翔', '大翔', '悠斗', '蓮', '颯太', '拓海', '直樹', '健太', '智也',
        '亮太', '和也', '大輔', '圭吾', '誠', '康平', '洋平', '雄大', '慎太郎', '拓真',
    ];

    // 名前（女性）
    private const FIRST_NAMES_FEMALE = [
        '陽菜', '結衣', '美咲', '葵', '咲良', 'さくら', '愛美', '美優', '千尋', '遥',
        '麻衣', '由美', '真央', '彩香', '恵美', '智子', '裕子', '直美', '涼子', '美穂',
    ];

    // 件名・本文のテンプレート（ショッピングサイトへの問い合わせを想定）
    private const INQUIRY_TEMPLATES = [
        ['subject' => '商品の在庫について', 'body' => 'お気に入りの商品が在庫切れになっています。再入荷の予定はありますでしょうか。'],
        ['subject' => '配送状況の確認', 'body' => '注文した商品がまだ届いていません。配送状況を確認していただけますでしょうか。'],
        ['subject' => '返品・交換について', 'body' => '先日購入した商品のサイズが合わなかったため、交換をお願いしたいです。手続き方法を教えてください。'],
        ['subject' => '注文のキャンセルについて', 'body' => '先ほど注文した商品をキャンセルしたいのですが、手続き方法を教えてください。'],
        ['subject' => '支払い方法について', 'body' => 'クレジットカード以外の支払い方法はありますか。代金引換に対応していますでしょうか。'],
        ['subject' => '破損した商品が届きました', 'body' => '本日届いた商品の箱が破損しており、中身にも傷がついていました。交換をお願いできますでしょうか。'],
        ['subject' => '会員登録ができません', 'body' => '会員登録をしようとしたところエラーメッセージが表示され、先に進めません。ご確認いただけますでしょうか。'],
        ['subject' => 'ポイントについての質問', 'body' => '貯まったポイントの有効期限を教えてください。また、次回の買い物でどのように使用できますか。'],
        ['subject' => 'サイズ表記について', 'body' => '商品ページのサイズ表記がわかりにくいのですが、実寸を教えていただけますでしょうか。'],
        ['subject' => '領収書の発行について', 'body' => '先日の注文について、宛名入りの領収書を発行していただくことは可能でしょうか。'],
        ['subject' => '商品の色違いはありますか', 'body' => '気に入った商品があるのですが、掲載されている色以外の展開はありますでしょうか。'],
        ['subject' => 'セール情報について', 'body' => '次回のセール開催予定はありますか。メールマガジンに登録すれば通知されますでしょうか。'],
        ['subject' => 'ギフトラッピングについて', 'body' => 'プレゼント用にギフトラッピングをお願いすることはできますか。追加料金はかかりますでしょうか。'],
        ['subject' => '退会方法について', 'body' => '会員を退会したいのですが、手続き方法を教えてください。'],
        ['subject' => '注文内容の変更について', 'body' => '先ほど注文した商品の配送先住所を変更したいのですが、可能でしょうか。'],
        ['subject' => 'クーポンが使用できません', 'body' => '配布されたクーポンコードを入力しましたが、エラーが出て使用できません。ご確認をお願いします。'],
        ['subject' => '商品が届きません', 'body' => '注文から2週間以上経ちますが、まだ商品が届いていません。状況を教えてください。'],
        ['subject' => 'パスワードを忘れました', 'body' => 'ログイン用のパスワードを忘れてしまいました。再設定の方法を教えてください。'],
        ['subject' => '商品の使い方について', 'body' => '購入した商品の使用方法がよくわからないため、詳しい説明をお願いできますでしょうか。'],
        ['subject' => '定期購入の解約について', 'body' => '定期購入を解約したいのですが、次回発送日までに間に合いますでしょうか。手続き方法を教えてください。'],
    ];

    /**
     * お問い合わせデータを100件投入する。
     */
    public function run(): void
    {
        $statuses = [Contact::STATUS_NEW, Contact::STATUS_IN_PROGRESS, Contact::STATUS_RESOLVED];
        $rows = [];

        for ($i = 0; $i < 100; $i++) {
            // 偶数番目を男性名、奇数番目を女性名にすることで男女比を半々にする
            $firstNames = $i % 2 === 0 ? self::FIRST_NAMES_MALE : self::FIRST_NAMES_FEMALE;
            $template = self::INQUIRY_TEMPLATES[array_rand(self::INQUIRY_TEMPLATES)];
            $createdAt = fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d H:i:s');

            $rows[] = [
                'name' => self::LAST_NAMES[array_rand(self::LAST_NAMES)].$firstNames[array_rand($firstNames)],
                'email' => sprintf('contact%03d@example.com', $i + 1),
                'subject' => $template['subject'],
                'body' => $template['body'],
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        Contact::insert($rows);
    }
}
