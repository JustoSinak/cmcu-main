<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ObservationMedicale;
use App\Models\Patient;
use App\Models\SoinsInfirmier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChirurgienController extends Controller
{

    public function AbservationMedicaleCreate(Patient $patient)
    {
        $observation_medicales = Cache::remember("observation_medicales_patient_{$patient->id}", 600, function () use ($patient) {
            return ObservationMedicale::with('patient')->where('patient_id', $patient->id)->select('id','observation','date','anesthesiste','patient_id')->get();
        });
        $soins_infirmiers = SoinsInfirmier::with('patient')->where('patient_id', $patient->id)->select('id','soin','date','patient_id')->get();
        return view('admin.consultations.observation_medicale', [
            'anesthesistes' => User::whereIn('users.name', ['TENKE', 'SANDJON'])->select('id','name')->get(),
            'users' => User::where('role_id', '=', 2)->select('id','name')->get(),
            'patient' => $patient,
            'patient_externes' => Client::orderBy('nom', 'asc')->select('id','nom')->get(),
            'observation_medicales' => $observation_medicales,
            'soins_infirmiers' => $soins_infirmiers
        ]);
    }

    public function AbservationMedicaleStore(Request $request)
    {
        $observationMedicale = new ObservationMedicale();
        $observationMedicale->user_id = $request->input('user_id');
        $observationMedicale->patient_id = $request->input('patient_id');
        $observationMedicale->observation = $request->input('observation');
        $observationMedicale->date = $request->input('date');
        $observationMedicale->anesthesiste = $request->input('anesthesiste');
        $observationMedicale->save();
        Cache::forget("observation_medicales_patient_{$observationMedicale->patient_id}");
        return back()->with('success', 'Votre enregistrement a bien été pris en compte');
    }

    public function AbservationMedicaleEdit()
    {

    }

    public function AbservationMedicaleUpdate()
    {

    }
}
