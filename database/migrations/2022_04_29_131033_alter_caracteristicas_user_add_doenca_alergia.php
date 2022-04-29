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
        Schema::table('caracteristicas_paciente', function(Blueprint $table) {
           $table->text('comorbidades')->after('foto_path')->nullable();
           $table->text('pre_disposicoes')->after('comorbidades')->nullable();
           $table->text('alergias_remedios')->after('pre_disposicoes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
