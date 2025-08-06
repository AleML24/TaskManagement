<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Utils\ResponseFormat;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $keywords = Keyword::orderBy('name')->get();
            return ResponseFormat::response(200, 'Keywords retrieved successfully', $keywords);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:keywords,name',
            ]);

            $keyword = Keyword::create($validatedData);

            return ResponseFormat::response(201, 'Keyword created successfully', $keyword);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keyword $keyword)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:keywords,name,' . $keyword->id,
            ]);

            $keyword->update($validatedData);

            return ResponseFormat::response(200, 'Keyword updated successfully', $keyword);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keyword $keyword)
    {
        try {
            $keyword->delete();
            return ResponseFormat::response(200, 'Keyword deleted successfully', null);
        } catch (\Exception $e) {
            return ResponseFormat::exceptionResponse($e);
        }
    }
}
