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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('company_name');
            $table->string('location');
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract, remote
            $table->decimal('salary_min', 12, 2)->unsigned()->nullable();
            $table->decimal('salary_max', 12, 2)->unsigned()->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'created_at']);
            $table->index('employment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
