<?php

namespace App\Http\Controllers;

use App\Services\FilmService;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    protected $filmService;

    public function __construct(FilmService $filmService)
    {
        $this->filmService = $filmService;
    }

    public function index()
    {
        return response()->json($this->filmService->getAll());
    }

    public function show($id)
    {
        return response()->json($this->filmService->get($id));
    }

    public function store(Request $request)
    {
        return response()->json($this->filmService->create($request->all()), 201);
    }

    public function update(Request $request, $id)
    {
        return response()->json($this->filmService->update($id, $request->all()));
    }

    public function destroy($id)
    {
        return response()->json($this->filmService->delete($id));
    }
}
