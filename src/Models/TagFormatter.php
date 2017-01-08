<?php

namespace Humweb\Tags\Models;

use Illuminate\Support\Str;

/**
 * TagFormatter
 *
 * @package Humweb\Tags\Models
 */
class TagFormatter
{

    public function slug($title)
    {
        return Str::slug($title);
    }

    public function title($slug)
    {
        return Str::title($slug);
    }
}