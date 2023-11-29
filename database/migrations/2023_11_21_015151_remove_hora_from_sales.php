<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('hora');
        });
    }
    //Si quiero revertir esto puedo usar despues php artisan migrate:rollback

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->time('hora')->nullable(); 
        });
    }
};
//php artisan make:migration remove_hora_from_sales --table=sales comando para remover un elemento de la tabla