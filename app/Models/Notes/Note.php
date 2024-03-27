<?php

namespace App\Models\Notes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes.notes';
    protected $fillable = ['status', 'name', 'due_date', 'priority', 'description'];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
