<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // GET: Items list with pagination & search
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('price', 'like', "%{$request->search}%")
                  ->orWhere('status', 'like', "%{$request->search}%");
        }

        $items = $query->orderBy('id', 'asc')->paginate(3);

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'last_page' => $items->lastPage(),   // <-- Add this
        ]);
    }


    // POST: Create Item
    public function store(Request $request)
    {
        $item = Item::create($request->only([
            'title', 'description', 'price', 'status', 'created_by', 'updated_by'
        ]));
        return response()->json($item);
    }

    // GET: Fetch single item for edit
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }

    // PUT/PATCH: Update Item
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->update($request->only([
            'title', 'description', 'price', 'status', 'updated_by'
        ]));
        return response()->json($item);
    }

    // DELETE: Remove Item
    public function destroy($id)
    {
        Item::destroy($id);
        return response()->json(['success' => true]);
    }
}
