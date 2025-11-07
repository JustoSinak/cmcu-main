<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->index(['name', 'prenom'], 'patients_name_prenom_idx');
            $table->index('numero_dossier', 'patients_numero_dossier_idx');
            $table->index('created_at', 'patients_created_at_idx');
        });

        Schema::table('facture_consultations', function (Blueprint $table) {
            $table->index('patient_id', 'facture_consultations_patient_id_idx');
            $table->index('user_id', 'facture_consultations_user_id_idx');
            $table->index('created_at', 'facture_consultations_created_at_idx');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->index('patient_id', 'consultations_patient_id_idx');
            $table->index('date_consultation', 'consultations_date_consultation_idx');
        });

        Schema::table('consultation_anesthesistes', function (Blueprint $table) {
            $table->index('patient_id', 'consultation_anesthesistes_patient_id_idx');
        });

        Schema::table('fiche_consommables', function (Blueprint $table) {
            $table->index('patient_id', 'fiche_consommables_patient_id_idx');
            $table->index('created_at', 'fiche_consommables_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id', 'users_role_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_name_prenom_idx');
            $table->dropIndex('patients_numero_dossier_idx');
            $table->dropIndex('patients_created_at_idx');
        });

        Schema::table('facture_consultations', function (Blueprint $table) {
            $table->dropIndex('facture_consultations_patient_id_idx');
            $table->dropIndex('facture_consultations_user_id_idx');
            $table->dropIndex('facture_consultations_created_at_idx');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex('consultations_patient_id_idx');
            $table->dropIndex('consultations_date_consultation_idx');
        });

        Schema::table('consultation_anesthesistes', function (Blueprint $table) {
            $table->dropIndex('consultation_anesthesistes_patient_id_idx');
        });

        Schema::table('fiche_consommables', function (Blueprint $table) {
            $table->dropIndex('fiche_consommables_patient_id_idx');
            $table->dropIndex('fiche_consommables_created_at_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_id_idx');
        });
    }
};

