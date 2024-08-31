<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongodb')->create('items', function ($collection) {
            $collection->string('title');
            $collection->text('description');
            $collection->text('installation')->nullable();
            $collection->array('image_urls');
            $collection->string('file_url');
            $collection->string('github_url')->nullable();
            $collection->string('web_url')->nullable();
            $collection->string('owner');
            $collection->date('last_update');
            $collection->array('categories')->nullable();
            $collection->string('type')->nullable();
            $collection->string('creator')->nullable();

            // Índexs opcionals per a camps que ho requereixin
            $collection->index('title');
            $collection->index('owner');
            $collection->index('last_update');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mongodb')->drop('items');
    }
}
