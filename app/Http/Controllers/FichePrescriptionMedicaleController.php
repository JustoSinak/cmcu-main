<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
// use MercurySeries\Flash\Flash;
use Laracasts\Flash\Flash;
use App\Models\PrescriptionMedicale;
use App\Models\FichePrescriptionMedicale;
use App\Models\AdminPrescriptionMedicale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FichePrescriptionMedicaleController extends Controller
{
    public function index($patient_id)
    {
        $this->authorize('infirmier_chirurgien', Patient::class);
        $fiche_prescription_medicale = Cache::remember("fiche_prescription_medicale_$patient_id", 600, function () use ($patient_id) {
            return FichePrescriptionMedicale::with('prescription_medicales')->firstOrCreate(['patient_id' => $patient_id]);
        });
        return view('admin.consultations.infirmiers.index_prescription_medicale', [
            'patient' => Patient::select('id','name','prenom')->find($patient_id),
            'fiche_prescription_medicale' => $fiche_prescription_medicale,
            'infirmieres' => User::where('role_id', 4)->select('id','name')->get(),
            'prescription_medicales' => PrescriptionMedicale::with(['patient:id,name,prenom', 'user:id,name'])->where('patient_id', $patient_id)->get()
        ]);
    }

    public function store($patient_id)
    {
        $this->authorize('medecin', Patient::class);
        DB::transaction(function () use ($patient_id) {
            $fiche_prescription_medicale = FichePrescriptionMedicale::where('patient_id', $patient_id)->first() ?: new FichePrescriptionMedicale();
            $fiche_prescription_medicale->fill([
                'patient_id' => $patient_id,
                'allergie' => request('allergie'),
                'regime' => request('regime'),
                'consultation_specialise' => request('consultation_specialise'),
                'protocole' => request('protocole'),
            ]);
            $fiche_prescription_medicale->save();
            Cache::forget("fiche_prescription_medicale_$patient_id");
        });
        Flash::info('Bien enregistré');
        return back();
    }

    public function prescriptionMedicaleStore(Request $request, $fiche_id)
    {
        $this->authorize('medecin', Patient::class);
        $request->validate([
            'medicament' => 'required',
            'posologie' => 'required',
            'horaire' => 'required|array',
            'voie' => 'required',
        ]);
        DB::transaction(function () use ($request, $fiche_id) {
            $fiche_prescription_medicale = FichePrescriptionMedicale::findOrFail($fiche_id);
            $prescriptionMedicale = new PrescriptionMedicale([
                'user_id' => auth()->id(),
                'medicament' => $request->input('medicament'),
                'posologie' => $request->input('posologie'),
                'voie' => $request->input('voie'),
                'horaire' => json_encode($request->input('horaire')),
            ]);
            $fiche_prescription_medicale->prescription_medicales()->save($prescriptionMedicale);
            Cache::forget("fiche_prescription_medicale_{$fiche_prescription_medicale->patient_id}");
        });
        Flash::info('Prescription enregistrée avec succès !');
        return back();
    }
    public function AdminPMStore(Request $request, $prescription_medicale_id)
    {
        $this->authorize('infirmier', Patient::class);
        $request->validate([
            'matin' => 'required_without_all:apre_midi,soir,nuit',
            'apre_midi' => 'required_without_all:matin,soir,nuit',
            'soir' => 'required_without_all:matin,apre_midi,nuit',
            'nuit' => 'required_without_all:matin,apre_midi,soir',
        ]);
        $prescription_medicale = PrescriptionMedicale::firstOrCreate(['id' => $prescription_medicale_id]);
        $adminPrescriptionMedicale = new AdminPrescriptionMedicale([
            'prescription_medicale_id' => $prescription_medicale_id,
            'user_id' => auth()->id(),
            'matin' => request('matin'),
            'apre_midi' => request('apre_midi'),
            'soir' => request('soir'),
            'nuit' => request('nuit'),
        ]);
        if(null !== request('date')){
            $adminPrescriptionMedicale->created_at = request('date');
        }
        
        $prescription_medicale->adminPrescriptionMedicales()->save($adminPrescriptionMedicale);
        Flash::info('Administration enregistrée avec succès');

        return back();
    }


    
}
