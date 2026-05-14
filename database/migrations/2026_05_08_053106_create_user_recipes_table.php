<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('ingredients');
            $table->integer('cook_time');
            $table->text('instructions');
            $table->timestamps();
        });
    }

     public function down(): void
    {
        Schema::dropIfExists('user_recipes');
    }
};