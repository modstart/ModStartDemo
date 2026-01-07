<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DemoNewsCategoryChange1 extends Migration
{
    
    public function up()
    {

        Schema::table('demo_news_category', function (Blueprint $table) {

            $table->string('cover', 200)->nullable()->comment('');

        });

    }

    
    public function down()
    {
            }
}
