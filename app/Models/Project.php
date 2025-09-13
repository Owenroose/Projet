<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string|null $challenge
 * @property string|null $solution
 * @property string|null $technologies
 * @property string|null $client
 * @property \Illuminate\Support\Carbon|null $project_date
 * @property string|null $project_url
 * @property string|null $image
 * @property int $order
 * @property bool $featured
 * @property bool $published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $technologies_array
 * @property-read \App\Models\Testimonial|null $testimonial
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereChallenge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereProjectDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereProjectUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereSolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTechnologies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'challenge',
        'solution',
        'technologies',
        'client',
        'project_date',
        'project_url',
        'image',
        'order',
        'featured',
        'published'
    ];

    protected $casts = [
        'project_date' => 'date',
        'featured' => 'boolean',
        'published' => 'boolean'
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope a query to only include published projects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    /**
     * Scope a query to only include featured projects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Get the technologies as an array.
     *
     * @return array
     */
    public function getTechnologiesArrayAttribute()
    {
        return explode(',', $this->technologies);
    }

    // Relation avec les témoignages
public function testimonial()
{
    return $this->hasOne(Testimonial::class, 'project_name', 'title');
}
}
