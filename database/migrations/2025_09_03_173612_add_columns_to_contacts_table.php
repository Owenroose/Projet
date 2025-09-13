<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('status')->default('new')->after('read');
            $table->string('priority')->default('medium')->after('status');
            $table->foreignId('assigned_to')->nullable()->after('priority')->constrained('users')->onDelete('set null');
            $table->text('response')->nullable()->after('assigned_to');
            $table->timestamp('response_sent_at')->nullable()->after('response');
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['status', 'priority', 'assigned_to', 'response', 'response_sent_at']);
        });
    }
};
