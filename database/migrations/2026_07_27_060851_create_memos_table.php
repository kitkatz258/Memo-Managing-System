<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->string('original_filename');
            $table->boolean('for_all_companies')->default(false);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->text('extracted_content')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE memos ADD FULLTEXT search_index (title, extracted_content)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
