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
}
