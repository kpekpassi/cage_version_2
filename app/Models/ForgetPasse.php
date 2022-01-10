<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ForgetPasse
 * 
 * 
 *
 * @package App\Models
 */
class ForgetPasse extends Model
{
	protected $table = 'forgetpasse';
	protected $primaryKey = 'id_forgetpasse';
	public $timestamps = false;

	protected $fillable = [
		'email_forget'
	];
}
