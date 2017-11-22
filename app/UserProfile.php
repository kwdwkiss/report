<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class UserProfile extends Model
{
    protected $table = 'user_profile';

    protected $guarded = [];

    public function _tagEntities()
    {
        return $this->belongsTo(TagEntity::class, 'id', 'entity_id');
    }

    public function _tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_entity', 'entity_id')->wherePivot('entity_type', 'user_profile');
    }

    public function syncTag($ids)
    {
        $ids = $this->parseIds($ids);
        $data = [];
        foreach ($ids as $key => $value) {
            $data[$value] = ['entity_type' => 'user_profile'];
        }
        $this->_tags()->sync($data);
    }

    protected function parseIds($value)
    {
        if ($value instanceof Model) {
            return [$value->getKey()];
        }

        if ($value instanceof Collection) {
            return $value->modelKeys();
        }

        if ($value instanceof BaseCollection) {
            return $value->toArray();
        }

        return (array)$value;
    }
}
