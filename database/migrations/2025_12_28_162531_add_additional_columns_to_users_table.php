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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->enum('user_type', ['admin', 'manager', 'cashier', 'waiter', 'user'])->default('user')->after('username');
            $table->string('salt')->nullable()->after('remember_token');
            $table->string('language', 10)->default('en')->after('salt');
            $table->string('contact_no')->nullable()->after('language');
            $table->text('address')->nullable()->after('contact_no');
            $table->unsignedBigInteger('business_id')->nullable()->after('address');
            $table->boolean('status')->default(true)->after('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'user_type',
                'salt',
                'language',
                'contact_no',
                'address',
                'business_id',
                'status',
            ]);
        });
    }
};
