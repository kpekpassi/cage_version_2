<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\SousCategorie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class CategorieController extends Controller
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
        $verification_categorie = Categorie::where(['libelle_categorie' =>$request->libelle_categorie])->first() ;
         
        if ($verification_categorie) {

            Session()->flash('error',"Cette categorie existe deja dans le catalogue, si elle était désactivé, elle sera réactivé");
			//dd($verification_categorie);
			$categorie = Categorie::where(['id_categorie' =>$verification_categorie->id_categorie])->first() ;
			
			$categorie->etat_categorie=1;

			$categorie->save();
            return back();
        }

        $categorie= new Categorie();

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $nom_image=str_replace(' ','',$request->libelle_categorie);
            Image::make($image)->save('files_upload/categorie/'.$nom_image.'.jpg');

            $file_name ='files_upload/categorie/'.$nom_image.'.jpg';

          }else{

            $file_name ="";
         }

        $categorie->libelle_categorie=$request->libelle_categorie;
        $categorie->image_categorie=$file_name;
        $categorie->etat_categorie=1;
        $categorie->position=$request->position_categorie;

        $categorie->save();

        return back()->with('success', 'Enregistrement effectué avec succè');
    }

 // Enregistrement de sous categorie
    public function store_sous_categorie(Request $request)
    {
        $sous_categorie= new SousCategorie();

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $nom_image=str_replace(' ','',$request->libelle_sous_categorie);
            Image::make($image)->save('files_upload/sous_categorie/'.$nom_image.'.jpg');

            $file_name ='files_upload/sous_categorie/'.$nom_image.'.jpg';

          }else{

            $file_name ="";
         }

        $sous_categorie->libelle_sous_categorie=$request->libelle_sous_categorie;
        $sous_categorie->id_categorie=$request->id_categorie;
        $sous_categorie->image_sous_categorie= $file_name;
        $sous_categorie->etat_sous_categorie=1;

        $sous_categorie->save();

        //Session()->flash('succes'," Sous categorie enregistrement effectuer avec succè");
        return back()->with('success', 'Sous categorie enregistrement effectuer avec succè');
    }


    //List des categories
    public function getAllCategorie()
    {
        // $categories = Categorie::where(['etat_categorie' =>1])->get() ;
        $categories = DB::table('categorie')
        ->where('etat_categorie', '=', 1)
        ->orderBy('position', 'ASC')
        ->get();

        return view('pages_backend/categorie/list_categorie',compact('categories'));
    }

    //List des sous-categories
    public function getAllSousCategorie()
    {
        $categories = Categorie::where(['etat_categorie' =>1])->get() ;

        return view('pages_backend/categorie/list_sous_categorie',compact('categories'));
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
        $categorie = Categorie::where(['id_categorie' =>$id])->first() ;

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            Image::make($image)->save('files_upload/sous_categorie/'.$id.'.jpg');

            $file_name ='files_upload/sous_categorie/'.$id.'.jpg';

          }else{

            $file_name =$categorie->image_categorie;
         }

        $categorie->libelle_categorie=$request->libelle_categorie;
        $categorie->position=$request->position_categorie;
        $categorie->image_categorie= $file_name;
		
        $categorie->save();

        return back()->with('success', 'Modification effectuée avec succè');
    }


    public function update_sous_categorie(Request $request, $id)
      {
        $sous_categorie = SousCategorie::where(['id_sous_categorie' =>$id])->first() ;

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            Image::make($image)->save('files_upload/categorie/'.$id.'.jpg');

            $file_name ='files_upload/categorie/'.$id.'.jpg';

          }else{

            $file_name =$sous_categorie->image_sous_categorie;
         }

        $sous_categorie->libelle_sous_categorie=$request->libelle_sous_categorie;
        $sous_categorie->image_sous_categorie=$file_name;
         
        $sous_categorie->save();

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
        $categorie = Categorie::where(['id_categorie' =>$id])->first() ;
		
		//vérifions si des produits sonts liés à la sous catégorie
		$nb_categorie = DB::table('sous_categorie')
        ->join('categorie', 'categorie.id_categorie', '=', 'sous_categorie.id_categorie')
        ->where('sous_categorie.id_categorie', '=', $categorie->id_categorie)
        ->where('sous_categorie.etat_sous_categorie', '=', 1)
        ->count();
		
		if($nb_categorie==0){
				$categorie->etat_categorie= 0;
         
				$categorie->save();
				return back()->with('success', 'Suppression effectuée avec succès');
		}else{
			Session()->flash('error',"Impossible de supprimer cette catégorie, car est liée à des sous catégories.");
			return back();
		}
		
    }
	
	//suppression de la sous catégorie
	public function supprimer_ss_categorie($id)
    {
        $ss_categorie = SousCategorie::where(['id_sous_categorie' =>$id])->first() ;
		
		//vérifions si des produits sonts liés à la sous catégorie
		$nb_produit = DB::table('produit')
        ->join('sous_categorie', 'produit.id_sous_categorie', '=', 'sous_categorie.id_sous_categorie')
        ->where('produit.id_sous_categorie', '=', $ss_categorie->id_sous_categorie)
        ->count();
		
		if($nb_produit==0){
			$ss_categorie->etat_sous_categorie= 0;
         
			$ss_categorie->save();
			return back()->with('success', 'Suppression effectuée avec succès');
		}else{
			Session()->flash('error',"Impossible de supprimer cette sous catégorie, car elle est liée à des produits.");
			return back();
		}

    }
	
}
