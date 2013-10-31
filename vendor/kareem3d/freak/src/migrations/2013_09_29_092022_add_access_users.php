<?php

use Illuminate\Database\Migrations\Migration;

class AddAccessUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('ka_user_accounts', function(\Illuminate\Database\Schema\Blueprint $table)
        {
            $table->smallInteger('access');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('ka_user_accounts', function($table)
        {
            $table->dropColumn('access');
        });
	}

}