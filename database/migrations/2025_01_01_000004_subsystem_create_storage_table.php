<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('storage', function (Blueprint $table) {
            $table->string('SID')->primary();
            $table->unsignedBigInteger('uploader_morphable_id');
            $table->string('uploader_morphable_type');
            $table->unsignedBigInteger('morphable_id')->nullable();
            $table->string('morphable_type')->nullable();
            $table->string('extension', 10);
            $table->string('fileName');
            $table->integer('fileSize')->unsigned();
            $table->enum('fileType', ['audio', 'image', 'excel', 'pdf','video', 'file']);
            $table->string('additionalPath')->nullable();
            $table->integer('width')->unsigned()->nullable();
            $table->integer('height')->unsigned()->nullable();
            $table->integer('duration')->unsigned()->nullable();
            $table->boolean('isUsed')->default(false);
            $table->boolean('isPublic')->default(false);
            $table->timestamps();

            $table->index(['uploader_morphable_type', 'uploader_morphable_id']);
            $table->index(['morphable_type', 'morphable_id']);
            $table->index(['isPublic', 'isUsed', 'created_at']);
            $table->index(['fileType', 'isPublic']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('storage');
    }
};
