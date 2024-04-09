<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use ModStart\Core\Dao\ModelManageUtil;

class CreateDemoNewsCategory extends Migration
{
    
    public function up()
    {

        Schema::create('demo_news_category', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();
            $table->integer('pid')->nullable()->comment('');
            $table->integer('sort')->nullable()->comment('');

            $table->string('title', 20)->nullable()->comment('');

        });

    }

    
    public function down()
    {
    }
}
