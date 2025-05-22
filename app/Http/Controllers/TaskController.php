<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Column $column)
    {
        $this->authorize('view', $column->board);

        $tasks = $column->tasks()->get();

        return response()->json($tasks, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Column $column)
    {
        $this->authorize('create', $column);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'order' => 'required|integer',
        ]);

        $task = $column->tasks()->create($request->only(['title', 'description', 'due_date', 'order']));

        return response()->json($task, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Column $column, Task $task)
    {
        $this->authorize('view', $column->board);

        if ($task->column_id !== $column->id) {
            return response()->json(['error' => 'Task does not belong to the specified column.'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($task, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($request->has('column_id')) {
            $task->column_id = $request->input('column_id');
            $task->setRelation('column', Column::find($task->column_id));
        }

        $this->authorize('update', $task);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'order' => 'sometimes|required|integer',
        ]);

        $task->update($request->only(['title', 'description', 'due_date', 'order', 'column_id']));

        return response()->json($task, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Column $column, Task $task)
    {
        $this->authorize('delete', $column->board);

        if ($task->column_id !== $column->id) {
            return response()->json(['error' => 'Task does not belong to the specified column.'], Response::HTTP_BAD_REQUEST);
        }

        $task->delete();
        
        return response()->json(['message' => 'Task deleted successfully.'], Response::HTTP_NO_CONTENT);
    }
}
