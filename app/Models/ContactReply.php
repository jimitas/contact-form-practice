<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * お問い合わせへの返信履歴を表すモデル。
 */
class ContactReply extends Model
{
    /**
     * 一括代入を許可する属性。
     *
     * @var list<string>
     */
    protected $fillable = [
        'contact_id',
        'body',
    ];

    /**
     * 返信先のお問い合わせ。
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
