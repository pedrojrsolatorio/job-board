<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class JobListing extends Model
{
    use Searchable, HasSlug;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'location',
        'job_type',
        'salary_min',
        'salary_max',
        'status',
        'is_featured',
        'featured_until',
        'expires_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scout search configuration
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'job_type' => $this->job_type,
            'category' => $this->category?->name,
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
