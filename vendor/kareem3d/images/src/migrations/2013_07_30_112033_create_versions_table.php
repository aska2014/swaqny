<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ka_versions', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('url');
            $table->integer('width');
            $table->integer('height');

            $table->integer('image_id')->unsigned();
            $table->foreign('image_id')->references('id')->on('ka_images')->onDelete('CASCADE');

            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ka_versions');
	}

}
