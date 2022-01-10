<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ville;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class VilleController extends Controller
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
        $verification_ville = Ville::where(['libelle_ville' =>$request->libelle_ville])->first() ;
         
        if ($verification_ville) {

            Session()->flash('error',"Cette ville existe deja dans le liste");
            return back();
        }

        $ville= new Ville();

        $ville->libelle_ville=$request->libelle_ville;
        $ville->etat_ville=1;

        $ville->save();

        return back()->with('success', 'Enregistrement effectué avec succè');
    }


    //List des villes
    public function getAllVille()
    {
        $villes = Ville::where(['etat_ville' =>1])->get();

        return view('pages_backend/ville/list_ville')->with(["villes" => $villes])->with(["page" => "liste-ville"]);
				
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
        $ville = Ville::where(['id_ville' =>$id])->first() ;

        $ville->libelle_ville=$request->libelle_ville;
         
        $ville->save();

        return back()->with('success', 'Modification effectuée avec succè');
    }

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ville = Ville::where(['id_ville' =>$id])->first() ;

        $ville->etat_ville= 0;
         
        $ville->save();
        return back()->with('success', 'Suppression effectuée avec succè');
    }
}
