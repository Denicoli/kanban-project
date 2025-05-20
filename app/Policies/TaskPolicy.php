<?php

namespace App\Policies;

use App\Models\Column;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->column->board->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->column->board->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->column->board->user_id;
    }
}
