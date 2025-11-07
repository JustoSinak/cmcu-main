<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use App\Models\Patient;
use App\Models\User;
// use Calendar;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent; 
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Cache;
class EventsController extends Controller
{

    public function index(Request $request)
    {
        // Cache events with relationships
        $events = Cache::remember('events_with_patients', 600, function () {
            return Event::with(['patients:id,name,prenom'])
                ->select('id', 'title', 'start', 'end', 'user_id', 'patient_id', 'statut')
                ->get();
        });

        // Cache resource lists
        $patients = Cache::remember('patients_list', 600, function () {
            return Patient::orderBy('name', 'ASC')
                ->select('id', 'name', 'prenom')
                ->get();
        });
        
        $ressources = Cache::remember('medecins_ressources', 600, function () {
            return User::where('role_id', 2)
                ->select('id', 'name', 'prenom')
                ->get();
        });

        return view('admin.events.index', compact('events', 'ressources', 'patients'));
    }

    public function medecinEvents(Request $request, $id_medecin)
    {
        // Cache individual medecin events
        $cacheKey = "medecin_{$id_medecin}_events";
        
        $data = Cache::remember($cacheKey, 600, function () use ($id_medecin) {
            $medecin = User::with([
                    'events' => function($q) use ($id_medecin) {
                        $q->where('user_id', $id_medecin)
                        ->with('patients:id,name,prenom')
                        ->select('id', 'title', 'start', 'end', 'user_id', 'patient_id', 'statut');
                    }
                ])
                ->select('id', 'name', 'prenom')
                ->findOrFail($id_medecin);
            
            return [
                'medecin' => $medecin,
                'events' => $medecin->events
            ];
        });

        return view('admin.events.show', $data);
    }

    public function update(Request $request)
    {
        $this->authorize('create', Event::class);
    
    if ($request->ajax()) {
        $events = json_decode($request->get('events'));
        $result = "";
        
        \DB::beginTransaction();
        try {
            foreach ($events as $event) {
                switch ($event->state) {
                        case 'cre':
                            $eventToBeSaved = Event::create([
                                'title' => $event->title,
                                'start' => Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $event->start),
                                'end' => Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $event->end),
                                'user_id' => $event->resourceId,
                                'description' => $event->description,
                                'objet' => $event->objet,
                                'statut' => $event->statut,
                                'state' => 'aucun',
                                'patient_id' => $event->patient->id,
                            ]);
                            $result .= "Rendez-vous ".$eventToBeSaved->id.' créé ! ';
                            break;
                        case 'mod':
                            $eventToBeUpdated = Event::findOrFail($event->id);
                            $eventToBeUpdated->update([
                                'id' => $event->id,
                                'title' => $event->title,
                                'start' => Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $event->start),
                                'end' => Carbon::createFromFormat('Y-m-d\TH:i:s.uP', $event->end),
                                'user_id' => $event->resourceId,
                                'description' => $event->description,
                                'objet' => $event->objet,
                                'statut' => $event->statut,
                                'state' => 'aucun', //$event->state,
                                //'patient_id' => $event->patient->id,
                            ]);
                            $result = $result."Rendez-vous ".$event->id.' modifié ! ';

                            break;
                        case 'sup':
                            Event::destroy($event->id);
                            $result = $result.'Rendez-vous '.$event->id.' supprimé ! ';
                            break;
                        default:
                            # code...
                            break;
                    }

                }
                \DB::commit();
                
                // Clear relevant caches
                Cache::forget('events_with_patients');
                foreach ($events as $event) {
                    Cache::forget("medecin_{$event->resourceId}_events");
                }
            
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
            }
        
            return response()->json(['info' => $result]);
        }
    
        abort(404);
    }
}









