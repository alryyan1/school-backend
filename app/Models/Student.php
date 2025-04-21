<?php

// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $student_name
 * @property string $father_name
 * @property string $father_job
 * @property string $father_address
 * @property string $father_phone
 * @property string|null $father_whatsapp
 * @property string $mother_name
 * @property string $mother_job
 * @property string $mother_address
 * @property string $mother_phone
 * @property string|null $mother_whatsapp
 * @property string|null $email
 * @property string $date_of_birth
 * @property string $gender
 * @property string|null $referred_school
 * @property string|null $success_percentage
 * @property string|null $medical_condition
 * @property string|null $other_parent
 * @property string|null $relation_of_other_parent
 * @property string|null $relation_job
 * @property string|null $relation_phone
 * @property string|null $relation_whatsapp
 * @property string|null $image
 * @property int $approved
 * @property string|null $aproove_date
 * @property int|null $approved_by_user
 * @property int $message_sent
 * @property string|null $goverment_id
 * @property string $wished_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereApprovedByUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereAprooveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFatherAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFatherJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFatherPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFatherWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereGovermentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMedicalCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMessageSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMotherAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMotherJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMotherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMotherPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereMotherWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereOtherParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereReferredSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereRelationJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereRelationOfOtherParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereRelationPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereRelationWhatsapp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereStudentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereSuccessPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereWishedLevel($value)
 * @mixin \Eloquent
 */
class Student extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',

    ];
}
