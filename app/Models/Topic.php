<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Topic extends Model {

    use SoftDeletes;
    
    protected $fillable = [
        'title',
        'description',
        'status',
        'tags',
        'order',
        'created_by',
        'content_id',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function content() {
        return $this->belongsTo(Content::class);
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function group() {
        return $this->belongsTo(Group::class, 'group_id');
    }

    protected static function boot() {

        parent::boot();
        static::saving(function ($topic) {

            $topic->order   = $topic->order ?? 1;
            $groupId        = $topic->group_id;
            $contentId      = $topic->content_id;

            $topics         = Topic::where('group_id', $groupId)->where('content_id', $contentId)->get()->sortBy('order')->values();
            $desiredOrder   = $topic->order;
            $newOrder       = 1;

            foreach ($topics as $t) {

                if ($t->id == $topic->id) {
                    continue;
                }

                if ($newOrder == $desiredOrder) {
                    $newOrder++;
                }

                $t->order = $newOrder;
                $t->saveQuietly();
                $newOrder++;
            }

            $topic->order = min($desiredOrder, $newOrder);
        });

        static::saved(function ($topic) {

            if ($topic->group_id) {
                $group = Group::find($topic->group_id);
                if ($group) {
                    $topicIds = Topic::where('group_id', $topic->group_id)->pluck('id');
                    $group->topics = $topicIds->mapWithKeys(fn($id) => ['topic_id_'.$id => $id])->toJson();
                    $group->saveQuietly();
                }
            }

            if ($topic->getOriginal('group_id') && $topic->getOriginal('group_id') != $topic->group_id) {
                $oldGroup = Group::find($topic->getOriginal('group_id'));
                if ($oldGroup) {
                    $topicIds = Topic::where('group_id', $oldGroup->id)->pluck('id');
                    $oldGroup->topics = $topicIds->mapWithKeys(fn($id) => ['topic_id_'.$id => $id])->toJson();
                    $oldGroup->saveQuietly();
                }
            }
        });

        static::deleting(function ($topic) {
            if ($topic->group_id) {
                $group = Group::find($topic->group_id);
                if ($group) {
                    $topicIds = Topic::where('group_id', $group->id)
                        ->where('id', '!=', $topic->id)
                        ->pluck('id');
                    $group->topics = $topicIds->mapWithKeys(fn($id) => ['topic_id_'.$id => $id])->toJson();
                    $group->saveQuietly();
                }
            }
        });
    }
}
