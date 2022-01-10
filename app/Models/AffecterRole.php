<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AffecterRole
 * 
 * @property int $id_affecter_roles
 * @property int $id_role
 * @property int $id_user
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class AffecterRole extends Model
{
	protected $table = 'affecter_roles';
	protected $primaryKey = 'id_affecter_roles';

	protected $casts = [
		'id_role' => 'int',
		'id_user' => 'int'
	];

	protected $fillable = [
		'id_role',
		'id_user'
	];
}
