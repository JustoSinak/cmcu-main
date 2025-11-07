<?php

namespace App\Http\Controllers;
use App\Models\Imagerie;
use Illuminate\Http\Request;
use App\Models\Patient;
// use ZanySoft\LaravelPDF\PDF;
use ZanySoft\LaravelPDF\Facades\PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ImagerieController extends Controller
{
    public function create(Patient $patient)
    {
        $imageries = Cache::remember("imageries_patient_{$patient->id}", 600, function () use ($patient) {
            return Imagerie::with('patient')->where('patient_id', $patient->id)->select('id','radiographie','echographie','scanner','irm','scintigraphie','autre','patient_id')->get();
        });
        return view('admin.consultations.partials.feuille_examen_imagerie', compact('patient', 'imageries'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $patient = Patient::select('id')->findOrFail($request->input('patient_id'));
            $imageries = new Imagerie();
            $imageries->radiographie = implode(',', $request->input('radiographie', []));
            $imageries->echographie = implode(',', $request->input('echographie', []));
            $imageries->scanner = implode(',', $request->input('scanner', []));
            $imageries->irm = implode(',', $request->input('irm', []));
            $imageries->scintigraphie = implode(',', $request->input('scintigraphie', []));
            $imageries->autre = implode(',', $request->input('autre', []));
            $imageries->patient_id = $patient->id;
            $imageries->user_id = Auth::id();
            $imageries->save();
            Cache::forget("imageries_patient_{$patient->id}");
        });

        Flash('La nouvelle prescription a été crée avec succès !!');
        return back();
    }

    public function show(Request $request, $id)
    {

        $imageries = Imagerie::find( $id);

        return view('admin.imageries.show', compact('imageries'));
    }

    public function export_imageries($id)
    {

        $imageries = Imagerie::find($id);
        $pdf = PDF::loadView('admin.etats.imageries', compact('imageries'));

        return $pdf->stream('imageries.pdf');
    }

}
