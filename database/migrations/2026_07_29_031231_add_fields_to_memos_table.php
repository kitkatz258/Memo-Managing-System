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
        Schema::table('memos', function (Blueprint $table) {
            $table->string('memo_no')->nullable();
            $table->unsignedSmallInteger('year');
            $table->string('author')->nullable();
            $table->boolean('for_all_categories')->default(false);
            $table->boolean('for_all_ranks')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memos', function (Blueprint $table) {
            $table->dropColumn(['memo_no', 'year', 'author', 'for_all_categories', 'for_all_ranks']);
        });
    }
};
