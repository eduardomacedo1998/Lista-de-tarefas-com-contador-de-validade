<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class EventController extends Controller
{
    // cria direcionamento para a view welcome.blade.php
    public function index()
    {
        return view('welcome');
    }
}
