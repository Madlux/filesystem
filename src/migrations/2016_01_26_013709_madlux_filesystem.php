<?php 
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MadluxFilesystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('fileroot');
            $table->string('href');
            $table->string('filesize');
            $table->string('filename');
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
        Schema::drop('files');
    }
}