<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class UsersController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('update', User::class);

        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);

        $cacheKey = "users_with_roles.page_{$page}.per_{$perPage}";

        // Cache users list with roles
        $users = Cache::tags(['users'])->remember($cacheKey, 1800, function () use ($perPage) {
            return User::with('roles:id,name')
                ->select('id', 'name', 'prenom', 'login', 'telephone', 'role_id', 'created_at')
                ->orderBy('id', 'asc')
                ->paginate($perPage);
        });

        return view('admin.users.index', compact('users'));
    }
    public function create()
    {
        $this->authorize('update', User::class);

        $roles = Role::all();
        return view('admin.users.create')->with('roles', $roles);
    }

    public function store(Request $request)
    {
        $this->authorize('update', User::class);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'lieu_naissance' => ['required', 'string', 'max:255'],
            'date_naissance' => ['date'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'unique:users', 'numeric', 'digits:9'],
            'sexe' => ['required'],
            'login' => ['required', 'string', 'min:6', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        DB::transaction(function () use ($request) {
            User::create([
                'name' => $request->input('name'),
                'prenom' => $request->input('prenom'),
                'onmc' => $request->input('onmc'),
                'specialite' => $request->input('specialite'),
                'lieu_naissance' => $request->input('lieu_naissance'),
                'date_naissance' => $request->input('date_naissance'),
                'telephone' => $request->input('telephone'),
                'sexe' => $request->input('sexe'),
                'login' => $request->input('login'),
                'role_id' => $request->input('roles'),
                'password' => Hash::make($request->input('password')),
            ]);
        });
        
        // Clear users cache
        Cache::tags(['users', 'patients'])->flush();

        return redirect()->route('users.index')
            ->with('success', "L'utilisateur a bien été créé");
    }
    public function edit($id)
    {
//        $this->authorize('update', $user);

        $roles = Role::all();
        $user = User::with('roles')->find($id);

        return view("admin.users.edit")->with('user', $user)->with('roles', $roles);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', User::class);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lieu_naissance' => ['required', 'string', 'max:255'],
            'date_naissance' => ['date:"dd/mm/yyyy"'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'unique:users', 'numeric', 'digits:9'],
            'sexe' => ['required'],
            'login' => ['required', 'string', 'min:6', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $mdpuser = $request->input('password');
        //$verifypass = password_verify($mdpuser, $user->password);

        //dd($verifypass);
        DB::transaction(function () use ($user, $request) {
            $user->name = $request->input('name');
            $user->lieu_naissance = $request->input('lieu_naissance');
            $user->date_naissance = $request->input('date_naissance');
            $user->prenom = $request->input('prenom');
            $user->telephone = $request->input('telephone');
            $user->sexe = $request->input('sexe');
            $user->login = $request->input('login');
            $user->role_id = $request->input('roles');
            $user->password = Hash::make($request->input('password'));

            $user->save();
        });

        Cache::tags(['users', 'patients'])->flush();

        return redirect()->route('users.index')->with('success',"L'utilisateur a bien été modifier");
    }


    public function destroy(User $user)
    {
        $this->authorize('update', $user);

        DB::transaction(function () use ($user) {
            $user->delete();
        });

        Cache::tags(['users', 'patients'])->flush();

        return redirect()->route('users.index')->with('success', "L'utilisateur a bien été supprimé");
    }

    public function changePassword(Request $request, User $user)
    {
        $this->authorize('changePassword', User::class);

        $request->validate([
            'old_pass' =>['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $old_pass = $request->input('old_pass');
        $verifypass = password_verify($old_pass, $user->password);

        //dd($verifypass);
        if ($verifypass)
        {
            $user->password = Hash::make($request->password);

            $user->save();
        }else{
            return redirect()->route('users.profile', $user->id)->with('error', "L'ancien mot de passe est invalide");
        }

        return redirect()->route('users.profile',$user->id)->with('success',"Mot de passe mis à jour avec succès");
    }
    
    public function profile(Request $request, $id){
        $user = User::with('roles')->find($id);

        return view("admin.users.profile")->with('user', $user);
    }
}










