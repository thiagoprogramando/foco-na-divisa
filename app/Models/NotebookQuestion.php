<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotebookQuestion extends Model {

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'notebook_id',
        'question_id',
        'question_position',
        'answer_id',
        'answer_result',
    ];

    public function notebook() {
        return $this->belongsTo(Notebook::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }

}
