<?php

namespace Humweb\Tags;

use Humweb\Module\AbstractModule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Module extends AbstractModule
{
    public $name        = 'Tags';
    public $version     = '1.1';
    public $author      = 'Ryun Shofner';
    public $website     = 'humboldtweb.com';
    public $license     = 'BSD-3-Clause';
    public $description = 'Tags Module';
    public $autoload    = [];


    public function boot()
    {
    }


    public function install()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 60)->index();
            $table->string('name', 60);
            $table->boolean('suggest')->default(false);
            $table->integer('count')->unsigned()->default(0); // count of how many times this tag was used
        });
        Schema::create('tagged_items', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('taggable');
            $table->integer('tag_id')->unsigned()->index();
            $table->string('tag_name', 60);
            $table->string('tag_slug', 60)->index();
        });

        return true;
    }


    public function uninstall()
    {
        Schema::dropIfExists('tagged_items');
        Schema::dropIfExists('tags');

        return true;
    }


    public function upgrade()
    {
        return true;
    }


    public function admin_menu()
    {
        return [
            'Content' => [
                [
                    // 'label' => 'Tags',
                    // 'url' => '/admin/tags',
                    // 'children' => [
                    // 	['label' => 'New Tag', 'url' => '/admin/tags/create']
                    // ]
                ],
            ],
        ];
    }

    // public function admin_quick_menu()
    // {
    // 	return [
    // 		'index' => [
    // 			['label' => 'Add Page', 'url' => '/admin/pages/create']
    // 		]
    // 	];
    // }
}
