<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Patient;
// use Barryvdh\DomPDF\Facade as PDF;
use ZanySoft\LaravelPDF\PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PrescriptionController extends Controller
{
    public function create(Patient $patient)
    {
        $prescriptions = Prescription::with('patient:id,name')->select('id', 'patient_id', 'created_at')->where('patient_id', $patient->id)->get();
        return view('admin.prescriptions.create', compact('patient', 'prescriptions'));
    }


    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $patient = Patient::findOrFail($request->input('patient_id'));

            $prescription = new Prescription();

            $prescription->hematologie = implode(',', $request->input('hematologie', []));
            $prescription->hemostase = implode(',', $request->input('hemostase', []));
            $prescription->biochimie = implode(',', $request->input('biochimie', []));
            $prescription->hormonologie = implode(',', $request->input('hormonologie', []));
            $prescription->marqueurs = implode(',', $request->input('marqueurs', []));
            $prescription->bacteriologie = implode(',', $request->input('bacteriologie', []));
            $prescription->spermiologie = implode(',', $request->input('spermiologie', []));
            $prescription->urines = implode(',', $request->input('urines', []));
            $prescription->serologie = implode(',', $request->input('serologie', []));
            $prescription->examen = implode(',', $request->input('examen', []));

            $prescription->patient_id = $request->input('patient_id');
            $prescription->user_id = Auth::id();

            $prescription->save();

            // Invalidate cache for patient's prescriptions
            Cache::forget("prescriptions_patient_{$patient->id}");

            Flash('La nouvelle prescription a été crée avec succès !!');

            return back();
        });
    }

    public function show(Request $request, $id)
    {
        $prescription = Cache::remember("prescription_{$id}", 3600, function () use ($id) {
            return Prescription::with('patient:id,name')->find($id);
        });

        return view('admin.prescriptions.show', compact('prescription'));
    }

    public function export_prescription($id)
    {
        $prescription = Cache::remember("prescription_{$id}", 3600, function () use ($id) {
            return Prescription::with('patient:id,name')->find($id);
        });
        $pdf = PDF::loadView('admin.etats.prescriptions', compact('prescription'));

        return $pdf->stream('prescriptions.pdf');
    }

}



