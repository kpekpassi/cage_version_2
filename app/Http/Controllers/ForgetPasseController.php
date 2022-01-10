<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ForgetPasseController extends Controller
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
            'useremail' => 'required|string',
        ]);
		
		$id_categorie=0;
		
		$verification_email = User::where(['email_user' =>$request->useremail])->first() ;
		
		if ($verification_email == null) {

            Session()->flash('error',"Cette adresse mail n'est pas enrégistrer, Merci de vérifier et réessayer. ");
            return back()->withErrors($validator)->withInput();
        } 
		else
		{
			    $email = $verification_email->email_user; 
                $id_client = $verification_email->id_user;
                $e_nom = "Bonjour $verification_email->nom_user  $verification_email->prenom_user " ;

                //dd($email);
  
                // titre du mail
                $titre ="Confirmation de récupération de mot de passe"; 
				
                $description ="Vous avez demandé à réinitialiser vos identifiants de connexion sur Cage Batiment.<br />
                Cette opération vous attribuera un nouveau mot de passe.<br /><br />
                Pour confirmer cette action, cliquez sur le lien suivant : <br />
                https://www.cagebatiment.com/nouveau-mot-de-passe/$id_client <br /><br /><br /> 
                Si ce n'est pas vous, ignorez cet email." ; 

                $contact = "Contact: +228 70 45 37 85 | 96 35 80 90 | 90 90 49 03 </br> Email: cagetogo@gmail.com </br>  Site Web : www.cagebatiment.com" ;

                $contenu = $e_nom . '<br /><br />' . $description .'<br /><br /><br />'.$contact ;


                // envoi du mail HTML
                $from = "From: CAGE Bâtiment <cagetogo@gmail.com>\nMime-Version:";
                $from .= " 1.0\nContent-Type: text/html; charset=ISO-8859-1\n";
                // envoie du mail
               mail($email,$titre,$contenu,$from);

			Session()->flash('success','Merci de consulter votre boîte mail, un message vous a été envoyé. Si vous ne le retrouver pas, consulter le Spam. ');
            
			return redirect('/');
		}
		
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
        $id_categorie=0;
        
		$password = User::where(['id_user' =>$id])->first();
        return view('pages_frontend/nouveau_passe',compact('password', 'id_categorie'));
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
		$validator = Validator::make($request->all(), [
            'useremail' => 'required|string',
        ]);
		
        $result = User::where(['email_user' => $request->username])->first();
		
		if ($result == null) {
            Session()->flash('error','Cette adresse mail n\'est pas enrégistrer dans le système');
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
		
		$password = User::where(['id_user' =>$id])->first();
		
		$password->password_user = password_hash($request->userpassword, PASSWORD_DEFAULT);
		
		$password->save();
		
        //return $this->connexion_auto($request->useremail, $request->userpassword);
        Session()->flash('success','Félicitation, Mot de passe changer avec succès. ');	
        return redirect('/login');
			
        
    }
	
	//
	public function connexion_auto($email, $passe)
    {
		
		$result = User::where(['email_user' => $email])->first();
        /* verifie si le les identifiant de l'utilisateur sont null il envoi erruer*/
        if ($result == null) {
            Session()->flash('error','Nom d\'utilisateur ou mot de passe incorrecte ');
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
            
            return redirect()->to('/detail-profil-client');

		}else{
          
            Session()->flash('error','Nom d\'utilisateur ou mot de passe incorrecte ');
            return redirect()->back();

        }
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
