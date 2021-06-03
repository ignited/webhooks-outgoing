<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->longText('body')->nullable();

            $table->string('response_code')->nullable();

            $table->integer('attempts')->default(0);

            $table->timestamps();
            $table->timestamp('last_attempt_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
