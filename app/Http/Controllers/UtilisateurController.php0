<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		 $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'userprenom' => 'required|string',
            'useremail' => 'required|string',
            'usercivilite' => 'required|string',
            'usertelephone' => 'required|string',
            'usernews' => 'required|string',
        ]);
		
		$verification_email = User::where(['email_user' =>$request->useremail])->first() ;
		
		if ($verification_email) {
            Session()->flash('error',"Ce mail existe déjà sous un compte, Merci d'utiliser un autre. ");
            return back()->withErrors($validator)->withInput();
        }
	
		$user = new User();

		$user->nom_user = $request->username;
		$user->prenom_user = $request->userprenom;
        $user->email_user = $request->useremail;
        $user->id_role = $request->id_role;
        $user->password_user = password_hash($request->userpassword, PASSWORD_DEFAULT);
        $user->password_visible = $request->userpassword ;
		$user->type_user = 1 ;
	   
        $user->save();
        
        return back()->with('success', 'Enregistrement effectué avec succè');
		
    }
	
   
     //List des villes
     public function getAllUtilisateur()
     {
         $utilisateurs = DB::table('user')
        ->where('user.id_role', '!=', null)
        ->get();

         $roles = Role::all();
 
         return view('pages_backend/utilisateur/list_utilisateur',compact('utilisateurs','roles'));
                 
     }

	
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
		$utilisateur = User::where(['id_user' =>$id])->first() ;
		 
		$utilisateur->nom_user = $request->username;
		$utilisateur->prenom_user = $request->userprenom;
        $utilisateur->email_user = $request->useremail;
        //$utilisateur->id_role = $request->id_role;
        $utilisateur->password_user = password_hash($request->userpassword, PASSWORD_DEFAULT);
        $utilisateur->password_visible = $request->userpassword ;
       
		$utilisateur->save();
		
		Session()->flash('success','Informations modifiées avec succès.');	
		return redirect()->back();
		
    }
	

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $utilisateur = User::where(['id_user' =>$id])->first() ;

        $utilisateur->delete();
		
		Session()->flash('success','Suppression effectuee avec succès.');	
		return redirect()->back();
    }
}
