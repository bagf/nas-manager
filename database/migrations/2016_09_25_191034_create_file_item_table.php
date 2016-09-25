<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_item', function (Blueprint $table) {
            $table->integer('file_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->index(['file_id', 'item_id',]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('file_item');
    }
}
