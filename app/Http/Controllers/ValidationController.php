<?php

namespace App\Http\Controllers;

// use App\Models\User;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
   public function checkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users,email',
    ]);

    return back(); // Inertia will handle the success/error state
}
}
