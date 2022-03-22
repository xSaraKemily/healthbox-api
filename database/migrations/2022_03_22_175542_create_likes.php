<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('opiniao_id');
            $table->tinyInteger('is_like');
            $table->timestamps();

            $table->index('usuario_id');
            $table->index('opiniao_id');
            $table->index(['opiniao_id', 'usuario_id']);
            $table->index(['opiniao_id', 'usuario_id', 'is_like']);
            $table->index('is_like');

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('opiniao_id')->references('id')->on('opinioes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
};
