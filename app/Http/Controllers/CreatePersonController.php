<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreatePersonController extends Controller
{
    public function create()
    {
        return view("salesperson-form/create");
    }
}
