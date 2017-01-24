<?php

namespace Humweb\Tags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    public    $timestamps = false;
    public    $fillable   = ['name', 'slug', 'count'];
    protected $table      = 'tags';
    protected $softDelete = false;


    /**
     * Remove unused tags
     *
     * @return int
     */
    public static function cleanupUnused()
    {
        return static::where('count', '=', 0)->delete();
    }


    /**
     * Increment tag count
     *
     * @param string $slug
     * @param int    $count
     */
    public static function incrementCount($slug, $count = 1)
    {
        return static::where('slug', '=', $slug)->increment('count', $count);
    }


    /**
     * Decrement tag count
     *
     * @param string $slug
     * @param int    $count
     */
    public static function decrementCount($slug, $count = 1)
    {
        return static::where('slug', '=', $slug)->decrement('count', $count);
    }


    /**
     * Name mutator
     *
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
    }

}
