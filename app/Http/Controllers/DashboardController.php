<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isEmployer()) {
            return redirect()->route('my-jobs.index');
        }

        if ($user->isJobSeeker()) {
            return redirect()->route('my-applications');
        }

        return redirect()->route('home');
    }
}
