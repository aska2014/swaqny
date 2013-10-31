<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageSpecificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ka_image_specifications', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';
			$table->increments('id');

            $table->string('directory');

            $table->integer('code_id')->unsigned()->nullable();
            $table->foreign('code_id')->references('id')->on('ka_codes')->onDelete('CASCADE');

            $table->integer('image_group_id')->unsigned();
            $table->foreign('image_group_id')->references('id')->on('ka_image_groups')->onDelete('CASCADE');

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
		Schema::drop('ka_image_specifications');
	}

}
