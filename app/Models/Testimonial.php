<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $client_name
 * @property string|null $client_position
 * @property string|null $client_company
 * @property string $content
 * @property int $rating
 * @property string|null $project_name
 * @property \Illuminate\Support\Carbon|null $date
 * @property bool $featured
 * @property bool $published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $star_rating
 * @property-read \App\Models\Project|null $project
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereClientCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereClientPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereProjectName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Testimonial whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'client_position',
        'client_company',
        'content',
        'rating',
        'project_name',
        'date',
        'featured',
        'published'
    ];

    protected $casts = [
        'date' => 'date',
        'featured' => 'boolean',
        'published' => 'boolean'
    ];

    /**
     * Scope a query to only include published testimonials.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    /**
     * Scope a query to only include featured testimonials.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Get the star rating as HTML.
     *
     * @return string
     */
    public function getStarRatingAttribute()
    {
        $stars = '';
        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5;

        for ($i = 0; $i < $fullStars; $i++) {
            $stars .= '<i class="fas fa-star"></i>';
        }

        if ($halfStar) {
            $stars .= '<i class="fas fa-star-half-alt"></i>';
            $fullStars++; // Pour compter la demi-étoile
        }

        // Ajouter des étoiles vides si nécessaire
        for ($i = $fullStars; $i < 5; $i++) {
            $stars .= '<i class="far fa-star"></i>';
        }

        return $stars;
    }

    // Relation avec les projets
public function project()
{
    return $this->belongsTo(Project::class, 'project_name', 'title');
}
}
