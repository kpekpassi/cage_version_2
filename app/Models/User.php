<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id_user
 * @property string|null $nom_user
 * @property string|null $prenom_user
 * @property string|null $email_user
 * @property string|null $password_user
 * @property string|null $password_visible
 * @property string|null $sexe_user
 * @property int|null $telephone_user
 * @property int|null $id_role
 * @property int|null $ok_newsletter
 * @property int|null $type_user
 * @property string|null $id_ville
 * @property string|null $quartier_user
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class User extends Model
{
	protected $table = 'user';
	protected $primaryKey = 'id_user';

	protected $casts = [
		'telephone_user' => 'int',
		'id_role' => 'int',
		'ok_newsletter' => 'int',
		'type_user' => 'int'
	];

	protected $fillable = [
		'nom_user',
		'prenom_user',
		'email_user',
		'password_user',
		'password_visible',
		'sexe_user',
		'telephone_user',
		'id_role',
		'ok_newsletter',
		'type_user',
		'id_ville',
		'quartier_user'
	];
}
