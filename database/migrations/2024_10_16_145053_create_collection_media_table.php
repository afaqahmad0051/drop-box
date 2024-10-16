<?php

use App\Models\Media;
use App\Models\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collection_media', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Collection::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Media::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_media');
    }
};
