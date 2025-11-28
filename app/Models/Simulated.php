<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Simulated extends Model {

    use SoftDeletes;
    
    protected $table = 'simulateds';

    protected $fillable = [
        'uuid',
        'image',
        'title',
        'caption',
        'description',
        'value',
        'date_start',
        'date_end',
        'status',
    ];

    public function invoices() {
        return $this->hasMany(Invoice::class, 'simulated_id');
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function simulatedAnswers() {
        return $this->hasMany(SimulatedQuestion::class);
    }

    public function hasInvoice($user = null, $status = null): bool {
        
        $user   = $user ?? Auth::id();
        $status = $status ?? 1;
        return $this->invoices()
            ->where('user_id', $user)
            ->where('payment_status', $status)
            ->exists();
    }

    public function countQuestionsByStatus(int $status, ?int $result = null): int {
        
        $query = $this->simulatedAnswers();
        
        if ($status === 1) {
            $query->whereNotNull('answer_id');
        } elseif ($status === 2) {
            $query->whereNull('answer_id');
        }

        if (!is_null($result)) {
            if ($result === 1) {
                $query->where('answer_result', 1);
            } elseif ($result === 2) {
                $query->where('answer_result', 2); 
            }
        }

        return $query->where('user_id', Auth::id())->count();
    }
}
