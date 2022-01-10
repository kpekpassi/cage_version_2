<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Partenaire
 * 
 * @property int $id_partenaire
 * @property string $sigle_partenaire
 * @property string $logo_partenaire
 *
 * @package App\Models
 */
class Partenaire extends Model
{
	protected $table = 'partenaire';
	protected $primaryKey = 'id_partenaire';
	public $timestamps = false;

	protected $fillable = [
		'sigle_partenaire',
		'logo_partenaire'
	];
}
