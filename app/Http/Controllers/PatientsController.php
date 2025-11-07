<?php

namespace App\Http\Controllers;
use App\Models\Consultation;
use App\Models\ConsultationAnesthesiste;
use App\Models\Dossier;
use App\Models\FactureConsultation;
use App\Models\FicheConsommable;
use App\Models\FicheIntervention;
use App\Models\Lettre;
use App\Models\Patient;
use App\Models\Ordonance;
use App\Models\Produit;
use App\Models\SoinsInfirmier;
use App\Models\SurveillancePostAnesthesique;
use App\Models\HistoriqueFacture;
use App\Models\User;
use App\Models\VisitePreanesthesique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use ZanySoft\LaravelPDF\PDF;


class PatientsController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('update', Patient::class);
        
        $name = $request->input('name');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $cacheKey = sprintf('patients.index.%s.%s.%s', auth()->id(), $name ?: 'all', $page);

        $patients = Cache::tags(['patients'])
            ->remember($cacheKey, 900, function () use ($name, $perPage) {
                return Patient::select('id', 'numero_dossier', 'name', 'prenom', 'montant', 'reste', 'created_at')
                    ->when($name, function ($query, $name) {
                        return $query->where(function ($innerQuery) use ($name) {
                            $innerQuery->where('name', 'like', "%{$name}%")
                                ->orWhere('prenom', 'like', "%{$name}%");
                        });
                    })
                    ->latest()
                    ->paginate($perPage);
            });

        if ($patients instanceof \Illuminate\Contracts\Pagination\Paginator && $name) {
            $patients->appends(['name' => $name]);
        }

        return view('admin.patients.index', compact('patients', 'name', 'perPage'));
    }





    

    public function create(User $user)
    {
        $this->authorize('update', Patient::class);
        $users = Cache::tags(['users'])->remember('users.role.2', 1800, function () {
            return User::where('role_id', 2)
                ->select('id', 'name', 'prenom')
                ->orderBy('name')
                ->get();
        });
        return view('admin.patients.create', compact('users'));
    }


    public function store(Request $request)
    {
        $this->authorize('update', Patient::class);

        $request->validate([
            'name' => 'required',
            'mode_paiement' => 'required',
            'prenom' => '',
            'assurance' => '',
            'assurancec' => '',
            'assurec' => '',
            'motif' => 'required',
            'details_motif' => 'required',
            'montant' => 'numeric|required',
            'avance' => 'numeric|required',
            'reste' => 'numeric',
            'reste1' => 'numeric',
            'demarcheur' => '',
            'numero_assurance' => 'required_with:assurance',
            'numero_dossier' => '',
            'prise_en_charge' => 'required_with:assurance|numeric|between:0,100',
            'num_cheque' => 'requiredIf:mode_paiement,chèque',
            'emetteur_cheque' => 'requiredIf:mode_paiement,chèque',
            'banque_cheque' =>  'requiredIf:mode_paiement,chèque',
            'emetteur_bpc' =>  'requiredIf:mode_paiement,bon de prise en charge',
            'date_insertion' => '',
        ]);
        
        //
        $modePaiementInfo = $request->input('mode_paiement') === 'chèque'
            ? collect([
                $request->input('num_cheque'),
                $request->input('emetteur_cheque'),
                $request->input('banque_cheque')
            ])->filter()->implode(' // ')
            : ($request->input('mode_paiement') === 'bon de prise en charge'
                ? $request->input('emetteur_bpc')
                : '');

        DB::transaction(function () use ($request, $modePaiementInfo) {
            $montant = $request->input('montant');
            $priseEnCharge = $request->input('prise_en_charge');
            $avance = $request->input('avance');

            Patient::create([
                'numero_dossier' => mt_rand(1000000, 9999999) - 1,
                'name' => $request->input('name'),
                'prenom' => $request->input('prenom'),
                'montant' => $montant,
                'assurance' => $request->input('assurance'),
                'avance' => $avance,
                'motif' => $request->input('motif'),
                'mode_paiement' => $request->input('mode_paiement'),
                'mode_paiement_info_sup' => $modePaiementInfo,
                'details_motif' => $request->input('details_motif'),
                'numero_assurance' => $request->input('numero_assurance'),
                'prise_en_charge' => $priseEnCharge,
                'assurec' => FactureConsultation::calculAssurec($montant, $priseEnCharge),
                'assurancec' => FactureConsultation::calculAssuranceC($montant, $priseEnCharge),
                'reste' => FactureConsultation::calculReste(
                    FactureConsultation::calculAssurec($montant, $priseEnCharge),
                    $avance
                ),
                'demarcheur' => $request->input('demarcheur'),
                'date_insertion' => $request->input('date_insertion'),
                'medecin_r' => $request->input('medecin_r'),
                'user_id' => Auth::id(),
            ]);
        });

        Cache::tags(['patients'])->flush();

        return redirect()->route('patients.index')->with('success', 'Le patient a été ajouté avec succès !');
    }


    // public function show(Patient $patient, Consultation $consultation)
    public function show(Patient $patient, Consultation $consultation)
    {
        $this->authorize('update', Patient::class);
        
        // Get paginated examens
        $examens_scannes = $patient->examens()->latest()->paginate(4);
        
        // Optimize with single query using eager loading for other relationships
        $patient->load([
            'consultations' => function ($query) {
                $query->with(['user:id,name'])
                    ->select('id', 'patient_id', 'user_id', 'diagnostic', 'date_consultation', 'created_at')
                    ->latest();
            },
            'consultation_anesthesistes' => function ($query) {
                $query->with(['user:id,name'])
                    ->select('id', 'patient_id', 'user_id', 'date_intervention', 'created_at')
                    ->latest();
            },
            'dossiers' => function ($query) {
                $query->select('id', 'patient_id', 'sexe', 'date_naissance', 'created_at')
                    ->latest();
            },
            'parametres' => function ($query) {
                $query->select('id', 'patient_id', 'poids', 'taille', 'created_at')
                    ->latest();
            },
            'premedications' => function ($query) {
                $query->select('id', 'patient_id', 'created_at')
                    ->latest();
            },
            'ordonances' => function ($query) {
                $query->select('id', 'patient_id', 'created_at')
                    ->latest()
                    ->limit(5);
            }
        ]);
        
        $medecin = Cache::tags(['users'])->remember('medecins_list', 3600, function () {
            return User::where('role_id', 2)
                ->select('id', 'name', 'prenom')
                ->orderBy('name')
                ->get();
        });
        
        $ficheInterventions = Cache::tags(['patients'])->remember("patient.{$patient->id}.fiche_interventions", 900, function () use ($patient) {
            return $patient->fiche_interventions()
                ->with(['user:id,name'])
                ->select('id', 'patient_id', 'user_id', 'created_at')
                ->latest()
                ->limit(10)
                ->get();
        });

        $latestVisite = Cache::tags(['patients'])->remember("patient.{$patient->id}.visite_preanesthesique", 900, function () use ($patient) {
            return $patient->visite_preanesthesiques()
                ->with(['patient:id,name,prenom', 'user:id,name'])
                ->select('id', 'patient_id', 'user_id', 'created_at')
                ->latest()
                ->first();
        });

        $ordonances = $patient->ordonances()
            ->with(['user:id,name'])
            ->select('id', 'patient_id', 'user_id', 'created_at')
            ->latest()
            ->paginate(5);

        $premedications = $patient->premedications()
            ->select('id', 'patient_id', 'created_at')
            ->latest()
            ->get();

        $prescriptions = $patient->prescriptions()
            ->with(['user:id,name'])
            ->select('id', 'patient_id', 'user_id', 'created_at')
            ->latest()
            ->get();

        return view('admin.patients.show', [
            'patient' => $patient,
            'examens_scannes' => $examens_scannes,
            'medecin' => $medecin,
            'consultations' => $patient->consultations->first(),
            'consultation_anesthesistes' => $patient->consultation_anesthesistes->first(),
            'visite_anesthesistes' => $latestVisite,
            'fiche_interventions' => $ficheInterventions,
            'prescriptions' => $prescriptions,
            'consultation' => $consultation,
            'ordonances' => $ordonances,
            'dossiers' => $patient->dossiers->first(),
            'parametres' => $patient->parametres->first(),
            'premedications' => $premedications,
            'compte_rendu_bloc_operatoires' => $patient->compte_rendu_bloc_operatoires()->select('id', 'patient_id', 'created_at')->latest()->first()
        ]);
    }


    public function update(Request $request, $id)
    {
        $this->authorize('update', Patient::class);
        $request->validate([
            'name' => '',
            'prenom' => '',
            'assurance' => '',
            'assurancec' => '',
            'assurec' => '',
            'numero_assurance' => '',
            'numero_dossier' => '',
            'montant' => '',
            'motif' => '',
            'details_motif' => 'required',
            'avance' => '',
            'reste' => '',
            'reste1' => '',
            'demarcheur' => '',
            'prise_en_charge' => 'numeric|between:0,100',
            'date_insertion' => 'date_insertion',
            'medecin_r' => '',
        ]);


        $patient = Patient::findOrFail($id);

        DB::transaction(function () use ($patient, $request) {
            $patient->fill([
                'assurance' => $request->input('assurance'),
                'numero_assurance' => $request->input('numero_assurance'),
                'name' => $request->input('name'),
                'montant' => $request->input('montant'),
                'motif' => $request->input('motif'),
                'details_motif' => $request->input('details_motif'),
                'avance' => $request->input('avance'),
                'reste' => $request->input('reste'),
                'reste1' => $request->input('reste1'),
                'assurancec' => $request->input('assurancec'),
                'assurec' => $request->input('assurec'),
                'demarcheur' => $request->input('demarcheur'),
                'prise_en_charge' => $request->input('prise_en_charge'),
                'date_insertion' => $request->input('date_insertion'),
                'prenom' => $request->input('prenom'),
                'medecin_r' => $request->input('medecin_r'),
                'user_id' => Auth::id(),
            ]);

            $patient->save();
        });

        Cache::tags(['patients'])->flush();

        return redirect()->route('patients.show', $patient->id)->with('success', 'Les informations du patient ont été mis à jour avec succès !');
    }

    public function motifMontantUpdate(Request $request, $id)
    {
        $this->authorize('update', Patient::class);
        $request->validate([
            'motif' => 'required',
            'name' => 'required',
            'prenom' => 'required',
            'medecin_r' => 'required',
            'mode_paiement' => 'required',
            'num_cheque' => 'requiredIf:mode_paiement,chèque',
            'emetteur_cheque' => 'requiredIf:mode_paiement,chèque',
            'banque_cheque' =>  'requiredIf:mode_paiement,chèque',
            'emetteur_bpc' =>  'requiredIf:mode_paiement,bon de prise en charge',
            'details_motif' => 'required',
            'montant' => 'required|numeric',
            'numero_assurance' => '',
            'assurance' => '',
            'avance' => 'required|numeric',
            'prise_en_charge' => 'required|numeric|between:0,100',
        ]);


        $patient = Patient::findOrFail($id);

        $modePaiementInfo = $request->input('mode_paiement') === 'chèque'
            ? collect([
                $request->input('num_cheque'),
                $request->input('emetteur_cheque'),
                $request->input('banque_cheque')
            ])->filter()->implode(' // ')
            : ($request->input('mode_paiement') === 'bon de prise en charge'
                ? $request->input('emetteur_bpc')
                : '');

        DB::transaction(function () use ($patient, $request, $modePaiementInfo) {
            $montant = $request->input('montant');
            $priseEnCharge = $request->input('prise_en_charge');
            $avance = $request->input('avance');

            $assurec = FactureConsultation::calculAssurec($montant, $priseEnCharge);

            $patient->fill([
                'name' => $request->input('name'),
                'prenom' => $request->input('prenom'),
                'medecin_r' => $request->input('medecin_r'),
                'mode_paiement_info_sup' => $modePaiementInfo,
                'montant' => $montant,
                'details_motif' => $request->input('details_motif'),
                'assurance' => $request->input('assurance'),
                'avance' => $avance,
                'mode_paiement' => $request->input('mode_paiement'),
                'prise_en_charge' => $priseEnCharge,
                'assurec' => $assurec,
                'assurancec' => FactureConsultation::calculAssuranceC($montant, $priseEnCharge),
                'reste' => FactureConsultation::calculReste($assurec, $avance),
                'numero_assurance' => $request->input('numero_assurance'),
                'user_id' => Auth::id(),
            ]);

            $patient->save();
        });

        Cache::tags(['patients'])->flush();

        return redirect()->route('patients.show', $patient->id)->with('success', 'Le motif et le montant ont été mis à jour avec succès !');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * Courier de sortie du patient
     */

    public function index_sortie()
    {
        $lettres = Lettre::all();
        return view('admin.lettres.index', compact('lettres'));
    }


    public function print_sortie(Patient $patient)
    {
        $dossier = Dossier::where("patient_id" , $patient->id)->first();

        $pdf = PDF::loadView('admin.etats.lettre', [
            'patient' => $patient,
            'consultations' => Consultation::where('patient_id', $patient->id)
                ->select('id', 'patient_id', 'diagnostic', 'created_at')
                ->latest()
                ->first(),
            'dossier' => $dossier,
        ]);

        return $pdf->stream('lettre-sortie.pdf');
    }


    public function destroy(Patient $patient)
    {
        DB::transaction(function () use ($patient) {
            $patient->delete();
        });

        Cache::tags(['patients'])->flush();

        return redirect()->route('patients.index')->with('success', "Le dossier du patient a bien été supprimé");
    }


    public function generate_consultation(Request $request, $id)
    {
        $this->authorize('update', Patient::class);
        $this->authorize('print', Patient::class);
        $patient = Patient::select([
            'id', 'numero_dossier', 'name', 'prenom', 'montant',
            'avance', 'reste', 'assurec', 'assurancec', 'mode_paiement',
            'mode_paiement_info_sup', 'motif', 'details_motif',
            'demarcheur', 'medecin_r'
        ])->findOrFail($id);

        $statutFacture = $patient->reste == 0 ? 'Soldée' : 'Non soldée';

        $facture = DB::transaction(function () use ($patient, $statutFacture) {
            $facture = FactureConsultation::create([
                'numero' => $patient->numero_dossier,
                'patient_id' => $patient->id,
                'assurancec' => $patient->assurancec,
                'assurec' => $patient->assurec,
                'mode_paiement' => $patient->mode_paiement,
                'mode_paiement_info_sup' => $patient->mode_paiement_info_sup,
                'motif' => $patient->motif,
                'details_motif' => $patient->details_motif,
                'montant' => $patient->montant,
                'demarcheur' => $patient->demarcheur,
                'avance' => $patient->avance,
                'reste' => $patient->reste,
                'prenom' => $patient->prenom,
                'medecin_r' => $patient->medecin_r,
                'date_insertion' => now()->toDateString(),
                'user_id' => auth()->id(),
                'statut' => $statutFacture,
            ]);

            $facture->historiques()->create([
                'reste' => $facture->reste,
                'montant' => $facture->montant,
                'percu' => $facture->avance,
                'assurec' => $facture->assurec,
                'mode_paiement' => $facture->mode_paiement,
            ]);

            return $facture;
        });

        Cache::tags(['factures', 'patients'])->flush();

        return redirect()->route('factures.consultation')
            ->with('success', 'Facture n° '.$facture->id.' générée avec succès!');
    }

    public function FcheConsommableCreate(FicheConsommable $consommable, Patient $patient)
    {

        $consommables = $patient->fiche_consommables()
            ->with(['patient:id,name'])
            ->select('id', 'patient_id', 'consommable', 'jour', 'nuit', 'date', 'created_at')
            ->latest()
            ->paginate(20);

        return view('admin.patients.fiche_consommable', [
            'produits' => Produit::select('id', 'designation', 'qte_stock')->orderBy('designation')->get(),
            'consommable' => $consommable,
            'consommables' => $consommables,
            'patient' => $patient,
            'user_id' => auth()->user()->id
        ]);
    }

    public function Autocomplete(Request $request)
    {
        $query = $request->input('query');

        $datas = Produit::select('designation')
            ->where('designation', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        $results = $datas->pluck('designation');
        return response()->json($results);
    }

    public function FcheConsommableStore(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'numeric'],
            'patient_id' => ['required', 'numeric', 'exists:patients,id'],
            'consommable' => ['required', 'string'],
            'jour' => ['nullable', 'numeric', 'min:0'],
            'nuit' => ['nullable', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($request) {
            $consommable = FicheConsommable::create([
                'user_id' => $request->input('user_id'),
                'patient_id' => $request->input('patient_id'),
                'consommable' => $request->input('consommable'),
                'jour' => $request->input('jour'),
                'nuit' => $request->input('nuit'),
                'date' => $request->input('date'),
            ]);

            $quantites = collect([
                $request->input('jour'),
                $request->input('nuit')
            ])->filter()->sum();

            if ($quantites > 0) {
                Produit::where('designation', $consommable->consommable)
                    ->select('id', 'qte_stock')
                    ->lockForUpdate()
                    ->get()
                    ->each(function ($produit) use ($request) {
                        $jour = (int) $request->input('jour', 0);
                        $nuit = (int) $request->input('nuit', 0);

                        $produit->qte_stock = max(0, $produit->qte_stock - ($jour + $nuit));
                        $produit->save();
                    });
            }
        });

        Cache::tags(['patients'])->flush();

        flash('La liste des consommables a été mis à jour')->info();
        return back();
    }

    public function SoinsInfirmierStore(Request $request)
    {
        SoinsInfirmier::create([
            'user_id' => auth()->id(),
            'patient_id' => $request->input('patient_id'),
            'date' => $request->input('date'),
            'observation' => $request->input('observation'),
            'patient_externe' => $request->input('patient_externe'),
        ]);

        Cache::tags(['patients'])->flush();

        Flash::info('Votre enregistrement a bien été pris en compte');

        return back();
    }

    public function export_ordonance($id)
    {
        //$this->authorize('print', Patient::class);

        $pdf = PDF::loadView('admin.etats.ordonance', [

            'compteur' => 1,
            'ordonance' => Ordonance::with('patient', 'user')->find($id)
        ]);

        return $pdf->stream('ordonance.pdf');
    }

   public function search(Request $request)
    {
        $this->authorize('update', Patient::class);
        
        $name = $request->input('name');
        
        // Optimize search with proper indexing
        $patients = Patient::select('id', 'numero_dossier', 'name', 'prenom', 'montant', 'reste')
            ->where(function($query) use ($name) {
                $query->where('prenom', 'like', "%{$name}%")
                    ->orWhere('name', 'like', "%{$name}%");
            })
            ->latest()
            ->paginate(10);
        
        return view('admin.patients.index', compact('patients', 'name'));
    }


}








