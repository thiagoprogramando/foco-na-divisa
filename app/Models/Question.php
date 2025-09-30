<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Question extends Model {

    use SoftDeletes;
    
    protected $fillable = [
        'topic_id',
        'board_id',
        'title',
        'resolution',
    ];

    public function topic() {
        return $this->belongsTo(Topic::class);
    }

    public function board() {
        return $this->belongsTo(Board::class);
    }

    public function alternatives() {
        return $this->hasMany(QuestionAlternative::class);
    }

    public function notebookQuestions() {
        return $this->hasMany(NotebookQuestion::class);
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }

    public function isFavorited(): bool {
        return $this->favorites()->where('user_id', Auth::id())->exists();
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
