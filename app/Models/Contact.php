<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * お問い合わせを表すモデル。
 */
class Contact extends Model
{
    // ステータス：新規
    public const STATUS_NEW = '新規';

    // ステータス：対応中
    public const STATUS_IN_PROGRESS = '対応中';

    // ステータス：解決済み
    public const STATUS_RESOLVED = '解決済み';

    /**
     * 一括代入を許可する属性。
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'body',
        'status',
    ];

    /**
     * 管理画面のステータス選択肢を返す。
     *
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => self::STATUS_NEW,
            self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
            self::STATUS_RESOLVED => self::STATUS_RESOLVED,
        ];
    }

    /**
     * このお問い合わせへの返信履歴。
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ContactReply::class);
    }
}
