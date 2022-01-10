<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Newsletter
 * 
 * @property int $id_newsletter
 * @property string|null $email_newsletter
 *
 * @package App\Models
 */
class Newsletter extends Model
{
	protected $table = 'newsletter';
	protected $primaryKey = 'id_newsletter';
	public $timestamps = false;

	protected $fillable = [
		'email_newsletter'
	];
}
