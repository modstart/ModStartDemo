<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DemoTestJobCreate extends Migration
{
    
    public function up()
    {
        Schema::create('demo_test_job', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();
            
            $table->tinyInteger('status')->nullable()->comment('');
            $table->string('statusRemark', 400)->nullable()->comment('');
            $table->dateTime('startTime')->nullable()->comment('');
            $table->dateTime('endTime')->nullable()->comment('');
            $table->text('data')->nullable()->comment('');
            $table->text('result')->nullable()->comment('');
        });

    }

    
    public function down()
    {
            }
}
