<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $id_role
 * @property string $libelle_role
 * @property string $code_role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Boutique[] $boutiques
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'role';
	protected $primaryKey = 'id_role';

	protected $fillable = [
		'libelle_role',
		'code_role'
	];

	public function boutiques()
	{
		return $this->hasMany(Boutique::class, 'id_role');
	}
}
