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

    public function labelResult() {
        
        if ($this->trashed()) {
            return [
                'message' => 'Questão Excluída',
                'color'   => 'secondary'
            ];
        }

        switch ($this->answer_result) {
            case 1:
                $result = [
                    'message' => 'Acertou',
                    'color'   => 'success'
                ];
                break;
            case 2:
                $result  = [
                    'message' => 'Errou',
                    'color'   => 'danger'
                ];
                break;
            default:
                $result  = [
                    'message' => 'Aguardando Resposta',
                    'color'   => 'warning'
                ];
                break;
        }

        return $result;
    }

    public static function countByResult(?int $userId = null, ?int $answerResult = null): int {
        
        $query = self::query();

        if (!is_null($userId)) {
            $query->where('user_id', $userId);
        }

        if (!is_null($answerResult)) {
            $query->where('answer_result', $answerResult);
        }

        return $query->count();
    }
}
