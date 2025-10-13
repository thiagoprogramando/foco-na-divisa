<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model {

    protected $table = 'topic_groups';
    
    protected $fillable = [
        'title', 
        'topics',
        'content_id',
        'order',
    ];

    public function content() {
        return $this->belongsTo(Content::class);
    }

    protected static function boot() {

        parent::boot();

        static::saving(function ($group) {

            $group->order = $group->order ?? 1;

            $query = Group::where('content_id', $group->content_id)->where('id', '!=', $group->id)->where('order', '>=', $group->order);
            if ($query->exists()) {
                DB::transaction(function () use ($group, $query) {
                    $query->increment('order');
                });
            }
        });
    }
}
