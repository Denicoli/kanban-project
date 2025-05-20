<?php

namespace App\Policies;

use App\Models\Column;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColumnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
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
    public function update(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Column $column): bool
    {
        return $user->id === $column->board->user_id;
    }
}
