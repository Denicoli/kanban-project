<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'column_id',
        'title',
        'description',
        'due_date',
        'order',
        'priority',
        'status',
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }
}
