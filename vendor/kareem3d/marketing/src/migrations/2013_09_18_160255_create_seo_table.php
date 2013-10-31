<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ka_seo', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('title');
            $table->text('keywords');
            $table->text('description');

            $table->integer('url_id')->unsigned();
            $table->foreign('url_id')->references('id')->on('ka_urls')->onDelete('CASCADE');

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
		Schema::drop('ka_seo');
	}

}
