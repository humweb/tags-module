<?php

namespace Humweb\Tags\Models;

use Illuminate\Database\Eloquent\Model;

class Tagged extends Model
{
    public    $timestamps = false;
    protected $table      = 'tagged_items';
    protected $softDelete = false;
    protected $fillable   = ['tag_id', 'tag_name', 'tag_slug'];


    public function taggable()
    {
        return $this->morphTo();
    }


    /**
     * Get instance of tag linked to the tagged value
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_slug', 'slug');
    }
}
