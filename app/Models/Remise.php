<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Remise
 * 
 * @property int $id_remise
 * @property string|null $reference_commande
 * @property int|null $id_commande
 * @property int|null $etat_remise
 * @property int|null $pourcentage_remise
 *
 * @package App\Models
 */
class Remise extends Model
{
	protected $table = 'remise';
	protected $primaryKey = 'id_remise';
	public $timestamps = false;

	protected $casts = [
		'id_commande' => 'int',
		'etat_remise' => 'int',
		'pourcentage_remise' => 'int'
	];

	protected $fillable = [
		'reference_commande',
		'id_commande',
		'etat_remise',
		'pourcentage_remise'
	];
}
