<?php

namespace App\Http\Controllers;

use App\Models\Subjects;
use App\Models\User;
use App\Models\UserSubjects;

class HomeController extends Controller
{
    public function home()
    {
        $stats = [
            'users' => User::count(),
            'subjects' => Subjects::count(),
            'assignments' => UserSubjects::count(),
        ];

        $recentSubjects = Subjects::latest()->take(4)->get();

        return view('home', [
            'data' => 'Welcome back, Admin!',
            'stats' => $stats,
            'recentSubjects' => $recentSubjects,
        ]);
    }
}