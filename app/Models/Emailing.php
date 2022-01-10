<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Emailing
 * 
 * @property int $id_email
 * @property string|null $titre_email
 * @property string|null $description_email
 * @property int|null $id_ville
 *
 * @package App\Models
 */
class Emailing extends Model
{
	protected $table = 'emailing';
	protected $primaryKey = 'id_email';
	public $timestamps = false;

	protected $casts = [
		'id_ville' => 'int'
	];

	protected $fillable = [
		'titre_email',
		'description_email',
		'id_ville'
	];
}
