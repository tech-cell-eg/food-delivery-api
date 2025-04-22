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
        Schema::table('cheifs', function (Blueprint $table) {

            //
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
            $table->string('email')->unique()->after('name');
            $table->string('password')->after('email');
            $table->string('phone')->after('password')->nullable();
            $table->string('address')->after('phone')->nullable();
            $table->string('specialty')->after('address');
            $table->string('experience')->after('specialty')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheifs', function (Blueprint $table) {
            //
        });
    }
};
