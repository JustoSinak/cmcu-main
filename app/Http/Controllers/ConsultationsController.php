<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationAnesthesiste;
use App\Models\Parametre;
// use Barryvdh\DomPDF\Facade as PDF;
// use ZanySoft\LaravelPDF\Facades\PDF;
use ZanySoft\LaravelPDF\PDF;
use App\Http\Requests\ConsultationRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ConsultationsController extends Controller
{

    public function IndexConsultationChirurgien(Patient $patient)
    {
        // Cache consultation list per patient
        $consultations = Cache::tags(['consultations', 'patients'])->remember(
            "patient_{$patient->id}_consultations", 
            1800, 
            function () use ($patient) {
                return Consultation::with(['user:id,name', 'patient:id,name,prenom'])
                    ->where('patient_id', $patient->id)
                    ->select('id', 'user_id', 'patient_id', 'diagnostic', 'date_consultation', 'created_at')
                    ->latest()
                    ->limit(50)
                    ->get();
            }
        );

        return view('admin.consultations.chirurgiens.index_consultation_chirurgien', [
            'patient' => $patient,
            'consultations' => $consultations,
        ]);
    }
    public function IndexConsultationAnesthesiste(ConsultationAnesthesiste $consultationAnesthesiste, Patient $patient)
    {

        $consultationAnesthesistes = ConsultationAnesthesiste::with(['patient:id,name,prenom', 'user:id,name'])
            ->where('patient_id', $patient->id)
            ->select('id', 'patient_id', 'user_id', 'date_intervention', 'created_at')
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.consultations.anesthesistes.index_consultation_anesthesiste', [
            'patient' => $patient,
            'consultationAnesthesistes' => $consultationAnesthesistes,
        ]);
    }


    public function create(Patient $patient, Consultation $consultation,ConsultationAnesthesiste $consultation_anesthesiste, Parametre $parametre)
    {

        $users = Cache::tags(['users'])->remember('users.role.2.consultations', 1800, function () {
            return User::where('role_id', 2)
                ->select('id', 'name', 'prenom')
                ->orderBy('name')
                ->get();
        });

        return view('admin.consultations.create', [
            'patient' => $patient,
            'users' => $users,
            'consultation' => $consultation,
            'parametre' => $parametre,
            'consultation_anesthesiste' => $consultation_anesthesiste
        ]);
    }


    public function edit(Patient $patient)
    {
        // Optimize with single eager load query
        $data = Cache::tags(['consultations', 'patients'])->remember("patient_{$patient->id}_edit_data", 600, function () use ($patient) {
            return [
                'consultation' => Consultation::with(['patient:id,name,prenom', 'user:id,name'])
                    ->where('patient_id', $patient->id)
                    ->latest()
                    ->first(),
                'consultation_anesthesiste' => ConsultationAnesthesiste::where('patient_id', $patient->id)
                    ->latest()
                    ->first(),
                'parametre' => Parametre::where('patient_id', $patient->id)
                    ->latest()
                    ->first()
            ];
        });

        $users = Cache::tags(['users'])->remember('medecins_with_patients', 3600, function () {
            return User::where('role_id', 2)
                ->select('id', 'name', 'prenom')
                ->get();
        });

        return view('admin.consultations.edit', array_merge([
            'patient' => $patient,
            'users' => $users,
        ], $data));
    }

    public function store_consultation_chirurgien(ConsultationRequest $request)
    {
        $patient = Patient::findOrFail($request->patient_id);

        DB::transaction(function () use ($request, $patient) {
            Consultation::create([
                'user_id' => auth()->id(),
                'patient_id' => $patient->id,
                'diagnostic' => $request->input('diagnostic'),
                'interrogatoire' => $request->input('interrogatoire'),
                'antecedent_m' => $request->input('antecedent_m'),
                'antecedent_c' => $request->input('antecedent_c'),
                'allergie' => $request->input('allergie'),
                'groupe' => $request->input('groupe'),
                'proposition' => implode(",", $request->proposition ?? []),
                'examen_c' => $request->input('examen_c'),
                'examen_p' => $request->input('examen_p'),
                'motif_c' => $request->input('motif_c'),
                'acte' => implode(",", $request->acte ?? []),
                'type_intervention' => $request->input('type_intervention'),
                'date_intervention' => $request->input('date_intervention'),
                'date_consultation' => $request->input('date_consultation'),
                'date_consultation_anesthesiste' => $request->input('date_consultation_anesthesiste'),
                'medecin_r' => $request->input('medecin_r'),
                'proposition_therapeutique' => $request->input('proposition_therapeutique'),
            ]);
        });
        
        // Clear cached consultation data
        Cache::tags(['consultations', 'patients'])->forget("patient_{$patient->id}_consultations");
        Cache::tags(['consultations', 'patients'])->forget("patient_{$patient->id}_edit_data");

        Flash('La nouvelle consultation a été créée avec succès !!');

        return back();
    }

    public function update_consultation_chirurgien(Consultation $consultation, Request $request)
    {

        DB::transaction(function () use ($consultation, $request) {
            $consultation->fill([
                'diagnostic' => $request->input('diagnostic'),
                'interrogatoire' => $request->input('interrogatoire'),
                'antecedent_m' => $request->input('antecedent_m'),
                'antecedent_c' => $request->input('antecedent_c'),
                'allergie' => $request->input('allergie'),
                'groupe' => $request->input('groupe'),
                'examen_c' => $request->input('examen_c'),
                'examen_p' => $request->input('examen_p'),
                'motif_c' => $request->input('motif_c'),
                'type_intervention' => $request->input('type_intervention'),
                'date_intervention' => $request->input('date_intervention'),
                'date_consultation' => $request->input('date_consultation'),
                'date_consultation_anesthesiste' => $request->input('date_consultation_anesthesiste'),
                'medecin_r' => $request->input('medecin_r'),
                'proposition_therapeutique' => $request->input('proposition_therapeutique'),
                'proposition' => implode(",", $request->proposition ?? []),
                'acte' => implode(",", $request->acte ?? []),
            ]);

            $consultation->save();
        });

        Cache::tags(['consultations', 'patients'])->forget("patient_{$consultation->patient_id}_consultations");
        Cache::tags(['consultations', 'patients'])->forget("patient_{$consultation->patient_id}_edit_data");

        Flash('La mise à jour a été effectuée');
        return back();
    }

    public function Astore(Request $request)
    {

        $patient = Patient::findOrFail($request->patient_id);

        DB::transaction(function () use ($request, $patient) {
            ConsultationAnesthesiste::create([
                'user_id' => auth()->id(),
                'patient_id' => $patient->id,
                'specialite' => $request->input('specialite'),
                'medecin_traitant' => $request->input('medecin_traitant'),
                'operateur' => $request->input('operateur'),
                'date_intervention' => $request->input('date_intervention'),
                'motif_admission' => $request->input('motif_admission'),
                'anesthesi_salle' => implode(",", $request->anesthesi_salle ?? []),
                'risque' => $request->input('risque'),
                'solide' => $request->input('solide'),
                'liquide' => $request->input('liquide'),
                'benefice_risque' => $request->input('benefice_risque'),
                'technique_anesthesie' => implode(",", $request->technique_anesthesie ?? []),
                'technique_anesthesie1' => $request->input('technique_anesthesie1'),
                'synthese_preop' => $request->input('synthese_preop'),
                'antecedent_traitement' => $request->input('antecedent_traitement'),
                'examen_clinique' => $request->input('examen_clinique'),
                'traitement_en_cours' => $request->input('traitement_en_cours'),
                'antibiotique' => $request->input('antibiotique'),
                'autre1' => $request->input('autre1'),
                'memo' => $request->input('memo'),
                'adaptation_traitement' => $request->input('adaptation_traitement'),
                'date_hospitalisation' => $request->input('date_hospitalisation'),
                'service' => $request->input('service'),
                'classe_asa' => $request->input('classe_asa'),
                'allergie' => $request->input('allergie'),
                'examen_paraclinique' => implode(",", $request->examen_paraclinique ?? []),
                'intubation' => $request->input('intubation'),
                'mallampati' => $request->input('mallampati'),
                'distance_interincisive' => $request->input('distance_interincisive'),
                'distance_thyromentoniere' => $request->input('distance_thyromentoniere'),
                'mobilite_servicale' => $request->input('mobilite_servicale'),
            ]);
        });

        Cache::tags(['consultations', 'patients'])->forget("patient_{$patient->id}_consultations");
        Cache::tags(['consultations', 'patients'])->forget("patient_{$patient->id}_edit_data");

        Flash('La nouvelle consultation a été créée avec succès !!');

        return back();
    }

    public function update_consultation_anesthesiste(ConsultationAnesthesiste $consultationAnesthesiste, Request $request, Patient $patient)
    {
        DB::transaction(function () use ($consultationAnesthesiste, $request) {
            $consultationAnesthesiste->fill([
                'specialite' => $request->input('specialite'),
                'medecin_traitant' => $request->input('medecin_traitant'),
                'operateur' => $request->input('operateur'),
                'date_intervention' => $request->input('date_intervention'),
                'motif_admission' => $request->input('motif_admission'),
                'anesthesi_salle' => implode(",", $request->anesthesi_salle ?? []),
                'risque' => $request->input('risque'),
                'solide' => $request->input('solide'),
                'liquide' => $request->input('liquide'),
                'benefice_risque' => $request->input('benefice_risque'),
                'technique_anesthesie' => implode(",", $request->technique_anesthesie ?? []),
                'technique_anesthesie1' => $request->input('technique_anesthesie1'),
                'synthese_preop' => $request->input('synthese_preop'),
                'antecedent_traitement' => $request->input('antecedent_traitement'),
                'examen_clinique' => $request->input('examen_clinique'),
                'traitement_en_cours' => $request->input('traitement_en_cours'),
                'antibiotique' => $request->input('antibiotique'),
                'autre1' => $request->input('autre1'),
                'memo' => $request->input('memo'),
                'adaptation_traitement' => $request->input('adaptation_traitement'),
                'date_hospitalisation' => $request->input('date_hospitalisation'),
                'service' => $request->input('service'),
                'classe_asa' => $request->input('classe_asa'),
                'allergie' => $request->input('allergie'),
                'examen_paraclinique' => implode(",", $request->examen_paraclinique ?? []),
                'intubation' => $request->input('intubation'),
                'mallampati' => $request->input('mallampati'),
                'distance_interincisive' => $request->input('distance_interincisive'),
                'distance_thyromentoniere' => $request->input('distance_thyromentoniere'),
                'mobilite_servicale' => $request->input('mobilite_servicale'),
            ]);

            $consultationAnesthesiste->save();
        });

        Cache::tags(['consultations', 'patients'])->forget("patient_{$patient->id}_consultations");
        Cache::tags(['consultations', 'patients'])->forget("patient_{$patient->id}_edit_data");

        Flash('La mise à jour a été éffectuée avec succès !!');

        return back();
    }

    
    public function show(Request $request, $id)
    {
        // return the consultation with patient and user to prevent lazy-loading in partials
        $consultations = Consultation::with(['patient', 'user'])->findOrFail($id);

        return view('admin.consultations.show', compact('consultations'));
    }

    public function Export_consentement_eclaire(Patient $patient)
    {
        // Optimize PDF generation with specific relationships
        $patient->load([
            'dossiers' => function($q) { 
                $q->latest()->limit(1); 
            },
            'fiche_interventions' => function($q) { 
                $q->latest()->limit(1); 
            },
            'consultation_anesthesistes' => function($q) { 
                $q->latest()->limit(1); 
            },
        ]);

        $pdf = PDF::loadView('admin.etats.consentement_eclaire', [
            'patient' => $patient,
            'dossiers' => $patient->dossiers->first(),
            'fiche_intervention' => $patient->fiche_interventions->first(),
            'consultation_anesthesiste' => $patient->consultation_anesthesistes->first()
        ]);

        return $pdf->stream('consentement_eclaire.pdf');
    }
   

}

