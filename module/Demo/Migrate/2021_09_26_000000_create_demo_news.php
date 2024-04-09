<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use ModStart\Core\Dao\ModelManageUtil;

class CreateDemoNews extends Migration
{
    
    public function up()
    {

        Schema::create('demo_news', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('categoryId')->nullable()->comment('');

            $table->string('title', 200)->nullable()->comment('');
            $table->string('cover', 200)->nullable()->comment('');
            $table->string('summary', 200)->nullable()->comment('');
            $table->text('content')->nullable()->comment('');

        });

    }

    
    public function down()
    {
    }
}
