<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Adresse
 * 
 * @property int $id_adresse
 * @property string|null $ville_adresse
 * @property string|null $pays_adresse
 * @property string|null $telephone
 * @property string|null $description_adresse
 * @property int|null $id_user
 * @property int|null $etat_adresse
 *
 * @package App\Models
 */
class Adresse extends Model
{
	protected $table = 'adresse';
	protected $primaryKey = 'id_adresse';
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'etat_adresse' => 'int'
	];

	protected $fillable = [
		'ville_adresse',
		'pays_adresse',
		'telephone',
		'description_adresse',
		'id_user',
		'etat_adresse'
	];
}
