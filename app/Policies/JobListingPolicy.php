<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JobListing;

class JobListingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, JobListing $jobListing): bool
    {
        return $jobListing->status === 'active' || $jobListing->user_id === $user?->id;
    }

    public function create(User $user): bool
    {
        return $user->isEmployer();
    }

    public function update(User $user, JobListing $jobListing): bool
    {
        return $user->isAdmin() || $jobListing->user_id === $user->id;
    }

    public function delete(User $user, JobListing $jobListing): bool
    {
        return $user->isAdmin() || $jobListing->user_id === $user->id;
    }
}
