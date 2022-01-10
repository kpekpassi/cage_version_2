<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InfoContact
 * 
 * @property int $id_contact
 * @property string|null $nom_contact
 * @property string|null $email_contact
 * @property string|null $objet_contact
 * @property string|null $message_contact
 * @property Carbon $date_contact
 *
 * @package App\Models
 */
class InfoContact extends Model
{
	protected $table = 'info_contact';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_contact' => 'int'
	];

	protected $dates = [
		'date_contact'
	];

	protected $fillable = [
		'id_contact',
		'nom_contact',
		'email_contact',
		'objet_contact',
		'message_contact',
		'date_contact'
	];
}
