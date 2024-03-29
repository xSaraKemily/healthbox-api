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
        Schema::table('opinioes', function (Blueprint $table) {
           $table->unsignedBigInteger('tratamento_id')->nullable()->after('paciente_id');

            $table->foreign('tratamento_id')->references('id')->on('tratamentos');
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
