<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partenaire;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class PartenaireController extends Controller
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

        $partenaire= new Partenaire();

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            $nom_image=str_replace(' ','',$request->sigle_partenaire);
            Image::make($image)->save('files_upload/partenaire/'.$nom_image.'.jpg');

            $file_name ='files_upload/partenaire/'.$nom_image.'.jpg';

          }else{

            $file_name ="";
         }
        $partenaire->sigle_partenaire=$request->sigle_partenaire;
        $partenaire->logo_partenaire=$file_name;

        $partenaire->save();

        return back()->with('success', 'Enregistrement effectué avec succè');
    }


    //List des villes
    public function getAllPartenaire()
    {
        $partenaires = Partenaire::all();

        return view('pages_backend/partenaire/list_partenaire')->with(["partenaires" => $partenaires])->with(["page" => "liste-ville"]);
				
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
        $partenaire = Partenaire::where(['id_partenaire' =>$id])->first() ;

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            $nom_image=str_replace(' ','',$request->sigle_partenaire);
            Image::make($image)->save('files_upload/partenaire/'.$nom_image.'.jpg');

            $file_name ='files_upload/partenaire/'.$nom_image.'.jpg';

          }else{

            $file_name =$partenaire->logo_partenaire;
         }
        $partenaire->sigle_partenaire=$request->sigle_partenaire;
        $partenaire->logo_partenaire=$file_name;
         
        $partenaire->save();

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
        $partenaire = Partenaire::where(['id_partenaire' =>$id])->first() ;
         
        $partenaire->delete();
        return back()->with('success', 'Suppression effectuée avec succè');
    }
}
