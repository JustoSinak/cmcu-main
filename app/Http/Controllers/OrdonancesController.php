<?php

namespace App\Http\Controllers;

use App\Models\Ordonance;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use ZanySoft\LaravelPDF\Facades\PDF;
// use ZanySoft\LaravelPDF\PDF;

class OrdonancesController extends Controller
{

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $patient = Patient::select('id')->findOrFail($request->input('patient_id'));
            Ordonance::create([
                'user_id' => auth()->id(),
                'patient_id' => $patient->id,
                'description'=> implode(",", $request->input('description', [])),
                'medicament'=> implode(",", $request->input('medicament', [])),
                'quantite'=> implode(",", $request->input('quantite', [])),
            ]);
            Cache::forget('ordonances_patient_' . $patient->id);
        });

        Flash('La nouvelle ordonance a été crée avec succès !!');
        return back();
    }

    public function export_pdf($id)
    {
        $ordonance = Cache::remember("ordonance_$id", 600, function () use ($id) {
            return Ordonance::with('patient')->find($id);
        });
        $pdf = PDF::loadView('admin.etats.ordonance', compact('ordonance'));
        return $pdf->download('ordonance.pdf');
    }

    public function ordonance_create(Patient $patient)
    {
        return view('admin.prescriptions.ordonance_create', compact('patient'));
    }

}
