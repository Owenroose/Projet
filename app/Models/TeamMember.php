<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $position
 * @property string|null $bio
 * @property string|null $skills
 * @property int|null $experience
 * @property string|null $photo
 * @property string|null $linkedin
 * @property string|null $twitter
 * @property string|null $facebook
 * @property string|null $instagram
 * @property int $order
 * @property bool $published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $experience_text
 * @property-read mixed $skills_array
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereSkills($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'skills',
        'experience',
        'photo',
        'linkedin',
        'twitter',
        'facebook',
        'instagram',
        'order',
        'published'
    ];

    protected $casts = [
        'published' => 'boolean'
    ];

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function getSkillsArrayAttribute()
    {
        return $this->skills ? explode(',', $this->skills) : [];
    }

    public function getExperienceTextAttribute()
    {
        if ($this->experience === 1) {
            return '1 an d\'expérience';
        } elseif ($this->experience > 1) {
            return $this->experience . ' ans d\'expérience';
        } else {
            return 'Débutant';
        }
    }
}
