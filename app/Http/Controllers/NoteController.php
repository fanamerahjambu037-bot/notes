<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'parent_id']);
        $notes = Note::orderByDesc('id')->get();
        return view('home', compact('categories', 'notes'));
    }

    public function store(Request $request, SupabaseStorage $storage)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:text,pdf',
            'content' => 'required_if:type,text|nullable|string',
            'file' => 'required_if:type,pdf|nullable|file|mimes:pdf|max:4096',
        ]);

        $payload = [
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'type' => $data['type'],
        ];

        if ($data['type'] === 'text') {
            $payload['content'] = $data['content'];
        } else {
            $file = $request->file('file');
            $path = Str::uuid() . '.pdf';
            try {
                $payload['file_url'] = $storage->upload($file, $path);
            } catch (\Throwable $e) {
                return back()->withErrors(['file' => $e->getMessage()])->withInput();
            }
            $payload['file_path'] = $path;
            $payload['file_name'] = $file->getClientOriginalName();
        }

        Note::create($payload);
        return back();
    }

    public function destroy(Note $note, SupabaseStorage $storage)
    {
        if ($note->type === 'pdf' && $note->file_path) {
            $storage->delete($note->file_path);
        }
        $note->delete();
        return back();
    }
}
