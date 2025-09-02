<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
