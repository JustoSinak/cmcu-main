<?php

namespace App\Http\Controllers;

use App\Models\CompteRenduBlocOperatoire;
use App\Models\FicheIntervention;
use App\Http\Requests\CompteRenduBlocOperatoireRequest;
use App\Models\Patient;
use App\Models\User;
// use ZanySoft\LaravelPDF\PDF;
use ZanySoft\LaravelPDF\Facades\PDF;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CompteRenduBlocOperatoireController extends Controller
{

    public function index(CompteRenduBlocOperatoire $compteRenduBlocOperatoire, Patient $patient)
    {
        $compteRenduBlocOperatoires = Cache::remember("crbo_patient_{$patient->id}", 600, function () {
            return CompteRenduBlocOperatoire::with('patient')->select('id','patient_id','chirurgien','date_intervention')->get();
        });
        return view('admin.consultations.chirurgiens.index_compte_rendu_operatoire', [
            'patient' => $patient,
            'compteRenduBlocOperatoires' => $compteRenduBlocOperatoires,
        ]);
    }


    public function create(CompteRenduBlocOperatoire $compteRenduBlocOperatoire, Patient $patient)
    {
        return view('admin.consultations.create_compte_rendu_operatoire', [
            'compteRenduBlocOperatoire' => $compteRenduBlocOperatoire,
            'patient' => $patient,
            'users' => User::where('role_id', '=', 2)->select('id','name')->get(),
            'anesthesistes' => User::whereIn('users.name', ['TENKE', 'SANDJON'])->select('id','name')->get(),
            'infirmierAnesthesistes' => User::where('role_id', '=', 4)->select('id','name')->get()
        ]);
    }

    public function edit(Patient $patient)
    {
        return view('admin.consultations.edit_compte_rendu_operatoire', [

            'compteRenduBlocOperatoire' => CompteRenduBlocOperatoire::with('user')->where('patient_id', $patient->id)->latest()->first(),
            'patient' => $patient,
            'users' => User::where('role_id', '=', 2)->get(),
            'anesthesistes' => User::whereIn('users.name', ['TENKE', 'SANDJON'])->get(),
            'infirmierAnesthesistes' => User::where('role_id', '=', 4)->get()
        ]);
    }


    public function store(CompteRenduBlocOperatoireRequest $request, Patient $patient)
    {
        $patient = Patient::select('id')->findOrFail($request->input('patient_id'));
        DB::transaction(function () use ($request, $patient) {
            CompteRenduBlocOperatoire::create([
                'patient_id' => $patient->id,
                'anesthesiste' => $request->input('anesthesiste'),
                'aide_op' => $request->input('aide_op'),
                'chirurgien' => $request->input('chirurgien'),
                'infirmier_anesthesiste' => $request->input('infirmier_anesthesiste'),
                'compte_rendu_o' => $request->input('compte_rendu_o'),
                'indication_operatoire' => $request->input('indication_operatoire'),
                'resultat_histo' => $request->input('resultat_histo'),
                'suite_operatoire' => $request->input('suite_operatoire'),
                'traitement_propose' => $request->input('traitement_propose'),
                'soins' => $request->input('soins'),
                'conclusion' => $request->input('conclusion'),
                'dure_intervention' => $request->input('dure_intervention'),
                'date_intervention' => $request->input('date_intervention'),
                'titre_intervention' => $request->input('titre_intervention'),
                'type_intervention' => $request->input('type_intervention'),
                'proposition_suivi' => $request->input('proposition_suivi'),
                'date_e'=> $request->input('date_e'),
                'date_s'=> $request->input('date_s'),
                'type_e'=> $request->input('type_e'),
                'type_s'=> $request->input('type_s'),
            ]);
            Cache::forget("crbo_patient_{$patient->id}");
        });
        Flash('Le compte rendu du bloc opérqtoire a été ajouté avec succes');
        return back();
    }

    public function update(CompteRenduBlocOperatoireRequest $request, CompteRenduBlocOperatoire $compteRenduBlocOperatoire, Patient $patient)
    {
        $compteRenduBlocOperatoire->update($request->all());

        Flash('Le compte rendu du bloc opérqtoire a été mis à jour');

        return back();
    }

    public function StoreFicheIntervention(Request $request, Patient $patient)
    {
        $patient = Patient::select('id')->findOrFail($request->input('patient_id'));
        DB::transaction(function () use ($request, $patient) {
            FicheIntervention::create([
                'user_id' => auth()->user()->id,
                'patient_id' => $patient->id,
                'nom_patient' => $request->input('nom_patient'),
                'prenom_patient' => $request->input('prenom_patient'),
                'sexe_patient' => $request->input('sexe_patient'),
                'date_naiss_patient' => $request->input('date_naiss_patient'),
                'portable_patient' => $request->input('portable_patient'),
                'type_intervention' => $request->input('type_intervention'),
                'dure_intervention' => $request->input('dure_intervention'),
                'position_patient' => implode(",", $request->input('position_patient', [])),
                'decubitus' => implode(",", $request->input('decubitus', [])),
                'laterale' => implode(",", $request->input('laterale', [])),
                'lombotomie' => implode(",", $request->input('lombotomie', [])),
                'date_intervention' => $request->input('date_intervention'),
                'medecin' => $request->input('medecin'),
                'aide_op' => implode(",", $request->input('aide_op', [])),
                'hospitalisation' => $request->input('hospitalisation'),
                'ambulatoire' => $request->input('ambulatoire'),
                'anesthesie' => implode(",", $request->input('anesthesie', [])),
                'recommendation' => $request->input('recommendation'),
            ]);
        });
        Flash('La fiche d\'inteventtion a bien été');
        return back();
    }


    public function compte_rendu_bloc_pdf($id)
    {

        $patient = Patient::with('compte_rendu_bloc_operatoires', 'consultations')->findOrFail($id);

        $pdf = PDF::loadView('admin.etats.crbo', compact('patient'));

        return $pdf->stream('crbo.pdf');
    }

    public function Print_ficheIntervention($id)
    {

        $fiche_intervention = FicheIntervention::findOrFail($id);

        $pdf = PDF::loadView('admin.etats.fiche_intervention', compact('fiche_intervention'));

        return $pdf->stream('fiche_intervention.pdf');
    }
}




