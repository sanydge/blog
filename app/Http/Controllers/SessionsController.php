<?php

namespace App\Http\Controllers;


use Nette\Schema\ValidationException;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store()
    {
       $attributes = request()->validate([
            'email'    => 'required|exists:users,email',
            'password' => 'required'
        ]);

        if (auth()->attempt($attributes)) {
            session()->regenerate();

            return redirect('/')->with('success', 'Welcome back!');
        }

        throw ValidationException::withMessage([
            'email'=> 'Your credentials could not be verified.'
        ]);
    }

    public function destroy()
    {
        auth()->logout();

        return redirect('/')->with('success', 'Goodbye!');
    }
}
