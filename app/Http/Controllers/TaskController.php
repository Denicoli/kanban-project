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
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
            'status' => 'sometimes|string|in:not-started,in-research,on-track,code-review,completed',
        ]);

        $task = $column->tasks()->create($request->only(['title', 'description', 'due_date', 'order', 'priority', 'status']));

        return response()->json($task, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

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
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
            'status' => 'sometimes|string|in:not-started,in-research,on-track,code-review,completed',
        ]);

        $task->update($request->only(['title', 'description', 'due_date', 'order', 'column_id', 'priority', 'status']));

        return response()->json($task, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();
        
        return response()->json(['message' => 'Task deleted successfully.'], Response::HTTP_NO_CONTENT);
    }
}
