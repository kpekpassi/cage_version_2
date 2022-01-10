<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adresse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class AdresseController extends Controller
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
		$adresse = new Adresse();
		
		$adresse->ville_adresse = $request->ville;
		$adresse->pays_adresse = $request->pays;
		$adresse->description_adresse = $request->description;
        $adresse->telephone = $request->telephone;
		$adresse->id_user = Cookie::get('id_user');
		$adresse->etat_adresse=1;
		
		$adresse->save();
		
		Session()->flash('success','Adresse ajoutée avec succès. ');	
		return redirect()->back();
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
	
	public function show_adresse_client()
    { 
		$id_user= Cookie::get('id_user');

        $adresse = Adresse::where(['id_user' =>$id_user])
		->where('etat_adresse', '=', 1)
		->first() ;
		
		return view('pages_frontend/ajouter_adresse',compact('adresse'));
		
    }
	
	public function liste_adresse_client()
    { 
		$id_user= Cookie::get('id_user');

        $adresses = Adresse::where(['id_user' =>$id_user])->where('etat_adresse', '=', 1)->get() ;
		
		return view('pages_frontend/nouveau_adresse',compact('adresses'));
		
    }
	
	//récuperation des adresses
	public function modifier_adresse_client($id)
    { 
		$id_user = Cookie::get('id_user');

        $adresse_client = Adresse::where(['id_adresse' =>$id])->where('etat_adresse', '=', 1)->first() ;
		
		return view('pages_frontend/adresse',compact('adresse_client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $adresse = Adresse::where(['id_adresse' =>$id])->first() ;
		 
		$adresse->ville_adresse = $request->ville;
		$adresse->pays_adresse = $request->pays;
		$adresse->description_adresse = $request->description;
        $adresse->telephone = $request->telephone;
		
		$adresse->save();
		
		Session()->flash('success','Informations modifiées avec succès. ');	
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
        $adresse = Adresse::where(['id_adresse' =>$id])->first() ;

         $adresse->etat_adresse=0;
         $adresse->save();

		Session()->flash('success','Adresse supprimer avec succès. ');
        return redirect()->back();
    }

}
