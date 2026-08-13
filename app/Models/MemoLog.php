<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoLog extends Model
{
    protected $fillable = ['memo_id', 'user_id', 'action', 'remarks'];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
