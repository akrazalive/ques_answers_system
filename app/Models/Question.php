<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['question_text'];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
