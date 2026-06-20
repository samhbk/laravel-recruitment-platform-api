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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, reviewed, shortlisted, rejected, hired
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['job_listing_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
