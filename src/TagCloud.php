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
     * @param       $tags
     * @param int   $threshold less than the threshold is excluded from being displayed.
     * @param float $maxsize   max desired CSS font-size in em units
     * @param float $minsize   min desired CSS font-size in em units
     *
     * @return array
     */
    public static function build($tags, $threshold = 0, $maxsize = 1.75, $minsize = .75)
    {
        $counts = $tagcount = $tagcloud = array();

        // Build slug => count collection
        // Exclude tags below threshold
        foreach ($tags as $tag) {
            if ($tag['count'] >= $threshold) {
                $counts[] = $tag['count'];
                $tagcount[$tag['slug']] = $tag['count'];
            }
        }

        //Get min/max counts
        $maxcount = max($counts);
        $mincount = min($counts) - 1;
        $constant = log($maxcount - $mincount) / (($maxsize - $minsize) <= 0 ? 1 : ($maxsize - $minsize));

        foreach ($tagcount as $tag => $count) {
            $size = log($count - $mincount) / ($constant + $minsize);
            $size = static::$baseSize + (round($size, 5) * 3);
            $tagcloud[] = array('name' => $tag, 'count' => $count, 'size' => $size);
        }

        return $tagcloud;
    }
}
