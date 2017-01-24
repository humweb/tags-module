<?php

namespace Humweb\Tests\Tags\Fake;

use Humweb\Tags\Models\TaggableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Page
 *
 * @package Humweb\Tests\Core\Fake
 */
class Page extends Model
{
    use TaggableTrait;

    protected $table = 'pages';

    protected $guarded = [];

    protected $versionsEnabled = true;

}