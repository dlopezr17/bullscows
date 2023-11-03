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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('user', 50);
            $table->unsignedInteger('age');
            $table->string('number', 5);
            $table->unsignedInteger('intents');
            $table->string('time', 10);
            $table->boolean('state')->default(0);
            $table->decimal('evaluation', 8, 2)->default('0.00');
            $table->boolean('win')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
