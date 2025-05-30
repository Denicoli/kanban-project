<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BoardController extends Controller 
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Board::class);

        $boards = $request->user()->boards()->get();

        return response()->json($boards, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Board::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board = $request->user()->boards()->create($validated);

        $defaultColumns = [
            ['title' => 'Backlog'],
            ['title' => 'To Do'],
            ['title' => 'In Progress'],
            ['title' => 'Done'],
        ];

        foreach ($defaultColumns as $col) {
            $board->columns()->create($col);
        }

        $board->load('columns');

        return response()->json($board, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        $this->authorize('view', $board);

        $board->load('columns.tasks');
        return response()->json($board, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $board->update($validated);

        return response()->json($board, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return response()->json(['message' => 'Board deleted successfully.'], Response::HTTP_NO_CONTENT);
    }
}