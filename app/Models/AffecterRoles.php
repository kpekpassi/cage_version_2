<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $id_role
 * @property string|null $libelle_role
 * 
 * @property Collection|Boutique[] $boutiques
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class AffecterRoles extends Model
{
	protected $table = 'affecter_roles';
	protected $primaryKey = 'id_affecter_roles';
	public $timestamps = false;

	protected $fillable = [
		'id_role',
		'id_user'
	];

}
