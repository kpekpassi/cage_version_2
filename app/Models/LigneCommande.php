<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LigneCommande
 * 
 * @property int $id_ligne_commande
 * @property int|null $quantite_commande
 * @property int|null $prix_commande
 * @property int|null $id_commande
 * @property string|null $reference_commande
 * @property int|null $id_produit
 * @property int|null $montant_tva
 * @property int|null $prix_ht
 * @property int $etat_produit
 * @property string $motif_rejet
 * @property int $etat_commande
 * @property Carbon $date_rejeter
 *
 * @package App\Models
 */
class LigneCommande extends Model
{
	protected $table = 'ligne_commande';
	protected $primaryKey = 'id_ligne_commande';
	public $timestamps = false;

	protected $casts = [
		'quantite_commande' => 'int',
		'prix_commande' => 'int',
		'id_commande' => 'int',
		'id_produit' => 'int',
		'montant_tva' => 'int',
		'prix_ht' => 'int',
		'etat_produit' => 'int',
		'etat_commande' => 'int'
	];

	protected $dates = [
		'date_rejeter'
	];

	protected $fillable = [
		'quantite_commande',
		'prix_commande',
		'id_commande',
		'reference_commande',
		'id_produit',
		'montant_tva',
		'prix_ht',
		'etat_produit',
		'motif_rejet',
		'etat_commande',
		'date_rejeter'
	];
}
