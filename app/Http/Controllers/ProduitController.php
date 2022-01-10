<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\PhotoProduit;
use App\Models\Boutique;
use App\Models\Promotion;
use App\Models\SousCategorie;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;

class ProduitController extends Controller
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
        $boutiques = Boutique::all() ;
        $sous_categories = SousCategorie::all() ;

        return view('pages_backend/produit/ajouter_produit',compact('boutiques','sous_categories'));
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
            'nom_produit' => 'required|string',
        ]);

         $verification_produit = Produit::where(['nom_produit' =>$request->nom_produit ,'id_boutique' =>$request->id_boutique])->first() ;

         $categorie = SousCategorie::where(['id_sous_categorie' =>$request->id_sous_categorie])->first() ;

        if ($verification_produit) {

            Session()->flash('error',"Cet produit existe deja dans cette boutique , Veuillez mettre à jour la quantité");
            return back()->withErrors($validator)->withInput();
        }

        if (empty($request->tva_applicable)) {

            Session()->flash('error',"Veuillez choisir le TVA applicable");
            return back()->withErrors($validator)->withInput();
        }

        if (empty($request->nouveau_produit)) {

            Session()->flash('error',"Veuillez choisir l'etat du produit");
            return back()->withErrors($validator)->withInput();
        }

        if (empty($request->id_boutique)) {

            Session()->flash('error',"Veuillez choisir la boutique correspondante");
            return back()->withErrors($validator)->withInput();
        }

        if (empty($request->id_sous_categorie)) {

            Session()->flash('error',"Veuillez choisir la categorie du produit");
            return back()->withErrors($validator)->withInput();
        }

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            $nom_image1=str_replace(' ','',$request->nom_produit);
            $nom_image=str_replace('/','',$nom_image1);
            Image::make($image)->save('files_upload/produit/'.$nom_image.'.jpg');

            $file_name ='files_upload/produit/'.$nom_image.'.jpg';

          }else{

            $file_name ="";
         }

        $produit = new Produit();

        $produit->nom_produit= $request->nom_produit;
        $produit->description_produit= $request->description_produit;
        $produit->caracteristique_produit= $request->caracteristique_produit;
        $produit->quantite_produit= $request->quantite_produit;
        $produit->prix_ht_produit= $request->prix_produit;
        $produit->id_sous_categorie= $request->id_sous_categorie;
        $produit->id_categorie= $categorie->id_categorie;
        $produit->id_boutique= $request->id_boutique;
        $produit->promotion= 0;
        $produit->nouveau_produit= $request->nouveau_produit;
        if($request->tva_applicable=='oui'){
            $produit->tva_applicable= $request->tva_applicable;
            $produit->taux_tva= $request->taux_tva;

            $montant_tva=($request->prix_produit*$request->taux_tva)/100;
            $produit->montant_tva= $montant_tva;

            $prix_ttc= $request->prix_produit+$montant_tva ;
            $produit->prix_ttc= $prix_ttc;

        }else{
            $produit->tva_applicable= $request->tva_applicable;
            $produit->taux_tva= 'NULL';
            $produit->montant_tva= 'NULL';
            $produit->prix_ttc=  $request->prix_produit;
        }
        $produit->image_produit=$file_name;
        $produit->etat_produit= 1 ;

        $produit->save();

        if($produit){

            $i=0; 
            foreach($request->photo as $photo){
                $taille_max = 104857600; // 100 Mo
                $file_name_photo = $photo->getClientOriginalName();
                $file_extension = strrchr($file_name_photo, ".");
                $file_tmp_name = $photo->getPathname();
                $filedest = 'files_upload/produit_image/'.$file_name_photo;

                if(move_uploaded_file($file_tmp_name,$filedest)){
                         $chemin_photo=$filedest;
                 }else{
                     $chemin_photo="null";
                 }

                $photo_produit= new PhotoProduit(); 

                $photo_produit->id_produit=$produit->id_produit;
                $photo_produit->photo_produit=$chemin_photo;
            
                $photo_produit->save();

             $i++;
          }

        }

        return back()->with('success', 'Produit enregistré avec succè');
    }

    // ajouter une promotion du produit
    public function promotionProduit(Request $request)
    {
        $produit = Produit::where(['id_produit' =>$request->id_produit])->first() ;
        $promotion = new Promotion();
        
        $date_jour = date('Y-m-d');
        
        if($request->date_debut_promotion < $date_jour){
            return back()->with('error', "Date debut promotion invalide ( $request->date_debut_promotion ) , elle doit etre superieure à la date du jour");
        }

        if($request->date_fin_promotion <= $date_jour && $request->date_fin_promotion <= $request->date_debut_promotion){
            return back()->with('error',"Date fin promotion invalide ( $request->date_fin_promotion ),elle doit etre superieure à la date debut");
            
        }
         
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
         srand((double)microtime()*1000000);
         $i = 0 ;
         $code = '' ;
         while ($i <= 4) {
             $num = rand() % 33;
             $tmp = substr($chars, $num, 1);
             $code = $code . $tmp;
             $i++;
         }

        $promotion->pourcentage_promotion= $request->pourcentage_promotion;
        $promotion->date_debut_promotion= $request->date_debut_promotion;
        $promotion->date_fin_promotion= $request->date_fin_promotion;
        $promotion->code_promotion= $code;
        $promotion->id_produit= $request->id_produit;

        $produit->promotion= 1;

        $promotion->save();
        $produit->save();

        return back()->with('success', 'Promotion enregistrée avec succè');

    }

     //List des produits
     public function getAllProduit()
     {
         $produits = Produit::where(['etat_produit' =>1])->get() ;
 
         return view('pages_backend/produit/list_produit',compact('produits'));
     }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //$produit = Produit::where(['id_produit' =>$id])->first() ;
        $produit_images = PhotoProduit::where(['id_produit' =>$id])->get() ;
        $promotion = Promotion::where(['id_produit' =>$id])->first() ;
        $produit = DB::table('produit')
        ->join('sous_categorie', 'produit.id_sous_categorie', '=', 'sous_categorie.id_sous_categorie')
        ->join('boutique', 'produit.id_boutique', '=', 'boutique.id_boutique')
        ->where('produit.id_produit', '=', $id)
        ->first();
 
        return view('pages_backend/produit/detail_produit',compact('produit','produit_images','promotion'));
    }
	
	public function detail_produit($id)
    {
		$promotion = Promotion::where(['id_produit' =>$id])->first() ;
        
        $id_categorie = 0;

		$produit = DB::table('produit')
        ->join('sous_categorie', 'produit.id_sous_categorie', '=', 'sous_categorie.id_sous_categorie')
        ->join('boutique', 'produit.id_boutique', '=', 'boutique.id_boutique')
        ->where('produit.id_produit', '=', $id)
        ->first();
		
		$photo_produits= DB::table('photo_produit')
        ->where('id_produit', '=', $id)
        ->limit(2)
		->get(); 
		
		$sous_categories = SousCategorie::All();
		
		$produits_idem_ss_cats = DB::table('produit')
		->where('produit.id_sous_categorie', '=', $produit->id_sous_categorie)
		->paginate(12);
		
		$produits_idem_cats = DB::table('sous_categorie')
		->join('produit', 'produit.id_sous_categorie', '=', 'sous_categorie.id_sous_categorie')
		->where('produit.id_sous_categorie', '=', $produit->id_sous_categorie)
		->paginate(12);
		
		$produits_autres_cats = DB::table('sous_categorie')
		->where('sous_categorie.id_categorie', '=', $produit->id_categorie)
		->paginate(12);
		
		 $nouveau_produits = DB::table('produit')
         ->where('produit.etat_produit', '=', 1)
         ->where('produit.nouveau_produit', '=', 'Nouveau')
         ->paginate(12);

         $commentaires = DB::table('commentaire')
         ->where('id_produit', '=', $id)
         ->where('commentaire.commentaire_parent', '=', 0)
         ->orderBy('commentaire.id_commentaire', 'asc')
         ->get();


         $commentaires_parent = DB::table('commentaire')
         ->where('id_produit', '=', $id)
         ->where('commentaire.commentaire_parent', '!=', 0)
         ->orderBy('commentaire.id_commentaire', 'asc')
         ->get();

		
		return view('pages_frontend/detail_produit',compact('promotion', 'nouveau_produits', 'produits_autres_cats', 'produits_idem_cats', 'produits_idem_ss_cats', 'sous_categories', 'produit', 'photo_produits','id_categorie','commentaires','commentaires_parent'));
    }	
	
	//
	public function liste_produit_partenaire($id)
    {
		$id_categorie = 0;

		$produits = DB::table('produit')
        //->join('sous_categorie', 'produit.id_sous_categorie', '=', 'sous_categorie.id_sous_categorie')
        ->join('boutique', 'produit.id_boutique', '=', 'boutique.id_boutique')
        ->where('boutique.id_boutique', '=', $id)
        ->paginate(24);
		
		$produits_idem_ss_cats = DB::table('produit')
		->paginate(12);

        $boutique = Boutique::where(['id_boutique' =>$id])->first() ;

        $partenaires = DB::table('boutique')
        ->where('id_boutique', '!=', $id)
		->get();
		
		
		$sous_categories = SousCategorie::All(); 
		
		return view('pages_frontend/details-partenaires',compact('produits_idem_ss_cats', 'produits', 'id_categorie', 'sous_categories','boutique','partenaires'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $produit = Produit::where(['id_produit' =>$id])->first() ;
        $boutiques = Boutique::all() ;
        $sous_categories = SousCategorie::all() ;

        return view('pages_backend/produit/edit_produit',compact('produit','boutiques','sous_categories'));
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
        $produit = Produit::where(['id_produit' =>$id])->first() ;
         
        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            Image::make($image)->save('files_upload/produit/'.$id.'.jpg');

            $file_name ='files_upload/produit/'.$id.'.jpg';

          }else{

            $file_name =$produit->image_produit;
         }

        $produit->nom_produit= $request->nom_produit;
        $produit->description_produit= $request->description_produit;
        $produit->caracteristique_produit= $request->caracteristique_produit;
        $produit->quantite_produit= $request->quantite_produit;
        $produit->prix_ht_produit= $request->prix_produit;
        $produit->id_sous_categorie= $request->id_sous_categorie;
        $produit->id_boutique= $request->id_boutique;
        $produit->nouveau_produit= $request->nouveau_produit;
        if($request->tva_applicable=='oui'){
            $produit->tva_applicable= $request->tva_applicable;
            $produit->taux_tva= $request->taux_tva;

            $montant_tva=($request->prix_produit*$request->taux_tva)/100;
            $produit->montant_tva= $montant_tva;

            $prix_ttc= $request->prix_produit+$montant_tva ;
            $produit->prix_ttc= $prix_ttc;

        }else{
            $produit->tva_applicable= $request->tva_applicable;
            $produit->taux_tva= 'NULL';
            $produit->montant_tva= 'NULL';
            $produit->prix_ttc=  $request->prix_produit;
        }
        $produit->image_produit=$file_name;
        //$produit->etat_produit= 1 ;

        $produit->save();

        //return back()->with('success', 'Modification effectuee avec succè');
        return redirect()->to('/list/produit')->with('success', 'Modification effectuée avec succè');
    }


    public function ajouter_produit_image(Request $request)
    {
        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
         srand((double)microtime()*1000000);
         $i = 0 ;
         $code = '' ;
         while ($i <= 4) {
             $num = rand() % 33;
             $tmp = substr($chars, $num, 1);
             $code = $code . $tmp;
             $i++;
         }

        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            Image::make($image)->save('files_upload/produit_image/'.$code.'.jpg');

            $file_name ='files_upload/produit_image/'.$code.'.jpg';

          }else{

            $file_name = "";
         }

        $photo_produit= new PhotoProduit(); 

        $photo_produit->id_produit=$request->id_produit;
        $photo_produit->photo_produit=$file_name;
            
        $photo_produit->save();

        return redirect()->back()->with('success', 'Modification effectuée avec succè');
    }


    public function update_produit_image(Request $request, $id)
    {
        $photo_produit = PhotoProduit::where(['id_photo_produit' =>$id])->first() ;
         
        if ($request->HasFile('file')) {
            $cover = $request->file('file');
            $image = Image::make($cover)->encode('jpg');
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            Image::make($image)->save('files_upload/produit_image/'.$id.'.jpg');

            $file_name ='files_upload/produit_image/'.$id.'.jpg';

          }else{

            $file_name =$photo_produit->photo_produit;
         }
        $photo_produit->photo_produit=$file_name;
       
        $photo_produit->save();

        //return back()->with('success', 'Modification effectuee avec succè');
        return redirect()->back()->with('success', 'Modification effectuée avec succè');
    }


    public function updatePromotion(Request $request, $id)
    {
        $promotion = Promotion::where(['id_promotion' =>$id])->first() ;

        $promotion->pourcentage_promotion= $request->pourcentage_promotion;
        $promotion->date_debut_promotion= $request->date_debut_promotion;
        $promotion->date_fin_promotion= $request->date_fin_promotion;

        $promotion->save();

        return back()->with('success', 'Modification effectuée avec succè');
        // return redirect()->back();
    }

    public function updateStock(Request $request, $id)
    {
        $produit = Produit::where(['id_produit' =>$id])->first() ;

        $produit->quantite_produit=$produit->quantite_produit + $request->quantite;

        $produit->save();

        return back()->with('success', 'Modification effectuée avec succè');
        // return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produit = Produit::where(['id_produit' =>$id])->first() ;

        $produit->etat_produit = 0 ;

        $produit->save();

       return back()->with('error','Suppression effectuée avec succè');
       
    }

    public function destroyPromotion($id)
    {
        $promotion = Promotion::where(['id_promotion' =>$id])->first() ;

        $produit = Produit::where(['id_produit' =>$promotion->id_produit])->first() ;

        $produit->promotion= 0;
        
        $promotion->delete();
        $produit->save();

        return back()->with('error', 'Suppression effectuée avec succè');
       
    }

    public function delete_produit_image($id)
    {
        $photo_produit = PhotoProduit::where(['id_photo_produit' =>$id])->first() ;
        //dd($promotion);
        $photo_produit->delete();

        return back()->with('success', 'Suppression effectuée avec succè');
       
    }
}
