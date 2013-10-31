<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // This table define user accounts
		Schema::create('ka_user_accounts', function(Blueprint $table)
		{
			$table->increments('id');

            // This is the only required field
            $table->string('email')->unique();

            // These two fields can be nullable
            $table->string('username')->nullable()->unique();
            $table->string('password');

            $table->smallInteger('type')->default(Kareem3d\Membership\User::NORMAL);

            $table->dateTime('online_at');

            $table->softDeletes();
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
		Schema::drop('ka_user_accounts');
	}

}
