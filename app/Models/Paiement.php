<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Paiement
 * 
 * @property int $id_paiement
 * @property string|null $id_transaction
 * @property string|null $reference_commande
 * @property string|null $mode_payement
 * @property float|null $montant_payer
 * @property int|null $etat_paiement
 * @property int|null $id_commande
 * @property string|null $phone_number
 *
 * @package App\Models
 */
class Paiement extends Model
{
	protected $table = 'paiement';
	protected $primaryKey = 'id_paiement';
	public $timestamps = false;

	protected $casts = [
		'montant_payer' => 'float',
		'etat_paiement' => 'int',
		'id_commande' => 'int'
	];

	protected $fillable = [
		'id_transaction',
		'reference_commande',
		'mode_payement',
		'montant_payer',
		'etat_paiement',
		'id_commande',
		'phone_number'
	];
}
