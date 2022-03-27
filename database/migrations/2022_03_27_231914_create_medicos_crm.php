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
        Schema::create('medicos_crm', function (Blueprint $table) {
            $table->id();
            $table->string('crm');
            $table->unsignedBigInteger('medico_id');
            $table->timestamps();

            $table->index('medico_id');

            $table->foreign('medico_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicos_crm');
    }
};
