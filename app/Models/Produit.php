<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Produit
 * 
 * @property int $id_produit
 * @property string|null $nom_produit
 * @property string|null $description_produit
 * @property int|null $prix_ht_produit
 * @property string|null $tva_applicable
 * @property int|null $taux_tva
 * @property int|null $prix_ttc
 * @property int|null $quantite_produit
 * @property string|null $stock_produit
 * @property string|null $nouveau_produit
 * @property int|null $etat_produit
 * @property int|null $id_categorie
 * @property int|null $id_sous_categorie
 * @property int|null $id_boutique
 * @property string|null $caracteristique_produit
 * @property string|null $image_produit
 * @property int|null $montant_tva
 * @property int $promotion
 *
 * @package App\Models
 */
class Produit extends Model
{
	protected $table = 'produit';
	protected $primaryKey = 'id_produit';
	public $timestamps = false;

	protected $casts = [
		'prix_ht_produit' => 'int',
		'taux_tva' => 'int',
		'prix_ttc' => 'int',
		'quantite_produit' => 'int',
		'etat_produit' => 'int',
		'id_categorie' => 'int',
		'id_sous_categorie' => 'int',
		'id_boutique' => 'int',
		'montant_tva' => 'int',
		'promotion' => 'int'
	];

	protected $fillable = [
		'nom_produit',
		'description_produit',
		'prix_ht_produit',
		'tva_applicable',
		'taux_tva',
		'prix_ttc',
		'quantite_produit',
		'stock_produit',
		'nouveau_produit',
		'etat_produit',
		'id_categorie',
		'id_sous_categorie',
		'id_boutique',
		'caracteristique_produit',
		'image_produit',
		'montant_tva',
		'promotion'
	];
}
