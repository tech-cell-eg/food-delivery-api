<?php

use App\Models\Cheif;
use App\Models\Restaurant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cheif::class, 'cheif_id')->constrained('cheifs')->onDelete('cascade');
            $table->foreignIdFor(User::class,'user_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(Restaurant::class,'restaurant_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
