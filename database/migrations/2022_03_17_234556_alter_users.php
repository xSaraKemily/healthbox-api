<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->date('data_nascimento')->nullable()->after('email');
            $table->string('telefone', 9)->nullable()->after('data_nascimento');
            $table->longText('foto_path')->nullable()->after('telefone');
            $table->tinyInteger('ativo')->default(1)->after('remember_token');
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
