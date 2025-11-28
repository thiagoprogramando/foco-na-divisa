<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {

    use SoftDeletes;
    
    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'simulated_id',
        'value',
        'due_date',
        'payment_status',
        'payment_splits',
        'payment_token',
        'payment_url'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function simulated() {
        return $this->belongsTo(Simulated::class, 'simulated_id');
    }

    public function statusLabel() {
        switch ($this->payment_status) {
            case 0:
                return '<span class="badge bg-label-warning rounded-pill">Pendente</span>';
            case 1:
                return '<span class="badge bg-label-success rounded-pill">Aprovado</span>';
            case 2:
                return '<span class="badge bg-label-danger rounded-pill">Cancelado</span>';
            default:
                return '<span class="badge bg-label-primary rounded-pill">Sem Informações</span>';
        }
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
