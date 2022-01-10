<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\AffecterRoles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Alert;
use DateTime;

class StatistiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages_backend/statistique/index');
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
		
    }


    public function statistique(Request $request)
    {
        $aujourdhui=date('Y-m-d');
        $hier = (new DateTime($aujourdhui))->modify('-1 day')->format('Y-m-d');
        $avant_hier=(new DateTime($aujourdhui))->modify('-2 day')->format('Y-m-d');
        $semaine_passe=(new DateTime($aujourdhui))->modify('-2 day')->format('Y-m-d');
        $filtre=$request->filtre;

            $nombre_total_produit_vedu = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->where('ligne_commande.etat_produit', '=', 1)
            ->sum('ligne_commande.quantite_commande');

            $montant_total_vendu = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->where('ligne_commande.etat_produit', '=', 1)
            ->sum('prix_commande');

            $nombre_commande = DB::table('commande')
            ->where('commande.etat_commande', '=', 1)
            ->count();
    
    //ON VERIFIE SI LE TYPE EST 1

       if($request->type==1)
      {
        if($filtre=='aujourdhui'){
            $option="Aujourdhui";
            $date=$aujourdhui;
        }
        elseif($filtre=='hier')
        {
            $option="Hier";
            $date=$hier;
        }
        elseif($filtre=='avant_hier')
        {
            $option="Avant-hier";
            $date=$avant_hier;
         }
         elseif($filtre=='mois_en_cours')
        {
            $option="Mois en cours";
            $date=date('m');

            $produits = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereMonth('commande.date_livraison', '=',$date)
            ->get();
           
            $nombre = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereMonth('commande.date_livraison', '=',$date)
            ->sum('ligne_commande.quantite_commande');

            $montant_total = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereMonth('commande.date_livraison', '=',$date)
            ->sum('prix_commande');

        return view('pages_backend/statistique/index',compact('produits','option','nombre','montant_total','nombre_total_produit_vedu','montant_total_vendu','nombre_commande'));

         }

         elseif($filtre=='mois_passe')
        {
            $option="Mois passé";
            $date=date('m')-1;

            $produits = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereMonth('commande.date_livraison', '=',$date)
            ->get();
           

            $nombre = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereMonth('commande.date_livraison', '=',$date)
            ->sum('ligne_commande.quantite_commande');

            $montant_total = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereMonth('commande.date_livraison', '=',$date)
            ->sum('prix_commande');

        return view('pages_backend/statistique/index',compact('produits','option','nombre','montant_total','nombre_total_produit_vedu','montant_total_vendu','nombre_commande'));

         }

       // RECUPERATION DES DONNEES SELON L'OPTION CHOISI

            $produits = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '=',$date)
            ->get();

            $nombre = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '=',$date)
            ->sum('ligne_commande.quantite_commande');

            $montant_total = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '=',$date)
            ->sum('prix_commande');

         return view('pages_backend/statistique/index',compact('produits','option','nombre','montant_total','nombre_total_produit_vedu','montant_total_vendu','nombre_commande'));

         //ON VERIFIE SI LE TYPE EST 2

         }elseif($request->type==2){
            
            $date1=$request->date1;
            $date2=$request->date2;
            $option=$date1." au ".$date2;

            $produits = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '>=',$date1)
            ->whereDate('commande.date_livraison', '<=',$date2)
            ->get();

            $nombre = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '>=',$date1)
            ->whereDate('commande.date_livraison', '<=',$date2)
            ->sum('ligne_commande.quantite_commande');

            $montant_total = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '>=',$date1)
            ->whereDate('commande.date_livraison', '<=',$date2)
            ->sum('prix_commande');

        return view('pages_backend/statistique/index',compact('produits','option','nombre','montant_total','nombre_total_produit_vedu','montant_total_vendu','nombre_commande'));

    //SI RIEN NA ETE CHOISIE
    }else{

            $option="Aujourdhui";
            $date=$aujourdhui;

            $produits = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '=',$date)
            ->get();

            $nombre = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '=',$date)
            ->sum('ligne_commande.quantite_commande');

            $montant_total = DB::table('commande')
            ->join('ligne_commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
            ->where('commande.etat_commande', '=', 1)
            ->whereDate('commande.date_livraison', '=',$date)
            ->sum('prix_commande');

        return view('pages_backend/statistique/index',compact('produits','option','nombre','montant_total','nombre_total_produit_vedu','montant_total_vendu','nombre_commande'));
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
        //
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
