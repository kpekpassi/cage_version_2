<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remise;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class RemiseController extends Controller
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
        $remise= new Remise();

        $remise->reference_commande=$request->reference_commande;
        $remise->id_commande=$request->id_commande;
        $remise->pourcentage_remise=$request->remise;
        $remise->etat_remise=1;

        $remise->save();

        Session()->flash('success',"Remise effectuée avec succè");
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
        $remise = Remise::where(['id_remise' =>$id])->first() ;

        $remise->pourcentage_remise = $request->remise ;

        $remise->save();

        Session()->flash('success',"Modification effectuée avec succè");
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
        $remise = Remise::where(['id_remise' =>$id])->first() ;

        $remise->delete();

        Session()->flash('success',"Suppression effectuée avec succè");
        return redirect()->back();

    }
}
