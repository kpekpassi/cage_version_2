<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Visiteur
 * 
 * @property int $id_visiteur
 * @property string $ip
 * @property Carbon|null $date_connecter
 * @property Carbon|null $date_update
 * @property Carbon|null $time
 * @property int $timestamp
 *
 * @package App\Models
 */
class Visiteur extends Model
{
	protected $table = 'visiteur';
	protected $primaryKey = 'id_visiteur';
	public $timestamps = false;

	protected $casts = [
		'timestamp' => 'int'
	];

	protected $dates = [
		'date_connecter',
		'date_update',
		'time'
	];

	protected $fillable = [
		'ip',
		'date_connecter',
		'date_update',
		'time',
		'timestamp'
	];
}
