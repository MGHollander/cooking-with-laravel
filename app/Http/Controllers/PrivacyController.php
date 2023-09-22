<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class PrivacyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function __invoke(): Response
    {
        return Inertia::render('Privacy');
    }

}
