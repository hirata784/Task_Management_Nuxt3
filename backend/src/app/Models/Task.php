<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'title',
        'is_done',
    ];

    // リレーションの追加
    public function user()
    {
        return $this->belongTo(User::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        // 日本語表示向けにYYYY/MM/DDに変更
        return $date->format('Y/m/d');
    }
}
