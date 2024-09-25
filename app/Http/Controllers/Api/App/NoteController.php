<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    // Display a listing of the notes.
    public function getAllNotes()
    {
        $notes = Note::where('user_id', Auth::id())->where('type','notes')->get();
        return response()->json($notes, 200);
    }
    public function getAllLists()
    {
        $lists = Note::where('user_id', Auth::id())->where('type','lists')->get();
        return response()->json($lists, 200);
    }

    // Store a newly created note in storage.
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'notes' => 'required|string',
            'type' => 'required|in:notes,lists', // Validate 'type' as 'notes' or 'lists'
        ]);

        // Check if the user already has a note for the current day
        $existingNote = Note::where('user_id', Auth::id())
                            ->whereDate('created_at', Carbon::today())
                            ->first();

        if ($existingNote) {
            // If a note exists, update the existing note
            $existingNote->update([
                'notes' => $request->notes,
                'type' => $request->type,
            ]);

            return response()->json(['message' => 'Note updated successfully', 'note' => $existingNote], 200);
        } else {
            // If no note exists for today, create a new note
            $newNote = Note::create([
                'user_id' => Auth::id(),
                'notes' => $request->notes,
                'type' => $request->type,
            ]);

            return response()->json(['message' => 'Note created successfully', 'note' => $newNote], 201);
        }
    }


    // Display the specified note.
    public function showNotes()
    {
        // Ensure the note belongs to the authenticated user
       $note= Note::where('user_id', Auth::id())->where('type', 'notes')
        ->whereDate('created_at', Carbon::today())
        ->first();

        return response()->json($note, 200);
    }
    public function showLists()
    {
        // Ensure the note belongs to the authenticated user
       $note= Note::where('user_id', Auth::id())->where('type', 'lists')
        ->whereDate('created_at', Carbon::today())
        ->first();

        return response()->json($note, 200);
    }



    // Remove the specified note from storage.
    public function destroyNotes()
    {
        $note= Note::where('user_id', Auth::id())->where('type', 'notes')
        ->whereDate('created_at', Carbon::today())
        ->first();
        if (!$note) {
            return response()->json([
                'message' => 'not found',
            ], 404);
        }
        $note->delete();

        return response()->json(['message' => 'Note deleted successfully'], 200);
    }
    public function destroyLists()
    {
        $note= Note::where('user_id', Auth::id())->where('type', 'lists')
        ->whereDate('created_at', Carbon::today())
        ->first();
        if (!$note) {
            return response()->json([
                'message' => 'not found',
            ], 404);
        }
        $note->delete();

        return response()->json(['message' => 'list deleted successfully'], 200);
    }
}
