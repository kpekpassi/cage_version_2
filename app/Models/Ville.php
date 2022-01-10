<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ville
 * 
 * @property int $id_ville
 * @property string|null $libelle_ville
 * @property int|null $etat_ville
 *
 * @package App\Models
 */
class Ville extends Model
{
	protected $table = 'ville';
	protected $primaryKey = 'id_ville';
	public $timestamps = false;

	protected $casts = [
		'etat_ville' => 'int'
	];

	protected $fillable = [
		'libelle_ville',
		'etat_ville'
	];
}
