<?php

namespace App\Http\Controllers;

use App\Models\CulturalEntry;
use App\Models\Media;
use Illuminate\Http\Request;

class CulturalEntryController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = CulturalEntry::query()
            ->publiclyVisible()
            ->with([
                'region',
                'categories',
                'media' => fn ($query) => $query
                    ->where('permission_status', Media::PERMISSION_GRANTED)
                    ->orderByDesc('is_cover')
                    ->orderBy('sort_order'),
                'sources',
            ]);

        $search = trim($validated['search'] ?? '');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $entries = $query
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('cultural-entries.index', compact('entries'));
    }
}
