<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index()  { return Tag::all(); }

    public function store(Request $r)
    {
        $data = $r->validate(['name' => 'required|unique:tags,name']);
        return Tag::create($data);
    }

    public function update(Request $r, Tag $tag)
    {
        $data = $r->validate(['name' => 'required|unique:tags,name,'.$tag->id]);
        $tag->update($data);
        return $tag;
    }

    public function destroy(Tag $tag) { $tag->delete(); return response()->noContent(); }
}
