<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserControlpanelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fr_user_control_panel', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('ka_user_accounts')->onDelete('CASCADE');

            $table->integer('control_panel_id')->unsigned();
            $table->foreign('control_panel_id')->references('id')->on('fr_control_panels')->onDelete('CASCADE');

            $table->boolean('accepted');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fr_user_control_panel');
	}

}
