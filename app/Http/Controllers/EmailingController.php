<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ville;
use App\Models\User;
use App\Models\Emailing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Mail;
use App\Mail\EnvoiMail;
use Alert;

class EmailingController extends Controller
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
      $i = 0;
      foreach ($request->ville as $ville) {
           $users = User::where(['id_ville' =>$request->ville[$i]])->get() ;

        foreach($users as $user){
                $email = $user->email_user; 
                $e_nom = "Bonjour Mr/Mme $user->nom_user $user->prenom_user " ;

                // titre du mail
                $titre =$request->titre_email; 
				
                $description =$request->description_email ; 

                $contact = "Contact: +228 70 45 37 85 | 96 35 80 90 | 90 90 49 03 </br> Email: cagetogo@gmail.com </br>  Site Web : www.cagebatiment.com" ;

                $contenu = $e_nom . '<br /><br />' . $description .'<br /><br /><br />'.$contact ;

                // envoi du mail HTML
                $from = "From: CAGE Bâtiment <cagetogo@gmail.com>\nMime-Version:";
                $from .= " 1.0\nContent-Type: text/html; charset=ISO-8859-1\n";
                // envoie du mail
               mail($email,$titre,$contenu,$from);
        }

        $email = new Emailing();
       
        $email->titre_email= $request->titre_email;
        $email->description_email= $request->description_email;
        $email->id_ville= $request->ville[$i];

        $email->save();

        $i++;

    }
        
       return back()->with('success', 'Email envoyes avec succè');
    }

    public function reenvoiMail($id)
    {
        $email = Emailing::where(['id_email' =>$id])->first() ;
         
        $users = User::where(['id_ville' =>$email->id_ville])->get() ;

    //     foreach($users as $user){
    //         $email = $user->email_user; 
    //         $e_nom = "Bonjour Mr/Mme $user->nom_user $user->prenom_user " ;
    //         // titre du mail
    //         $titre =$email->titre_email; 
            
    //         $description =$email->description_email ; 

    //         $contact = "Contact: +228 70 45 37 85 | 96 35 80 90 | 90 90 49 03 </br> Email: cagetogo@gmail.com </br>  Site Web : www.cagebatiment.com" ;

    //         $contenu = $e_nom . '<br /><br />' . $description .'<br /><br /><br />'.$contact ;


    //         // envoi du mail HTML
    //         $from = "From: CAGE Bâtiment <cagetogo@gmail.com>\nMime-Version:";
    //         $from .= " 1.0\nContent-Type: text/html; charset=ISO-8859-1\n";
    //         // envoie du mail
    //        mail($email,$titre,$contenu,$from);
    // }

        return back()->with('success', 'Email envoyes avec succè');
    }


    public function EmailPersonnaliser(Request $request)
    {
        $tab = json_decode($request->selectedids);
        for ($i =0 ; $i <= count($tab) - 1; $i++){
            $user = User::where(['id_user' =>$tab[$i]])->first() ;
            $email = $user->email_user; 
            $e_nom = "Bonjour Mr/Mme $user->nom_user $user->prenom_user " ;

            // titre du mail
            $titre =$request->titre_email; 
            
            $description =$request->description_email ; 

            $contact = "Contact: +228 70 45 37 85 | 96 35 80 90 | 90 90 49 03 </br> Email: cagetogo@gmail.com </br>  Site Web : www.cagebatiment.com" ;

           $logo='<img src="{{asset("files_upload/LOGOT.png")}}" alt=" " height="50px" widht="50px"></br>';

            $contenu = $e_nom . '<br/><br/>'. $description .'<br /><br /><br />'.$contact.'<br /><br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$logo ;

            // envoi du mail HTML
            $from = "From: CAGE Bâtiment <cagetogo@gmail.com>\nMime-Version:";
            $from .= " 1.0\nContent-Type: text/html; charset=ISO-8859-1\n";
            // envoie du mail
           mail($email,$titre,$contenu,$from);
        }
        
        return back()->with('success', 'Email envoyes avec succè');
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

    public function getAllEmail()
    {
        $villes = Ville::where(['etat_ville' =>1])->get();
        $emails = Emailing::all();

        return view('pages_backend/emailing/list_message',compact('villes','emails'));
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
        $email = Emailing::where(['id_email' =>$id])->first() ;
       
        $email->titre_email= $request->titre_email;
        $email->description_email= $request->description_email;
        $email->id_ville= $request->id_ville;

        $email->save();
        
       return back()->with('success', 'Modification effectuee avec succè');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $email = Emailing::where(['id_email' =>$id])->first() ;
         
        $email->delete();

        return back()->with('success', 'Suppression effectuée avec succè');
    }
}
