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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->string('sku')->nullable()->unique()->after('name');
            $table->integer('reorder_level')->default(0)->after('stock');
            $table->boolean('is_active')->default(true)->after('reorder_level');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['is_active', 'reorder_level', 'sku', 'supplier_id', 'category_id']);
        });
    }
};
