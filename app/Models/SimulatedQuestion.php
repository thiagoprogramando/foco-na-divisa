<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulatedQuestion extends Model {

    protected $table = 'simulated_questions';

    protected $fillable = [
        'user_id',
        'simulated_id',
        'question_id',
        'question_position',
        'answer_id',
        'answer_result',
        'resolved_at',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function simulated() {
        return $this->belongsTo(Simulated::class, 'simulated_id')->withTrashed();
    }

    public function question() {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function answer() {
        return $this->belongsTo(QuestionAlternative::class, 'answer_id');
    }
    
    public function labelResult() {

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
                    'message' => 'Nenhuma resposta',
                    'color'   => 'warning'
                ];
                break;
        }

        return $result;
    }
}
