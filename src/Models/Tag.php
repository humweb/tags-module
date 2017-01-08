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
     * Name mutator
     *
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
    }

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
     * @param string $name
     * @param int    $count
     */
    public static function incrementCount($slug, $name, $count = 1)
    {
        static::incrementOrDecrementCount($slug, $name, $count, 'increment');
    }

    /**
     * Decrement tag count
     *
     * @param string $slug
     * @param string $name
     * @param int    $count
     */
    public static function decrementCount($slug, $name, $count = 1)
    {
        static::incrementOrDecrementCount($slug, $name, $count, 'decrement');
    }

    /**
     * Increment or decrement count for a tag
     *
     * @param string  $slug
     * @param string  $name
     * @param integer $count
     * @param string  $action
     */
    public static function incrementOrDecrementCount($slug, $name, $count, $action)
    {
        if ($count <= 0) {
            return;
        }

        $tag = static::where('slug', '=', $slug)->first();

        if ( ! $tag) {
            $tag          = new self;
            $tag->name    = $name == '' ? $slug : $name;
            $tag->slug    = $slug;
            $tag->count   = 0;
            $tag->suggest = false;
        }
        $tag->count = $tag->count + ($action == 'increment' ? $count : $count * -1);

        $tag->save();
    }

}
