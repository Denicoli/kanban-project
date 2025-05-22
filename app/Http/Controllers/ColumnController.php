<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Column;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ColumnController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Board $board)
    {
        $this->authorize('view', $board);

        $columns = $board->columns()->with('tasks')->get();
        if ($columns->isEmpty()) {
            return response()->json(['message' => 'No columns found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($columns, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Board $board)
    {
        $this->authorize('create', $board);

        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $column = $board->columns()->create($request->only(['title', 'order']));

        return response()->json($column, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Column $column)
    {
        $this->authorize('view', $column);

        if (!$column) {
            return response()->json(['message' => 'Column not found.'], Response::HTTP_NOT_FOUND);
        }

        $column->load('tasks');

        return response()->json($column, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Column $column)
    {
        $this->authorize('update', $column);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|required|integer',
        ]);

        $column->update($request->only(['title', 'order']));

        return response()->json($column, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Column $column)
    {
        if ($column->tasks()->exists()) {
            return response()->json(['error' => 'Cannot delete column with tasks.'], Response::HTTP_BAD_REQUEST);
        }

        $this->authorize('delete', $column);

        $column->delete();

        return response()->json(['message' => 'Column deleted successfully.'], Response::HTTP_NO_CONTENT);
    }
}
