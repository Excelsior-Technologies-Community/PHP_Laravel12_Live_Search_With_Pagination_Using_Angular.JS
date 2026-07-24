<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query()->with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Min Price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Max Price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date From
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Date To
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->sort_by;
        $sortDir = $request->sort_dir ?? 'asc';

        if ($sortBy == 'newest') {
            $query->orderBy('id', 'desc');
        } elseif ($sortBy == 'oldest') {
            $query->orderBy('id', 'asc');
        } elseif ($sortBy == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sortBy == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('id', 'asc');
        }

        $items = $query->paginate(4);

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'last_page' => $items->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:0,1',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|string'
        ]);

        if ($request->has('status')) {
            $data['status'] = (bool)$request->status;
        } else {
            $data['status'] = true;
        }

        if (empty($data['category_id'])) {
            $data['category_id'] = null;
        }

        $item = Item::create($data);

        return response()->json(['data' => $item->load('category')], 201);
    }

    public function show(Item $item)
    {
        return response()->json(['data' => $item->load('category')]);
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:0,1',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|string'
        ]);

        if ($request->has('status')) {
            $data['status'] = (bool)$request->status;
        }

        if (empty($data['category_id'])) {
            $data['category_id'] = null;
        }

        $item->update($data);

        return response()->json(['data' => $item->load('category')]);
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(['success' => true], 200);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id'
        ]);

        Item::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Items deleted successfully']);
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:items,id',
            'status' => 'required|boolean'
        ]);

        Item::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function restore($id)
    {
        $item = Item::withTrashed()->findOrFail($id);
        $item->restore();
        return response()->json(['data' => $item->load('category')]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            return response()->json(['path' => $path]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function searchSuggestions(Request $request)
    {
        $search = $request->get('q', '');
        
        $items = Item::where('title', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'title']);

        return response()->json(['data' => $items]);
    }

    // Export CSV
    public function exportCsv()
    {
        $items = Item::with('category')->get();

        $csv = "ID,Title,Description,Price,Category,Status,Created At\n";

        foreach ($items as $item) {
            $status = $item->status ? 'Active' : 'Inactive';
            $category = $item->category ? $item->category->name : 'N/A';
            $csv .= "{$item->id},\"{$item->title}\",\"{$item->description}\",{$item->price},\"{$category}\",{$status},{$item->created_at}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=items.csv',
        ]);
    }
}
