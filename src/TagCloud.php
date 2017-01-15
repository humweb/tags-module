<?php

namespace Humweb\Tags;

use Humweb\Tags\Models\Tag;

class TagCloud
{
    public static $baseSize = 11;


    public static function buildFromDb($threshold = 0, $maxsize = 1.75, $minsize = .75)
    {
        $tags = Tag::all();

        return static::build($tags, $threshold, $maxsize, $minsize);
    }


    /**
     * @param array $tags
     * @param int   $threshold less than the threshold is not displayed.
     * @param float $maxsize   max font-size in em units
     * @param float $minsize   min font-size in em units
     *
     * @return array
     */
    public static function build($tags, $threshold = 0, $maxsize = 1.75, $minsize = .75)
    {
        $tagcount = $tagcloud = [];

        // Build slug => count collection
        // Filter tags below threshold
        foreach ($tags as $tag) {
            if ($tag->count >= $threshold) {
                $tagcount[$tag->slug] = $tag->count;
            }
        }

        //Get min/max counts
        $maxcount = max($tagcount);
        $mincount = min($tagcount) - 1;
        $constant = log($maxcount - $mincount) / (($maxsize - $minsize) <= 0 ? 1 : ($maxsize - $minsize));

        foreach ($tagcount as $tag => $count) {
            $size       = log($count - $mincount) / ($constant + $minsize);
            $size       = static::$baseSize + (round($size, 5) * 3);
            $tagcloud[] = ['name' => $tag, 'count' => $count, 'size' => $size];
        }

        return $tagcloud;
    }
}
