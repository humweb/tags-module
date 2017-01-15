<?php

namespace Humweb\Tags\Models;

use DB;
use Illuminate\Support\Str;

trait TaggableTrait
{

    public static $shouldCleanupUnused = false;


    /**
     * Tagged query scope
     *
     * @param string $tagName
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function scopeWithTag($query, $tagName)
    {

        return $query->whereHas('tagged', function ($q) use ($tagName) {
            if (is_array($tagName)) {
                $tagName = array_map(function ($tag) {
                    return Str::slug($tag);
                }, $tagName);
                $q->whereIn('tags.slug', $tagName);
            } else {
                $q->where('tags.slug', '=', Str::slug($tagName));
            }
        });
    }


    /**
     * @return boolean
     */
    public static function shouldCleanupUnused()
    {
        return self::$shouldCleanupUnused;
    }


    /**
     * @param boolean $shouldCleanupUnused
     */
    public static function setCleanupUnused($shouldCleanupUnused)
    {
        self::$shouldCleanupUnused = $shouldCleanupUnused;
    }


    /**
     * Tagged relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tagged()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'tagged_items');
    }


    /**
     * Sync tags
     *
     * @param array $tags
     */
    public function saveTags(array $tags)
    {
        $ids     = [];
        $newTags = [];

        if (is_array($tags) and ! empty($tags)) {
            foreach ($tags as $item) {
                $newTags[Str::slug($item)] = $item;
            }

            $existing = Tag::select('id', 'slug')->whereIn('slug', array_keys($newTags))->get();

            // Get existing ids and remove them from the new tags array
            foreach ($existing as $row) {
                if (isset($newTags[$row->slug])) {
                    unset($newTags[$row->slug]);
                }
                $ids[] = $row->id;
            }

            // Insert new tags
            foreach ($newTags as $slug => $name) {
                $ids[] = Tag::insertGetId(['name' => $name, 'slug' => $slug]);
            }
        }

        //Sync tags
        $changes = $this->tagged()->sync($ids);

        //Update stat counts
        $this->updateTagCounts($changes);
    }


    /**
     * Add tag
     *
     * @param string $tagName
     */
    public function tag($tagName)
    {
        $tagName = trim($tagName);
        if ( ! strlen($tagName)) {
            return;
        }

        $tagSlug = Str::slug($tagName);

        if ($this->tagged()->where('tag_slug', '=', $tagSlug)->take(1)->count() >= 1) {
            return;
        }

        $tagged = Tag::firstOrCreate([
            'name' => Str::title($tagName),
            'slug' => $tagSlug,
        ]);

        $this->tagged()->attach($tagged);

        // TODO: refactor to event (possibly on the model insert event)
        Tag::incrementCount($tagSlug, $tagName, 1);
    }


    /**
     * Remove the tag from this model
     *
     * @param array|string|null $tagNames
     */
    public function untag($tagNames = null)
    {
        if (is_null($tagNames)) {
            $tagNames = $this->tagNames();
        } elseif ( ! is_array($tagNames)) {
            $tagNames = [$tagNames];
        }

        foreach ($tagNames as $tagName) {
            $this->removeTag($tagName);
        }

        if (static::shouldCleanupUnused()) {
            Tag::cleanupUnused();
        }
    }


    /**
     * Remove the tag from this model.
     *
     * @param $tagName string
     */
    public function removeTag($tagName)
    {
        $tagName = trim($tagName);
        $tagSlug = Str::slug($tagName);
        $tag     = $this->tagged()->where('slug', '=', $tagSlug)->first();

        if ($tag && $count = $this->tagged()->detach($tag->id)) {
            Tag::decrementCount($tagSlug, $tagName, $count);
        }
    }


    /**
     * Return array of the tag names related to the current model.
     *
     * @return array
     */
    public function tagNames()
    {
        $tagNames       = array();
        $taggedIterator = $this->tagged()->select('tag_slug', 'tag_name');

        foreach ($taggedIterator->get() as $tagged) {
            $tagNames[$tagged->tag_slug] = $tagged->tag_name;
        }

        return $tagNames;
    }


    /**
     * Update tag counts
     *
     * @param array $changes
     */
    public function updateTagCounts($changes)
    {
        foreach ($changes['attached'] as $id) {
            Tag::where('id', $id)->increment('count');
        }

        foreach ($changes['detached'] as $id) {
            Tag::where('id', $id)->decrement('count');
        }
    }
}
