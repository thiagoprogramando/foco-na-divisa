<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable {

    use SoftDeletes;

    protected $fillable = [
        'id',
        'uuid',
        'photo',
        'name',
        'cpfcnpj',
        'email',
        'password',
        'address_postal_code',
        'address_num',
        'address_address',
        'address_city',
        'address_state',
        'role',
    ];

    public function favoriteQuestions() {
        return $this->belongsToMany(Question::class, 'favorites');
    }

    public function notebooks() {
        return $this->hasMany(Notebook::class, 'created_by');
    }

    public function questions() {
        return $this->hasMany(NotebookQuestion::class, 'user_id');
    }

    public function questionsWithTrashed() {
        return $this->hasMany(NotebookQuestion::class)->withTrashed();
    }

    public function countForQuestion(int $questionId, $answer_result = null) {
        $query = $this->questionsWithTrashed()
            ->where('question_id', $questionId)
            ->where('user_id', $this->id);

        if (!is_null($answer_result)) {
            $query->whereIn('answer_result', $answer_result);
        }

        return $query->orderBy('answer_result', 'asc')->get();
    }

    public function successCountForQuestion(int $questionId): int {
        return $this->questionsWithTrashed()
            ->where('question_id', $questionId)
            ->where('user_id', $this->id)
            ->where('answer_result', 1)
            ->count();
    }

    public function errorCountForQuestion(int $questionId): int {
        return $this->questionsWithTrashed()
            ->where('question_id', $questionId)
            ->where('user_id', $this->id)
            ->where('answer_result', 2)
            ->count();
    }

    public function pendingCountForQuestion(int $questionId): int {
        return $this->questionsWithTrashed()
            ->where('question_id', $questionId)
            ->where('user_id', $this->id)
            ->where(function ($q) {
                $q->where('answer_result', 0)
                  ->orWhereNull('answer_result');
            })
            ->count();
    }

    public function invoices() {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    public function products() {
        return $this->belongsToMany(
            Product::class, 'invoices', 'user_id', 'product_id'
        )->withPivot('id', 'value', 'due_date', 'payment_status', 'payment_token', 'created_at', 'updated_at');
    }

    public function maskName() {
        if (empty($this->name)) {
            return '';
        }

        $nameParts = explode(' ', trim($this->name));

        if (count($nameParts) === 1) {
            return $nameParts[0];
        }

        return $nameParts[0] . ' ' . $nameParts[1];
    }

    public function maskCpfCnpj() {

        $value = preg_replace('/\D/', '', $this->cpfcnpj);
        if (strlen($value) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $value);
        } elseif (strlen($value) === 14) {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $value);
        }

        return $this->cpfcnpj;
    }

    public function daysToPlanExpiration(): int {
        
        $invoice = $this->invoices()
            ->whereHas('product', fn($q) => $q->where('type', 'plan'))
            ->latest('created_at')
            ->first();

        if (!$invoice) {
            return 0;
        }

        $totalInvoices = $this->invoices()
            ->whereHas('product', fn($q) => $q->where('type', 'plan'))
            ->count();

        if ($totalInvoices === 1 && $invoice->payment_status !== 1) {
            $daysUsed   = Carbon::parse($invoice->created_at)->diffInDays(Carbon::now());
            $daysLeft   = max(0, 2 - $daysUsed);
            return $daysLeft;
        }

        $daysLeft = Carbon::now()->diffInDays(Carbon::parse($invoice->due_date), false);
        return max(0, $daysLeft);
    }

    public function planLabel(): string {
        
        $plan = $this->products()->where('type', 'plan')->latest('pivot_created_at')->first();
        if ($plan) {
            return e($plan->name);
        }

        return 'Escolha um plano';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
}
