<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Response;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");

            });
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

        // Sorting
        if ($request->sort_by == 'newest') {

            $query->orderBy('id', 'desc');

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

    // Export CSV
    public function exportCsv()
    {
        $items = Item::all();

        $csv = "ID,Title,Description,Price,Status\n";

        foreach ($items as $item) {

            $status = $item->status ? 'Active' : 'Inactive';

            $csv .= "{$item->id},{$item->title},{$item->description},{$item->price},{$status}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=items.csv',
        ]);
    }
}