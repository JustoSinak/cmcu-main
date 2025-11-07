<?php

namespace App\Http\Controllers;
use App\Models\Patient;
use ZanySoft\LaravelPDF\Facades\PDF;
// use ZanySoft\LaravelPDF\PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Devi;
use App\Models\LigneDevi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DevisController extends Controller
{
    public function index()
    {
        $devis = Cache::remember('devis_list', 600, function () {
            return Devi::latest()->with('ligneDevis:id,element,quantite,prix_u,devi_id')->select("id","code","acces","user_id","nom","nbr_chambre","nbr_visite","nbr_ami_jour","pu_chambre","pu_visite","pu_ami_jour")->limit(100)->get();
        });
        $patients = Patient::orderBy('name', 'ASC')->select('id','name', 'prenom')->get();
        return view('admin.devis.index', compact('devis','patients'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('update', Devi::class);
        $request->validate([
            'code_devis' => '',
            'nbr_chambre' => 'required|numeric|min:0',
            'nbr_visite' => 'required|numeric|min:0',
            'nbr_ami_jour' => 'required|numeric|min:0',
            'pu_chambre' => 'required|numeric|min:0',
            'pu_visite' => 'required|numeric|min:0',
            'pu_ami_jour' => 'required|numeric|min:0',
            'nom_devis' => 'required',
            'acces_devis' => 'required',
            'ligneDevi' => 'array|required',
            'ligneDevi.*.element' => 'required',
            'ligneDevi.*.quantite' => 'required|numeric|min:1',
            'ligneDevi.*.prix_u' => 'required|numeric|min:1',
        ]);
        DB::transaction(function () use ($request, $id) {
            $devi = Devi::findOrFail($id);
            $devi->nom = $request->input('nom_devis');
            $devi->nbr_chambre = $request->input('nbr_chambre');
            $devi->nbr_visite = $request->input('nbr_visite');
            $devi->nbr_ami_jour = $request->input('nbr_ami_jour');
            $devi->pu_chambre = $request->input('pu_chambre');
            $devi->pu_visite = $request->input('pu_visite');
            $devi->pu_ami_jour = $request->input('pu_ami_jour');
            $devi->code = $request->input('code_devis') ?? \Carbon\Carbon::now()->toDateString().'/'.substr($request->input('nom_devis'),0,4);
            $devi->acces = $request->input('acces_devis');
            $lignedevis = $request->input('ligneDevi');
            $devi->save();
            LigneDevi::where('devi_id',$id)->delete();
            foreach ($lignedevis as $ligneDevi) {
                $devi->ligneDevis()->save(new LigneDevi([
                    "element" => $ligneDevi["element"],
                    "quantite" => $ligneDevi["quantite"],
                    "prix_u" => $ligneDevi["prix_u"],
                ]));
            }
            Cache::forget('devis_list');
        });
        return redirect()->route('devis.index')->with('success', 'Devis modifié avec succès !');
    }


    public function store(Request $request)
    {
        $this->authorize('create', Devi::class);
        $request->validate([
            'code_devis' => '',
            'nbr_chambre' => 'required|numeric|min:0',
            'nbr_visite' => 'required|numeric|min:0',
            'nbr_ami_jour' => 'required|numeric|min:0',
            'pu_chambre' => 'required|numeric|min:0',
            'pu_visite' => 'required|numeric|min:0',
            'pu_ami_jour' => 'required|numeric|min:0',
            'nom_devis' => 'required',
            'acces_devis' => 'required',
            'ligneDevi' => 'array|required',
            'ligneDevi.*.element' => 'required',
            'ligneDevi.*.quantite' => 'required|numeric|min:1',
            'ligneDevi.*.prix_u' => 'required|numeric|min:1',
        ]);
        DB::transaction(function () use ($request) {
            $devis = Devi::create([
                'nom' => $request->input('nom_devis'),
                'nbr_chambre' => $request->input('nbr_chambre'),
                'nbr_visite' => $request->input('nbr_visite'),
                'nbr_ami_jour' => $request->input('nbr_ami_jour'),
                'pu_chambre' => $request->input('pu_chambre'),
                'pu_visite' => $request->input('pu_visite'),
                'pu_ami_jour' => $request->input('pu_ami_jour'),
                'code' => $request->input('code_devis') ?? \Carbon\Carbon::now()->toDateString().'/'.substr($request->input('nom_devis'),0,4),
                'acces' => $request->input('acces_devis'),
                'user_id' =>  Auth::id(),
            ]);
            $lignedevis = $request->input('ligneDevi');
            foreach ($lignedevis as $ligneDevi) {
                $devis->ligneDevis()->save(new LigneDevi([
                    "element" => $ligneDevi["element"],
                    "quantite" => $ligneDevi["quantite"],
                    "prix_u" => $ligneDevi["prix_u"],
                ]));
            }
            Cache::forget('devis_list');
        });
        return redirect()->route('devis.index')->with('success', 'Devis enregistré avec succès !');
    }

    public function export_devis (Request $request, $montant_en_lettre)
    {
        $this->authorize('print', Devi::class);
        $request->validate([
            'patient' => 'required',
            'nbr_chambre' => 'required|numeric|min:0',
            'nbr_visite' => 'required|numeric|min:0',
            'nbr_ami_jour' => 'required|numeric|min:0',
            'pu_chambre' => 'required|numeric|min:0',
            'pu_visite' => 'required|numeric|min:0',
            'pu_ami_jour' => 'required|numeric|min:0',
            'nom_devis' => 'required',
            'acces_devis' => '',
            'code_devis' => '',
            'ligneDevi' => 'array|required',
            'ligneDevi.*.element' => 'required',
            'ligneDevi.*.quantite' => 'required|numeric|min:1',
            'ligneDevi.*.prix_u' => 'required|numeric|min:1',
        ]);
        
        $devis = new Devi([
            'nbr_chambre' => $request->get('nbr_chambre'),
            'nbr_visite' => $request->get('nbr_visite'),
            'nbr_ami_jour' => $request->get('nbr_ami_jour'),
            'pu_chambre' => $request->get('pu_chambre'),
            'pu_visite' => $request->get('pu_visite'),
            'pu_ami_jour' => $request->get('pu_ami_jour'),
            'nom' => $request->get('nom_devis'),
            'code' => $request->get('code_devis') ?? \Carbon\Carbon::now()->toDateString().'/'.substr($request->get('nom_devis'),0,4),
            'user_id' =>  Auth::id(),
            'total1' => 0,
        ]);
        $nomPatient =$request->get('patient');
         $ld = $request->get('ligneDevi');
         $lignedevis=[];
         $total1 = 0;
         $prix_chambre = $request->get('nbr_chambre') * $request->get('pu_chambre');
         $prix_visite = $request->get('nbr_visite') * $request->get('pu_visite');
         $prix_ami_jour = $request->get('nbr_ami_jour') * $request->get('pu_ami_jour');
         $devis->total2 = $prix_chambre + $prix_visite + $prix_ami_jour;
         foreach ($ld as  $ligneDevi) {
            $total1 += $ligneDevi["prix_u"]*$ligneDevi["quantite"];
            array_push($lignedevis, new LigneDevi([
                "element" => $ligneDevi["element"],
                "quantite" => $ligneDevi["quantite"],
                "prix_u" => $ligneDevi["prix_u"],
                "prix" => $ligneDevi["prix_u"]*$ligneDevi["quantite"],
             ]));
         }
         $devis->total1 = $total1;
         $devis->total = $montant_en_lettre;
        $pdf = PDF::loadView('admin.etats.devis', [
            'devis' => $devis,
            'ligneDevis' => $lignedevis,
            'nomPatient' => $nomPatient,
        ]);

        return $pdf->stream('devis.pdf');
    }
}
