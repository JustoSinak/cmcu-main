<?php

namespace App\Http\Controllers;

use App\Models\AdaptationTraitement;
use App\Models\Patient;
use App\Models\Premedication;
use App\Models\SurveillancePostAnesthesique;
use App\Models\TraitementHospitalisation;
use App\Models\VisitePreanesthesique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Laracasts\Flash\Flash;
// use MercurySeries\Flash\Flash;

class AnesthesisteController extends Controller
{

    public function Premdication_Traitement(Patient $patient)
    {
        $premedications = Premedication::with(['patient:id,name,prenom', 'user:id,name'])->where('patient_id', $patient->id)->latest()->select('id','medicament','patient_id','user_id')->get();
        $TraitementHospitalisations = TraitementHospitalisation::with('patient:id,name,prenom', 'user:id,name')->where('patient_id', $patient->id)->select('id','medicament_posologie_dosage','patient_id','user_id')->get();
        $AdaptationTraitements = AdaptationTraitement::with(['patient:id,name,prenom', 'user:id,name'])->where('patient_id', $patient->id)->select('id','medicament_posologie_dosage','patient_id','user_id')->get();
        $medicament = Premedication::with(['patient:id,name,prenom', 'user:id,name'])->where('patient_id', $patient->id)->latest()->first(['medicament']);
        return view('admin.consultations.premdication_tritement', compact('patient', 'TraitementHospitalisations', 'AdaptationTraitements', 'premedications', 'medicament'));
    }

    public function VisitePreanesthesiqueStore()
    {
        DB::transaction(function () {
            VisitePreanesthesique::create([
                'user_id' => auth()->id(),
                'patient_id' => request('patient_id'),
                'date_visite' => request('date_visite'),
                'element_nouveaux' => request('element_nouveaux')
            ]);
        });
        Flash('Les nouveaux éléménts ont bien été pris en compte !!');
        return back();
    }

    public function PremedicationConsignePreparationStore()
    {
        DB::transaction(function () {
            Premedication::create([
                'user_id' => auth()->id(),
                'patient_id' => request('patient_id'),
                'consigne_ide' => request('consigne_ide'),
                'preparation' => request('preparation'),
                'medicament' => request('medicament')
            ]);
        });
        Flash('Les nouveaux éléménts ont bien été pris en compte !!');
        return back();
    }

    public function TraitementHospitalisationStore(Patient $patient)
    {
        $medicament = Premedication::with(['patient:id,name,prenom', 'user:id,name'])->where('patient_id', $patient->id)->latest()->first();
        DB::transaction(function () use ($medicament) {
            TraitementHospitalisation::create([
                'user_id' => auth()->id(),
                'patient_id' => request('patient_id'),
                'medicament_posologie_dosage' => $medicament ? $medicament->name : null,
                'duree' => request('duree'),
                'j' => request('j'),
                'j0' => request('j0'),
                'j1' => request('j1'),
                'j2' => request('j2'),
                'm' => request('m'),
                'mi' => request('mi'),
                'n' => request('n'),
                's' => request('s'),
                'm1' => request('m1'),
                'mi1' => request('mi1'),
                's1' => request('s1'),
                'n1' => request('n1'),
                'date' => request('date'),
            ]);
        });
        Flash('Les nouveaux éléménts ont bien été pris en compte !!');
        return back();
    }

    public function AdaptationTraitementPersoStore()
    {
        DB::transaction(function () {
            AdaptationTraitement::create([
                'user_id' => auth()->id(),
                'patient_id' => request('patient_id'),
                'medicament_posologie_dosage' => request('medicament_posologie_dosage'),
                'arret' => request('arret'),
                'poursuivre' => request('poursuivre'),
                'continuer' => request('continuer'),
                'j' => request('j'),
                'j0' => request('j0'),
                'j1' => request('j1'),
                'j2' => request('j2'),
                'm' => request('m'),
                'mi' => request('mi'),
                'n' => request('n'),
                's' => request('s'),
                'm1' => request('m1'),
                'mi1' => request('mi1'),
                's1' => request('s1'),
                'n1' => request('n1'),
                'date' => request('date'),
            ]);
        });
        Flash('Les nouveaux éléménts ont bien été pris en compte !!');
        return back();
    }

    public function IndexSurveillancePostAnesthesise(Patient $patient, SurveillancePostAnesthesique $surveillancePostAnesthesique)
    {
        return view('admin.consultations.index_surveillance_post_anesthesique', [

            'patient' => $patient,
            'surveillance_post_anesthesiques' => SurveillancePostAnesthesique::with('patient')->where('patient_id', '=', $patient->id)->get(),
//            'surveillance_post_anesthesique' => $surveillancePostAnesthesique
        ]);
    }

    public function SurveillancePostAnesthesiseStore()
    {
        DB::transaction(function () {
            SurveillancePostAnesthesique::create([
                'user_id' => auth()->id(),
                'patient_id' => request('patient_id'),
                'date_creation' => request('date_creation'),
                'surveillance' => request('surveillance'),
                'traitement' => request('traitement'),
                'examen_paraclinique' => request('examen_paraclinique'),
                'observation' => request('observation'),
                'date_sortie' => request('date_sortie'),
                'heur_sortie' => request('heur_sortie'),
            ]);
        });
        Flash::info('Votre enregistrement a bien été pris en compte');
        return back();
    }

}
