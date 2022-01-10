<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Boutique
 * 
 * @property int $id_boutique
 * @property string|null $nom_boutique
 * @property string|null $description_boutique
 * @property string|null $photos_boutique
 * @property string|null $ville_boutique
 * @property string|null $pays_boutique
 * @property string|null $nif_boutique
 * @property int|null $contact_1_boutique
 * @property int|null $contact_2_boutique
 * @property string|null $email_boutique
 * @property string|null $slogan_boutique
 * @property int|null $id_role
 * @property string|null $password_boutique
 * @property int|null $etat_boutique
 * @property string|null $rue_boutique
 * @property string|null $quartier_boutique
 * @property string|null $batiment_boutique
 * @property string|null $nom_responsable
 * @property int|null $contact_responsable
 * 
 * @property Role $role
 *
 * @package App\Models
 */
class Boutique extends Model
{
	protected $table = 'boutique';
	protected $primaryKey = 'id_boutique';
	public $timestamps = false;

	protected $casts = [
		'contact_1_boutique' => 'int',
		'contact_2_boutique' => 'int',
		'id_role' => 'int',
		'etat_boutique' => 'int',
		'contact_responsable' => 'int'
	];

	protected $fillable = [
		'nom_boutique',
		'description_boutique',
		'photos_boutique',
		'ville_boutique',
		'pays_boutique',
		'nif_boutique',
		'contact_1_boutique',
		'contact_2_boutique',
		'email_boutique',
		'slogan_boutique',
		'id_role',
		'password_boutique',
		'etat_boutique',
		'rue_boutique',
		'quartier_boutique',
		'batiment_boutique',
		'nom_responsable',
		'contact_responsable'
	];

	public function role()
	{
		return $this->belongsTo(Role::class, 'id_role');
	}
}
