<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Facture;
use App\Http\Requests\ProduitRequest;
use App\Models\Patient;
use App\Models\Produit;
// use Barryvdh\DomPDF\Facade as PDF;
use ZanySoft\LaravelPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class ProduitsController extends Controller
{
    public function index()
    {
        // Cache product count
        $produitCount = Cache::remember('produits_count', 600, function () {
            return Produit::count();
        });
        
        $produits = Produit::select(['id', 'designation', 'categorie', 'qte_stock', 'qte_alerte', 'prix_unitaire'])
            ->orderBy('id', 'asc')
            ->paginate(200);

        return view('admin.produits.index', compact('produits', 'produitCount'));
    }


    public function create()
    {
        $this->authorize('create', Produit::class);
        return view('admin.produits.create');
    }

    public function store(Request $request)
    {

        $this->authorize('create', Produit::class);


        $request->validate([
            'designation'=> ['required', 'unique:produits'],
            'categorie'=> 'required',
            'qte_alerte'=> 'required',
            'qte_stock'=> 'required',
            'prix_unitaire'=> 'required|integer'
        ]);
        $produit = new Produit();
        $produit->designation = $request->get('designation');
        $produit->categorie = $request->get('categorie');
        $produit->qte_stock = $request->get('qte_stock');
        $produit->qte_alerte = $request->get('qte_alerte');
        $produit->prix_unitaire = $request->get('prix_unitaire');
        $produit->user_id = Auth::id();

        $produit->save();

        return redirect()->route('produits.index')->with('success', 'Le produit a été ajouté avec succès !');
    }

    public function edit(Produit $produit)
    {
        $this->authorize('create', Produit::class);

//        $produit = Produit::find($id);

        return view('admin.produits.edit', compact('produit'));
    }


    public function update(ProduitRequest $request, Produit $produit)
    {
        $this->authorize('create', Produit::class);


        $input = $request->input('qte_stock');
        $nqte = DB::table('produits')->where('qte_stock', $produit->qte_stock)->sum('qte_stock');

        $produit->qte_stock = $input + $nqte;
        $produit->user_id = Auth::id();
        $produit->save();

        return redirect()->route('produits.index')->with('success', 'La mise à jour a bien été éffectuer');
    }


    public function destroy(Produit $produit)
    {
        $this->authorize('delete', $produit);

        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Le produit a bien été supprimé');
    }

    public function stock_pharmaceutique()
    {
        $produits = Produit::where('categorie', 'PHARMACEUTIQUE')
            ->select('id', 'designation', 'qte_stock', 'qte_alerte', 'prix_unitaire')
            ->paginate(200);
        
        $pharmaCount = Produit::where('categorie', 'PHARMACEUTIQUE')->count();

        return view('admin.produits.pharmaceutique', compact('produits', 'pharmaCount'));
    }

    public function stock_materiel()
    {

        $produits = Produit::where('categorie', '=', 'MATERIEL')->paginate(50);

        $materielCount = count($produits);

        return view('admin.produits.materiel', compact('produits', 'materielCount'));

    }

    public function stock_anesthesiste()
    {
//        $this->authorize('anesthesiste', Produit::class);
//        $this->authorize('update', Produit::class);

        $produits = Produit::where('categorie', '=', 'ANESTHESISTE')->paginate(50);
        $pharmaCount = count($produits);


        return view('admin.produits.anesthesiste', array_merge(['produits' => $produits], ['pharmaCount' => $pharmaCount]));

    }

    public function add_to_cart(Request $request, $id)
    {
        // Use select to retrieve only necessary columns
        $produit = Produit::select(['id', 'designation', 'qte_stock', 'qte_alerte', 'prix_unitaire', 'categorie'])
            ->findOrFail($id);

        if ($produit->qte_stock == 0) {
            $route = auth()->user()->role_id === 7 
                ? 'produits.pharmaceutique' 
                : 'produits.anesthesiste';
                
            return redirect()->route($route)
                ->with('error', 'Le produit n\'est plus disponible en stock');
        }

        $oldCart = Session::get('cart', null);
        $cart = new Cart($oldCart);
        $cart->add($produit, $produit->id);

        $request->session()->put('cart', $cart);

        flash()->success("La facture vient d'être mise à jour");

        return redirect()->route('pharmaceutique.facturation');
    }
    public function facturation()
    {

        if(!Session::has('cart')){

            return view('admin.produits.facturation');
        }

        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $produit = DB::table('produits')->where('id', $cart);
        $patient = Patient::all();

        return view('admin.produits.facturation',
            [
                'produit' => $produit,
                'produits' => $cart->items,
                'totalPrix' => $cart->totalPrix,
                'patient' => $patient
            ]);
    }

    public function getReduceByOne($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new  Cart($oldCart);
        $cart->reduceByOne($id);

        if (count($cart->items) > 0)
        {
            Session::put('cart', $cart);
        }else{
            Session::forget('cart');
        }

        flash()->success("La facture vient d'être mise à jour");

        return redirect()->route('pharmaceutique.facturation');
    }

    public function getRemoveItem($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new  Cart($oldCart);

        $cart->removeItem($id);

        if (count($cart->items) > 0)
        {
            Session::put('cart', $cart);
        }else{
            Session::forget('cart');
        }

        flash()->info("La produit à bien été supprimer de la facture");

        return redirect()->route('pharmaceutique.facturation');
    }


    public function export_pdf(Request $request, Produit $produit, Patient $patient)
    {
//        $this->authorize('print', Produit::class);
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $produit = DB::table('produits')->where('id', $cart);
        $patient = \request('patient');

        $facture = Facture::create([
            'numero' => mt_rand(10000, 999999),
            'quantite_total' => $cart->totalQte,
            'prix_total' => $cart->totalPrix,
            'patient' => $patient,
            'user_id' => \auth()->user()->id,
        ]);


        $facture->produits()->attach($cart->items);

        $pdf = PDF::loadView('admin.etats.pharmacie', ['produit' => $produit, 'patient' => $patient, 'produits' => $cart->items, 'totalPrix' => $cart->totalPrix, 'facture' => $facture]);

        Session::forget('cart');
        return $pdf->stream('pharmacie.pdf');
    }
}

















