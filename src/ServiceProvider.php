<?php

namespace Humweb\Tags;

use Humweb\Modules\ModuleBaseProvider;

class ServiceProvider extends ModuleBaseProvider
{

    protected $moduleMeta = [
        'name'    => 'Tagging Module',
        'slug'    => 'tags',
        'version' => '',
        'author'  => '',
        'email'   => '',
        'website' => '',
    ];


    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->app['modules']->put('tags', $this);
        $this->loadMigrations();
    }

}
