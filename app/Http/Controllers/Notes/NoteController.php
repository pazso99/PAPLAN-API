<?php

namespace App\Http\Controllers\Notes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\NoteCreateRequest;
use App\Http\Requests\Notes\NoteUpdateRequest;
use App\Http\Resources\Notes\NoteResource;
use App\Models\Notes\Note;

class NoteController extends Controller
{
    /**
     * Get all notes
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return NoteResource::collection(Note::all());
    }

    /**
     * Save a note
     *
     * @param \App\Http\Requests\Notes\NoteCreateRequest $request
     * @return \App\Http\Resources\Notes\NoteResource
     */
    public function store(NoteCreateRequest $request)
    {
        $note = Note::create([
            'status' => $request->status,
            'name' => $request->name,
            'due_date' => $request->dueDate,
            'priority' => $request->priority,
            'description' => $request->description,
        ]);

        return NoteResource::make($note);
    }

    /**
     * Get one note
     *
     * @param \App\Models\Notes\Note $note
     * @return \App\Http\Resources\Notes\NoteResource
     */
    public function show(Note $note)
    {
        return NoteResource::make($note);
    }

    /**
     * Update a note
     *
     * @param \App\Http\Requests\Notes\NoteUpdateRequest $request
     * @param \App\Models\Notes\Note $note
     * @return \App\Http\Resources\Notes\NoteResource
     */
    public function update(NoteUpdateRequest $request, Note $note)
    {
        $note->update([
            'status' => $request->status,
            'name' => $request->name,
            'due_date' => $request->dueDate,
            'priority' => $request->priority,
            'description' => $request->description,
        ]);

        return NoteResource::make($note);
    }

    /**
     * Remove a note
     *
     * @param \App\Models\Notes\Note $note
     * @return \App\Http\Resources\Notes\NoteResource
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return NoteResource::make($note);
    }
}
