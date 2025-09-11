<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model {

    use SoftDeletes;

    protected $table = 'products';
    
    protected $fillable = [
        'uuid',
        'image',
        'name',
        'description',
        'value',
        'status',
        'type',
        'time',
        'messages',
        'files',
        'posts',
        'views'
    ];

    public function invoices() {
        return $this->hasMany(Invoice::class, 'product_id');
    }

    public function hasInvoice($user = null, $status = null): bool {
        
        $user   = $user ?? Auth::id();
        $status = $status ?? 1;
        return $this->invoices()
            ->where('user_id', $user)
            ->where('payment_status', $status)
            ->exists();
    }

    public function timeLabel() {
        switch ($this->time) {
            case 'free':
                return 'Gratuito';
            case 'monthly':
                return 'Mensal';
            case 'semi-annual':
                return 'Semestral';
            case 'yearly':
                return 'Anual';
            case 'lifetime':
                return 'Vitalício';
            default:
                return 'Gratuito';
        }
    }

    public function calculateDueDate() {
        return match ($this->time) {
            'monthly'     => now()->addMonth(),
            'semi-annual' => now()->addMonths(6),
            'yearly'      => now()->addYear(),
            'lifetime'    => now()->addYears(100),
            'free'        => now()->addYears(100),
            default       => now()->addDays(7),
        };
    }

    protected $casts = [
        'messages' => 'array',
        'files'    => 'array',
        'posts'    => 'array',
    ];

    public function getMessagesList(): array {
        return collect($this->messages ?? [])->map(function ($item) {
            return [
                'date'      => $item['date'] ?? null,
                'title'     => $item['title'] ?? null,
                'message'   => $item['message'] ?? null,
                'url_image' => $item['url_image'] ?? null,
            ];
        })->all();
    }

    public function getFilesList(): array {
        return collect($this->files ?? [])->map(function ($item) {
            return [
                'id'    => $item['id'] ?? null,
                'date'  => $item['date'] ?? null,
                'title' => $item['title'] ?? null,
                'url'   => $item['url'] ?? null,
            ];
        })->all();
    }

    public function getPostsList(): array {
        return collect($this->posts ?? [])->map(function ($item) {
            return [
                'id'        => $item['id'] ?? null,
                'date'      => $item['date'] ?? null,
                'title'     => $item['title'] ?? null,
                'message'   => $item['message'] ?? null,
                'url'       => $item['url'] ?? null,
            ];
        })->all();
    }
}
