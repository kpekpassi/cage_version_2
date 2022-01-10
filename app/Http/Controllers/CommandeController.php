<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Boutique;
use App\Models\Adresse;
use App\Models\Remise;
use App\Models\Ville;
use App\Models\Paiement;
use App\Models\LigneCommande;
use ShoppingCart;
use Mail;
use App\Mail\EnvoiFacture;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use PDF;
use Alert;
use CinetPay\CinetPay;

class CommandeController extends Controller
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
         // Recupration de la date du jour
         $date_jour=date('Y-m-d');
         $id_user= Cookie::get('id_user');
         $total=ShoppingCart::total();
         $user = User::where(['id_user' =>$id_user])->first() ;
        $utilisateurs = User::where(['type_user' =>1])->get() ;
           foreach ($utilisateurs as $utilisateur) 
           { 
                $e_nom = "Commande de $user->nom_user  $user->prenom_user ," ;
                $email = $utilisateur->email_user; 
                $email1 = "fofanabilali2014@gmail.com"; 
                $email2 = "cagetogo@gmail.com"; 
                // titre du mail
                $titre ="Alert Commande"; 
				
                $description ="Une commande vient d'etre passée merci de prendre en compte."; 

                $contact = "Contact: +228 70 45 37 85 | 96 35 80 90 | 90 90 49 03 </br> Email: cagetogo@gmail.com  <br>  Site Web : www.cagebatiment.com" ;

                $contenu = $e_nom . '<br /><br />' . $description .'<br /><br /><br />'.$contact ;

                // envoi du mail HTML
                $from = "From: CAGE Bâtiment <cagetogo@gmail.com>\nMime-Version:";
                $from .= " 1.0\nContent-Type: text/html; charset=ISO-8859-1\n";
                // envoie du mail
               mail($email,$titre,$contenu,$from);
               mail($email1,$titre,$contenu,$from);
               mail($email2,$titre,$contenu,$from);
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

         $dernier_numero = DB::table('commande') ->latest('numero_facture') ->first();
          if($dernier_numero==null){
             $numero=1;
         }else{
            $numero=$dernier_numero->numero_facture+1;
         }
         
         if( $total > 100000 || $request->mode=="magasin"){
           $frais_livraison=0;
           $mode_paiement="Paiement au magasin";
         }else{
            $frais_livraison=1000; 
            $mode_paiement="Paiement à la livraison";
         }
        
         $commande = new Commande();
         
         $commande->reference_commande= $code;
         $commande->date_commande= $date_jour;
         $commande->etat_commande= 0;
         $commande->receptionner= 0;
         $commande->mode_paiement=$mode_paiement;
         $commande->id_adresse= $request->id_adresse;
         $commande->numero_facture=  $numero;
         $commande->frais_livraison= $frais_livraison;
         $commande->id_user= Cookie::get('id_user');
         $commande->valider_traitement= 0;
    
         $commande->save();

         $items = ShoppingCart::all();

        foreach($items as $item){
           
        $produit = Produit::where(['id_produit' =>$item->id])->first() ;

         $prix=$produit->prix_ht_produit*$item->qty;
         $montant_tva= ($prix*$produit->taux_tva)/100 ;

        $ligne_commande = new LigneCommande();
         
         $ligne_commande->reference_commande= $code;
         $ligne_commande->quantite_commande= $item->qty;
         $ligne_commande->prix_commande= $item->total;
         $ligne_commande->id_commande= $commande->id_commande;
         $ligne_commande->id_produit= $item->id;
         $ligne_commande->montant_tva= $montant_tva;
         $ligne_commande->prix_ht= $prix;
    
         $ligne_commande->save();

          }

        if($request->mode=="domicile" || $request->mode=="magasin")
         {
         ShoppingCart::destroy();
         return redirect()->to('/histotique-achats')->with('success', 'Conmande effectuée avec succè');
         }
         else{

            $verify_connection = @fsockopen("www.google.com", 80);
            if ($verify_connection) {
                $is_conn = true; //action when connected
                fclose($verify_connection);
            } else {

                die('<div style="font-size: 20px">Aucune connexion internet <a href="/">Retour à l\'accueil</a></div>');
            }

            echo '<div style="  width: 40px;  height: 40px;  margin: 100px auto;  background-color: #333;  border-radius: 100%;    -webkit-animation: sk-scaleout 1.0s infinite ease-in-out;  animation: sk-scaleout 1.0s infinite ease-in-out;"></div><div style="position: absolute;  left: 50%;  top: 25%;  transform: translate(-50%, -50%); font-size: 2rem;">En route vers la page du payement...</div> <style>@-webkit-keyframes sk-scaleout {  0% { -webkit-transform: scale(0) }  100% {    -webkit-transform: scale(1.0);    opacity: 0;  }}@keyframes sk-scaleout {  0% {     -webkit-transform: scale(0);    transform: scale(0);  } 100% {    -webkit-transform: scale(1.0);    transform: scale(1.0);    opacity: 0;  }}</style>';
            $apiKey = "34664266160461b5dae5879.62282259"; //Veuillez entrer votre apiKey
            $site_id = "770071"; //Veuillez entrer votre siteId
            $id_transaction = CinetPay::generateTransId(); // Identifiant du Paiement
            $description_du_paiement = sprintf('Achat des produits %s', $id_transaction); //Description du Payment
            $date_transaction = date("Y-m-d H:i:s"); // Date Paiement dans votre système
            $montant_a_payer = ShoppingCart::total(); // Montant à Payer : minimun est de 100 francs sur CinetPay
            $devise = 'XOF'; // Montant à Payer : minimun est de 100 francs sur CinetPay
            $identifiant_du_payeur = $code; // Mettez ici une information qui vous permettra d'identifier de façon unique le payeur
            $formName = "goCinetPay"; // nom du formulaire CinetPay
            $notify_url = ''; // Lien de notification CallBack CinetPay (IPN Link)
            $return_url = ''; // Lien de retour CallBack CinetPay
            $cancel_url = ''; // Lien d'annulation CinetPay
            // Configuration du bouton
            $btnType = 2;//1-5xwxxw
            $btnSize = 'large'; // 'small' pour reduire la taille du bouton, 'large' pour une taille moyenne ou 'larger' pour  une taille plus grande
            
            // Paramétrage du panier CinetPay et affichage du formulaire
            $cp = new CinetPay($site_id, $apiKey);
            try {
                $cp->setTransId($id_transaction)
                    ->setDesignation($description_du_paiement)
                    ->setTransDate($date_transaction)
                    ->setAmount($montant_a_payer)
                    ->setCurrency($devise)
                    ->setDebug(false)// Valorisé à true, si vous voulez activer le mode debug sur cinetpay afin d'afficher toutes les variables envoyées chez CinetPay
                    ->setCustom($identifiant_du_payeur)// optional
                    ->setNotifyUrl($notify_url)// optional
                    ->setReturnUrl($return_url)// optional
                    ->setCancelUrl($cancel_url)// optional
                    ->displayPayButton($formName, $btnType, $btnSize);
            } catch (Exception $e) {
                print $e->getMessage();
            }

            ShoppingCart::destroy();
         }

    }

    public function ipnCinetPay()
    {
        $id_transaction = $_POST['cpm_trans_id'];
        if (!empty($id_transaction)) {
            try {
                $apiKey = "34664266160461b5dae5879.62282259"; //Veuillez entrer votre apiKey
                $site_id = "770071"; //Veuillez entrer votre siteId
        
                $cp = new CinetPay($site_id, $apiKey);
        
                // Reprise exacte des bonnes données chez CinetPay
                $cp->setTransId($id_transaction)->getPayStatus();

                $commande = Commande::where(['reference_commande' => $cp->_cpm_custom])->first();
               
                // On verifie que le paiement est valide
                if ($cp->isValidPayment()) {
                    //echo "Succès";
                    $status = 1;
                  } else {
                    //echo "Erreur";
                    $status = 0;
                  }
                  $paiement = new Paiement();

                  $paiement->id_transaction= $id_transaction ;
                  $paiement->id_commande= $commande->id_commande;
                  $paiement->phone_number= $cp->_cpm_phone_prefixe . " " . $cp->_cel_phone_num;
                  $paiement->reference_commande= $cp->_signature . " " . $cp->_payment_method;
                  $paiement->montant_payer= $cp->_cpm_amount;
                  $paiement->mode_paiement= "Flooz";
                  $paiement->etat_paiement= $status;

                  $paiement->save();

                echo $status;

                $paymentData = [
                    "cpm_site_id" => $cp->_cpm_site_id,
                    "signature" => $cp->_signature,
                    "cpm_amount" => $cp->_cpm_amount,
                    "cpm_trans_id" => $cp->_cpm_trans_id,
                    "cpm_custom" => $cp->_cpm_custom,
                    "cpm_currency" => $cp->_cpm_currency,
                    "cpm_payid" => $cp->_cpm_payid,
                    "cpm_payment_date" => $cp->_cpm_payment_date,
                    "cpm_payment_time" => $cp->_cpm_payment_time,
                    "cpm_error_message" => $cp->_cpm_error_message,
                    "payment_method" => $cp->_payment_method,
                    "cpm_phone_prefixe" => $cp->_cpm_phone_prefixe,
                    "cel_phone_num" => $cp->_cel_phone_num,
                    "cpm_ipn_ack" => $cp->_cpm_ipn_ack,
                    "created_at" => $cp->_created_at,
                    "updated_at" => $cp->_updated_at,
                    "cpm_result" => $cp->_cpm_result,
                    "cpm_trans_status" => $cp->_cpm_trans_status,
                    "cpm_designation" => $cp->_cpm_designation,
                    "buyer_name" => $cp->_buyer_name,
                ];
               
                return  $paymentData;

            } catch (Exception $e) {
                // Une erreur s'est produite
                echo "Erreur :" . $e->getMessage();
            }
        } else {
            // redirection vers la page d'accueil
            die();
        }
    }


    
    public function return()
    {
        if (isset($_POST['cpm_trans_id'])) {
            // SDK PHP de CinetPay 
            //require_once __DIR__ . '/../src/cinetpay.php';

            try {
                // Initialisation de CinetPay et Identification du paiement
                $id_transaction = $_POST['cpm_trans_id'];
                //Veuillez entrer votre apiKey et site ID
                $apiKey = "34664266160461b5dae5879.62282259"; //Veuillez entrer votre apiKey
                $site_id = "770071"; //Veuillez entrer votre siteId
                $plateform = "PROD";
                $version = "V1";
                $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
                $CinetPay->setTransId($id_transaction)->getPayStatus();
                $cpm_site_id = $CinetPay->_cpm_site_id;
                $signature = $CinetPay->_signature;
                $cpm_amount = $CinetPay->_cpm_amount;
                $cpm_trans_id = $CinetPay->_cpm_trans_id;
                $cpm_custom = $CinetPay->_cpm_custom;
                $cpm_currency = $CinetPay->_cpm_currency;
                $cpm_payid = $CinetPay->_cpm_payid;
                $cpm_payment_date = $CinetPay->_cpm_payment_date;
                $cpm_payment_time = $CinetPay->_cpm_payment_time;
                $cpm_error_message = $CinetPay->_cpm_error_message;
                $payment_method = $CinetPay->_payment_method;
                $cpm_phone_prefixe = $CinetPay->_cpm_phone_prefixe;
                $cel_phone_num = $CinetPay->_cel_phone_num;
                $cpm_ipn_ack = $CinetPay->_cpm_ipn_ack;
                $created_at = $CinetPay->_created_at;
                $updated_at = $CinetPay->_updated_at;
                $cpm_result = $CinetPay->_cpm_result;
                $cpm_trans_status = $CinetPay->_cpm_trans_status;
                $cpm_designation = $CinetPay->_cpm_designation;
                $buyer_name = $CinetPay->_buyer_name;
                // Aucun enregistrement dans la base de donnée ici
                if ($cpm_result == '00') {
                    // une page HTML de paiement bon
                    echo 'Felicitation, votre paiement a été effectué avec succès';
                    die();
                } else {
                    // une page HTML de paiement echoué
                    echo 'Echec, votre paiement a échoué';
                    die();
                }
            } catch (Exception $e) {
                echo "Erreur :" . $e->getMessage();
                // Une erreur s'est produite
            }
        } else {
            // redirection vers la page d'accueil
            return redirect()->to('/');
            die();
        }
    }

    public function cancel()
    {
        // une page HTML d'Annulation ou une redirection vers la page d'accueil
       // echo 'Vous avez annulé votre paiement';
        return redirect()->to('/');
    }

    public function getAllCommandeUser()
    {
          $commandes = DB::table('commande')
          ->where('commande.etat_commande', '=', 0)
          ->where('commande.receptionner', '=', 0)
          ->get();

        return view('pages_backend/commande/list_commande_attente',compact('commandes'));
    }


    public function getAllCommandeTraitement()
    {
          //$id_utilisateur= Cookie::get('id_user');

          $commandes = DB::table('commande')
          ->where('commande.etat_commande', '=', 0)
          ->where('commande.receptionner', '=', 1)
          //->where('commande.id_utilisateur', '=', $id_utilisateur)
          ->get();

        return view('pages_backend/commande/list_commande_traitement',compact('commandes'));
    }

//liste commande valider
    public function getAllCommandeValider()
    {
          $commandes = DB::table('commande')
          ->where('commande.etat_commande', '=', 1)
          ->get();

        return view('pages_backend/commande/list_commande_valider',compact('commandes'));
    }
	
	//liste des commandes rejeter
	public function getAllCommandeRejeter()
    {
          $commandes = DB::table('ligne_commande')
		  ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
          ->where('ligne_commande.etat_produit', '=', 2)
          ->get();

		  $produits = Produit::All();

        return view('pages_backend/commande/list_commande_rejeter',compact('produits', 'commandes'));
    }
	
	//historique commande client
	public function historique_achat()
    {
		$id_user= Cookie::get('id_user');
		
          $commandes = DB::table('commande')
          ->where('commande.id_user', '=', $id_user)
          ->orderBy('commande.id_commande','desc')
          ->paginate(8) ;
		  
        return view('pages_frontend/mes_achats',compact('commandes'));
    }
	
	//les meileurs ventes
	public function meileurs_ventes_clients(){

		$id_categorie=0 ;
		
		$meilleurs_ventes = DB::table('ligne_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
		//->orderBy('ligne_commande.id_produit', 'desc')
		->groupBy('ligne_commande.id_produit')
		->take(6)
        ->get();
		
		return view('pages_frontend/plus_vendu',compact('meilleurs_ventes','id_categorie'));

	}
	
	
	public function detail_historique($id,$reference_commande)
    {
        $user = User::where(['id_user' =>$id])->first() ;
		
		$villes = Ville::all() ;

        $commande = Commande::where(['reference_commande' =>$reference_commande])->first() ;

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;
         
        $adresse = Adresse::where(['id_adresse' =>$commande->id_adresse])->first() ;

        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
		->where('ligne_commande.etat_produit', '=', 1)
        ->get();

        $prix_total = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
		->where('ligne_commande.etat_produit', '=', 1)
        ->sum('ligne_commande.prix_commande');

        $prix_total_ht = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
		->where('ligne_commande.etat_produit', '=', 1)
        ->sum('ligne_commande.prix_ht');

         $prix_total_tva = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
		->where('ligne_commande.etat_produit', '=', 1)
        ->sum('ligne_commande.montant_tva');
		
		//vérification s'il existe des produits rejetés
		$nbre_produit_rej = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 2)
        ->count();
		
		//si oui, recupération et affichage de la liste
		$liste_produit_rejs = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 2)
        ->get();

        return view('pages_frontend/details-historique-achats',compact('liste_produit_rejs', 'nbre_produit_rej', 'villes','prix_total_tva','prix_total_ht','commandes','user','prix_total','adresse','commande','remise'));

    }
 

    public function getAllUser()
    {
        $users = DB::table('user')
        ->where('user.type_user', '=', 2)
        ->get();

          $villes = Ville::where(['etat_ville' =>1])->get() ;

        return view('pages_backend/commande/list_client',compact('users','villes'));
    }

    public function checkout(){
        
        $id_user = $id_user= Cookie::get('id_user');
        $adresses = Adresse::where(['id_user' =>$id_user,'etat_adresse' =>1])->get() ;
		$id_categorie=0 ;
		
		return view('pages_frontend/checkout',compact('id_categorie','adresses'));
	}
	
	//consulter les produits de la commande
	public function consulterFacture($id,$reference_commande)
    {
        $user = User::where(['id_user' =>$id])->first() ;

        $fournisseurs = Boutique::all() ;

        $villes = Ville::all() ;

        $commande = Commande::where(['reference_commande' =>$reference_commande])->first() ;

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;

        $adresse = Adresse::where(['id_adresse' =>$commande->id_adresse])->first() ;

        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 0)
        ->get();
		
        return view('pages_backend/commande/consulter_facture',compact('commandes','user','adresse','commande','remise','villes','fournisseurs'));

    }
	
	//consulter produit valider
	public function consulterProduitValider($id,$reference_commande)
    {
        $user = User::where(['id_user' =>$id])->first() ;
        $fournisseurs = Boutique::all() ;
        $villes = Ville::all() ;

        $commande = Commande::where(['reference_commande' =>$reference_commande])->first() ;

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;

        $adresse = Adresse::where(['id_adresse' =>$commande->id_adresse])->first() ;

        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 1)
        ->get();


        return view('pages_backend/commande/liste_produit_valider',compact('commandes','user','adresse','commande','remise','villes','fournisseurs'));

    }


	//consulter produit rejetés
	public function consulterProduitRejeter($id,$reference_commande)
    {
        $user = User::where(['id_user' =>$id])->first() ;
        $fournisseurs = Boutique::all() ;
        $villes = Ville::all() ;

        $commande = Commande::where(['reference_commande' =>$reference_commande])->first() ;

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;

        $adresse = Adresse::where(['id_adresse' =>$commande->id_adresse])->first() ;

        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 2)
        ->get();


        return view('pages_backend/commande/liste_produit_rejeter',compact('commandes','user','adresse','commande','remise','villes','fournisseurs'));

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }


    public function voirFacture($id,$reference_commande)
    {
        $user = User::where(['id_user' =>$id])->first() ;

        $utilisateurs = DB::table('user')
        ->where('user.type_user', '=', 1)
        ->get();

        $villes = Ville::all() ;

        $commande = Commande::where(['reference_commande' =>$reference_commande])->first() ;

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;

        $adresse = Adresse::where(['id_adresse' =>$commande->id_adresse])->first() ;
        // si la commande n'est pas encore valider
        /*if($commande->etat_commande == 0)
        {
        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->get();

         $prix_total = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->sum('ligne_commande.prix_commande');

        $prix_total_ht = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->sum('ligne_commande.prix_ht');

         $prix_total_tva = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->sum('ligne_commande.montant_tva');

        return view('pages_backend/commande/facturation',compact('commandes','user','prix_total','adresse','commande','remise','villes','prix_total_tva','prix_total_ht'));

        // si la commande est deja valider

        }else{
*/
        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 1)
        ->get();

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;

        $prix_total = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 1)
        ->sum('ligne_commande.prix_commande');

        $prix_total_ht = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 1)
        ->sum('ligne_commande.prix_ht');

        $prix_total_tva = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 1)
        ->sum('ligne_commande.montant_tva');
		
		//vérification s'il existe des produits rejetés
		$nbre_produit_rej = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 2)
        ->count();
		
		//si oui, recupération et affichage de la liste
		$liste_produit_rejs = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('ligne_commande.etat_produit', '=', 2)
        ->get();

        return view('pages_backend/commande/facturation',compact('liste_produit_rejs', 'nbre_produit_rej', 'commandes','user','prix_total','adresse','commande','remise','villes','prix_total_tva','prix_total_ht','utilisateurs'));

        //}
    }


    public function download_facture($id,$reference_commande)
    {    
        $user = User::where(['id_user' =>$id])->first() ;

        $villes = Ville::all() ;

        $commande = Commande::where(['reference_commande' =>$reference_commande])->first() ;

        $remise = Remise::where(['reference_commande' =>$reference_commande])->first() ;

        $adresse = Adresse::where(['id_adresse' =>$commande->id_adresse])->first() ;
        // si la commande n'est pas encore valider
        $commandes = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->get();

         $prix_total = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->sum('ligne_commande.prix_commande');

        $prix_total_ht = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->sum('ligne_commande.prix_ht');

         $prix_total_tva = DB::table('ligne_commande')
        ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
        //->join('user', 'user.id_user', '=', 'commande.id_user')
        ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
        ->where('commande.id_user', '=', $id)
        ->where('commande.reference_commande', '=', $reference_commande)
        ->where('commande.etat_commande', '=', 0)
        ->sum('ligne_commande.montant_tva');


         $pdf = PDF::loadView('pages_backend/commande/facture_pdf',['user'=>$user,'prix_total'=>$prix_total,'commandes'=>$commandes,'commande'=>$commande,'adresse'=>$adresse,'villes'=>$villes,'prix_total_tva'=>$prix_total_tva,'prix_total_ht'=>$prix_total_ht,'remise'=>$remise])->setPaper('a4', 'landscape');

        return $pdf->stream('facture.pdf'); 
        
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
	
    public function receptionnerCommande(Request $request,$id)
    {
        $id_utilisateur= Cookie::get('id_user');

        $commandee = Commande::where(['id_commande' =>$id])->first() ;

        $commandee->id_utilisateur=$id_utilisateur;
        $commandee->receptionner=1;
        $commandee->save();

       return redirect()->back()->with('success', 'Conmande receptionnee avec succè');

    }
	
	public function cloturerCommande(Request $request,$id)
    {
        $id_utilisateur= Cookie::get('id_user');

        $commandee = Commande::where(['id_commande' =>$id])->first() ;

        $date_jour=date('Y-m-d');
        //$commandees = LigneCommande::where(['id_produit' =>$id])->first() ;

         
       $commandes = DB::table('ligne_commande')
       ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
       ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
       ->where('commande.id_user', '=', $commandee->id_user)
       ->where('commande.reference_commande', '=', $commandee->reference_commande)
       ->where('ligne_commande.etat_commande', '=', 0)
       ->get();


        foreach($commandes as $commande){

       $produit = Produit::where(['id_produit' =>$commande->id_produit])->first() ;
       $produit->quantite_produit=$produit->quantite_produit-$commande->quantite_commande;
       $produit->save();

        }

        $commandee->id_utilisateur=$id_utilisateur;
        $commandee->date_livraison=$date_jour;
        $commandee->etat_commande=1;
        $commandee->save();
		
		Session()->flash('success', 'Commande clôturée avec succès');	
		return $this->getAllCommandeValider();
    }

    public function update_frais_livraison(Request $request,$id)
    {
         $commandee = Commande::where(['id_commande' =>$id])->first() ;
         $commandee->frais_livraison=$request->frais;
         $commandee->save();

        return redirect()->back()->with('success', 'Frais de livraison enregistrer avec succè');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     // Validation de la commande
    // public function destroy($id)
    // {
    //      $date_jour=date('Y-m-d');
    //      $commandee = Commande::where(['id_commande' =>$id])->first() ;

    //      $commandes = DB::table('ligne_commande')
    //     ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
    //     ->join('produit', 'produit.id_produit', '=', 'ligne_commande.id_produit')
    //     ->where('commande.id_user', '=', $commandee->id_user)
    //     ->where('commande.reference_commande', '=', $commandee->reference_commande)
    //     ->where('commande.etat_commande', '=', 0)
    //     ->get();

    //      foreach($commandes as $commande){

    //     $produit = Produit::where(['id_produit' =>$commande->id_produit])->first() ;
    //     $produit->quantite_produit=$produit->quantite_produit-$commande->quantite_commande;
    //     $produit->save();

    //      }
        
    //      $commandee->etat_commande=1;
    //      $commandee->date_livraison=$date_jour;
    //      $commandee->save();

    //     return redirect()->back()->with('success', 'Conmande validée avec succè');
    // }

    // public function validerTraitement(Request $request,$id)
    // {
    //      $commandee = Commande::where(['id_commande' =>$id])->first() ;
    //      if($commandee->valider_traitement==0){
    //         $commandee->valider_traitement=1;
    //      }else{
    //         $commandee->valider_traitement=0;
          
    //      }
    //      $commandee->save();
    //     return redirect()->back()->with('success', 'Operation effectuée avec succès');
    // }
	
	//valider une commande
	public function valider_commande($id, $reference, Request $request){
		
		$commandee = LigneCommande::where(['id_produit' =>$id])->where(['reference_commande' =>$reference])->first() ;

        $commandee->etat_produit=1;
        $commandee->save();

        return redirect()->back()->with('success', 'Produit validé avec succès');
	}

	//rejeter une commande
	public function rejeter_commande($id, $reference, Request $request){
		$date_jour=date('Y-m-d');
        $commandee = LigneCommande::where(['id_produit' =>$id])->where(['reference_commande' =>$reference])->first() ;

        $commandee->etat_produit=2;
        $commandee->motif_rejet=$request->motif;
		$commandee->date_rejeter=$date_jour;
        $commandee->save();

        return redirect()->back()->with('success', 'Produit rejeté avec succès');
	}
}
