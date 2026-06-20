<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_listing_id',
        'user_id',
        'cover_letter',
        'resume_path',
        'status',
        'match_score',
        'ai_summary',
        'ai_strengths',
        'ai_gaps',
        'ai_status',
        'ai_analyzed_at',
        'ai_error',
    ];

    protected $casts = [
        'ai_strengths' => 'array',
        'ai_gaps'        => 'array',
        'ai_analyzed_at' => 'datetime',
    ];

    public function jobListing()
    {
        return $this->belongsTo(JobListing::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
