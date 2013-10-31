<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRecipientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ka_user_recipients', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('ka_user_accounts')->onDelete('CASCADE');

            $table->integer('receiptable_id')->unsigned();
            $table->string('receiptable_type');

            $table->boolean('seen');

            $table->string('extra');

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
		Schema::drop('ka_user_recipients');
	}

}
