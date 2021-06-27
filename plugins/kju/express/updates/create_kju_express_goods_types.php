<?php namespace Kju\Express\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateKjuExpressGoodsTypes extends Migration
{
    public function up()
    {
        Schema::create('kju_express_goods_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 50);

            $table->boolean('profit_share')->default(false);

            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique('name');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('kju_express_goods_types');
    }
}
