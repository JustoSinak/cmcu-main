<?php

namespace App\Http\Controllers;

use App\Models\Chambre;
use App\Models\Consultation;
use App\Models\Event;
use App\Http\Requests\LicenceActiveRequest;
use App\Models\Fiche;
use App\Models\Licence;
use App\Models\Patient;
use App\Models\Produit;
use App\Models\User;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Cache;
// use MercurySeries\Flashy\Flashy;

class AdminController extends Controller
{


    public function dashboard()
    {
        // Cache dashboard statistics for 5 minutes
        $stats = Cache::remember('dashboard_stats_' . auth()->id(), 300, function () {
            return [
                'produits' => Produit::count(),
                'users' => User::count(),
                'patients' => Patient::count(),
                'events' => Event::where('user_id', auth()->id())->count(),
                'chambres' => Chambre::count(),
                'fiches' => Fiche::count(),
            ];
        });
        
        // Optimize consultation query with eager loading and limit
        $consultation = Consultation::with(['user:id,name', 'patient:id,name,prenom'])
            ->where('user_id', auth()->id())
            ->select('id', 'user_id', 'patient_id', 'date_consultation', 'created_at')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', array_merge($stats, compact('consultation')));
    }

    public function ActiveLicence(LicenceActiveRequest $request)
    {
        $licence = Licence::where('client', 'cmcuapp')->first();

        $licence->update([
            'license_key' => $request->input('license_key'),
            'expire_date' => Carbon::parse('+1 month')
        ]);
        
        // Clear license cache
        Cache::forget('license_cmcuapp');

        Flash::info('Votre licence a bien été activée');

        return back();
    }

   public function index()
    {
        return redirect()->route('admin.dashboard');
    }

   /*function phpans_license(){
        $license = rand(1000,9999) . '-' . rand(1000,9999) . '-' . rand(1000,9999) . '-' . rand(1000,9999) . '-' . rand(1000,9999);
        return $license;
    }*/

}






















