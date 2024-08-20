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
        Schema::table('images', function (Blueprint $table) {
            $table->string('name')->after('id'); // Nom de l'image
            $table->string('path'); // Chemin réel du fichier sur le serveur
            $table->string('url')->unique()->change(); // URL unique générée pour accéder à l'image
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('path');
            $table->dropUnique(['url']); // Supprime la contrainte unique sur `url`
        });
    }
};
