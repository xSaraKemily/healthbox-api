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
        Schema::table('acompanhamentos', function (Blueprint $table) {
            $table->dropForeign('acompanhamentos_questionario_id_foreign');
            $table->dropColumn('questionario_id');
        });

        Schema::table('questionarios', function (Blueprint $table) {
            $table->unsignedBigInteger('acompanhamento_id')->after('descricao');
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
