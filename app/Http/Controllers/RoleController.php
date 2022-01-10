<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class RoleController extends Controller
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
        // $verification_ville = Ville::where(['libelle_ville' =>$request->libelle_ville])->first() ;
         
        // if ($verification_ville) {

        //     Session()->flash('error',"Cette ville existe deja dans le liste");
        //     return back();
        // }

        $role= new Role();

        $role->libelle_role=$request->libelle_role;
        $role->code_role=$request->code_role;

        $role->save();

        return back()->with('success', 'Enregistrement effectué avec succè');
    }


    //List des villes
    public function getAllRole()
    {
        $roles = Role::all();

        return view('pages_backend/role/list_role')->with(["roles" => $roles])->with(["page" => "liste-ville"]);
				
    }
	
	public function liste_role()
    {
        $roles = Role::all();

        return view('modals/ajout/add_utilisateur', compact('roles'));
				
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
        $role = Role::where(['id_role' =>$id])->first() ;

        $role->libelle_role=$request->libelle_role;
         
        $role->save();

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
        $role = Role::where(['id_role' =>$id])->first() ;
         
        $role->delete();

        return back()->with('success', 'Suppression effectuée avec succè');
    }
}
