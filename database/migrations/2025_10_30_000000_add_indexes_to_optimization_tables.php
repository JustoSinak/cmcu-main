<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToOptimizationTables extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('date_insertion');
            $table->index('medecin_r');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->index('patient');
            $table->index(['created_at']); // If 'created_at' is not present, replace with actual date column
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->index('date_consultation');
            $table->index('date_intervention');
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['date_insertion']);
            $table->dropIndex(['medecin_r']);
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropIndex(['patient']);
            $table->dropIndex(['created_at']); // Update if you use a different column
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex(['date_consultation']);
            $table->dropIndex(['date_intervention']);
        });
    }
}
