<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ville;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class InscriptionController extends Controller
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

        if (strlen($request->username) < 3 || strlen($request->userprenom) < 3) {
            Session()->flash('error','Le nom ou prenom trop cours!');
            return back()->withErrors($validator)->withInput();
        }
		
		$verification_email = User::where(['email_user' =>$request->useremail])->first() ;
		
		if ($verification_email) {

            Session()->flash('error',"Ce mail existe déjà sous un compte, Merci d'utiliser un autre. ");
            return back()->withErrors($validator)->withInput();
        }
		
		if (strlen($request->userpassword) < 8) {
            Session()->flash('error','Mot de passe trop cours !');
            return back()->withErrors($validator)->withInput();
        }
		
		if($request->userpassword != $request->userpasswordconfirm){
			Session()->flash('error','Les mots de passe ne sont pas conforment ! Merci de re-saisir. ');	
			return back()->withErrors($validator)->withInput();
		}

        if (strlen($request->usertelephone)<8) {
            Session()->flash('error','Numeros de telephone incorrecte');
            return back()->withErrors($validator)->withInput();
        }
	
		$user = new User();

		$user->nom_user = $request->username;
		$user->prenom_user = $request->userprenom;
		$user->email_user = $request->useremail;
		$user->password_user = password_hash($request->userpassword, PASSWORD_DEFAULT);
		$user->sexe_user = $request->usercivilite;
        $user->telephone_user = $request->usertelephone;
        $user->id_ville = $request->userville;
        $user->quartier_user = $request->userquartier;
		$user->ok_newsletter = $request->usernews;
		$user->type_user = 2;
	   
        $user->save();

                $e_nom = "Commande de $user->nom_user  $user->prenom_user" ;
                $email = $user->email_user; 
                // titre du mail
                $titre ="Bienvenue sur Cage Batiment"; 
				
                $description ="Merci pour votre inscription"; 

                $contact = "Contact: +228 70 45 37 85 | 96 35 80 90 | 90 90 49 03 </br> Email: cagetogo@gmail.com      <br>        Site Web : www.cagebatiment.com" ;

                $contenu = $e_nom . '<br /><br />' . $description .'<br /><br /><br />'.$contact ;

                // envoi du mail HTML
                $from = "From: CAGE Bâtiment <cagetogo@gmail.com>\nMime-Version:";
                $from .= " 1.0\nContent-Type: text/html; charset=ISO-8859-1\n";
                // envoie du mail
               mail($email,$titre,$contenu,$from);
        
		return $this->connexion_auto($request->useremail, $request->userpassword);
		
    }
	
    public function connexion_auto($email, $passe)
    {
		
		$result = User::where(['email_user' => $email])->first();
        /* verifie si le les identifiant de l'utilisateur sont null il envoi erruer*/
        if ($result == null) {
            Session()->flash('error','Identifiants incorrectes. Merci de réessayer ');
            return redirect()->back();
            /* si non il envoi les resultats de la requete */
        }  
		
        if ($result->type_user == 2 && password_verify($passe, $result->password_user))
        {
            //**** mise en cookie des données de l'utilisateur**//

            Cookie::queue('email_user', $result->email_user , 5000);
            Cookie::queue('nom_user', $result->nom_user , 5000);
            Cookie::queue('prenom_user', $result->prenom_user , 5000);
            Cookie::queue('id_user', $result->id_user , 5000);
            
            //return redirect()->to('/detail-profil-client');
            return redirect()->to('/detail-profil-client')->with('success', 'Bienvenu , votre compte à bien été enregistrer');

		}else{
          
            Session()->flash('error','Nom d\'utilisateur ou mot de passe incorrecte ');
            return redirect()->back();

        }
	}
	

    public function passe_client()
    { 
        $id_user= Cookie::get('id_user');

        $user = User::where(['id_user' =>$id_user])->first() ;

        return view('pages_frontend/changer_passe',compact('user'));
    }
    
	//gestion des mot de passes oublier
	public function passe_oublier(){
		
		$id_categorie=0;

        return view('pages_frontend/mot_passe_oublier',compact('id_categorie'));
	}
	
	//envoi du lien par mail
	public function lien_recuperation(Request $request){
		
		$id_categorie=0;
		
		$verification_email = User::where(['email_user' =>$request->useremail])->first() ;
		
		if ($verification_email == null) {

            Session()->flash('error',"Cette adresse mail n'est pas enrégistrer, Merci de vérifier et réessayer. ");
            return back()->withErrors($validator)->withInput();
        }
		
		

        return view('pages_frontend/mot_passe_oublier',compact('id_categorie'));
	}
	
	//
	public function show_profil_client()
    { 
        $id_user= Cookie::get('id_user');

        $user = User::where(['id_user' =>$id_user])->first() ;

        return view('pages_frontend/mon_compte',compact('user'));
    }
	
	public function show_info_client()
    {  
        $villes = Ville::where(['etat_ville' =>1])->get() ;
        $id_user= Cookie::get('id_user');

        $user = User::where(['id_user' =>$id_user])->first() ;

        return view('pages_frontend/info_personel',compact('user','villes'));
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
       
		$user = User::where(['id_user' =>$id])->first() ;
		 
		$user->nom_user = $request->username;
		$user->prenom_user = $request->userprenom;
		$user->email_user = $request->useremail;
        $user->sexe_user = $request->usercivilite;
        $user->id_ville = $request->userville;
        $user->quartier_user = $request->userquartier;
		$user->telephone_user = $request->usertelephone;
		
		$user->save();
		
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
        //
    }
}
