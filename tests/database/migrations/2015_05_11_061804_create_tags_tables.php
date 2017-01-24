<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 60)->index();
            $table->string('name', 60);
            $table->integer('count')->unsigned()->default(0); // count of how many times this tag was used
        });

        Schema::create('tagged_items', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('taggable');
            $table->integer('tag_id')->unsigned()->index();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tagged_items');
		Schema::dropIfExists('tags');
	}

}
