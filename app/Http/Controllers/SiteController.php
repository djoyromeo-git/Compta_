<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Person;
use App\Models\SiteCategory;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the sites.
     */
    public function index()
    {
        $sites = Site::with(['category', 'person'])->get();
        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new site.
     */
    public function create()
    {
        $categories = SiteCategory::all();
        $people = Person::all();
        return view('sites.create', compact('categories', 'people'));
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'site_category_id' => 'required|exists:site_categories,id',
            'person_id' => 'required|exists:people,id',
            'address' => 'nullable|string'
        ]);

        Site::create($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site créé avec succès.');
    }

    /**
     * Display the specified site.
     */
    public function show(Site $site)
    {
        $site->load(['category', 'person', 'transactions' => function($query) {
            $query->latest()->with(['type', 'currency']);
        }]);
        
        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified site.
     */
    public function edit(Site $site)
    {
        $categories = SiteCategory::all();
        $people = Person::all();
        return view('sites.edit', compact('site', 'categories', 'people'));
    }

    /**
     * Update the specified site in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'site_category_id' => 'required|exists:site_categories,id',
            'person_id' => 'required|exists:people,id',
            'address' => 'nullable|string'
        ]);

        $site->update($validated);

        return redirect()->route('sites.index')
            ->with('success', 'Site mis à jour avec succès.');
    }

    /**
     * Remove the specified site from storage.
     */
    public function destroy(Site $site)
    {
        if ($site->transactions()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce site car il contient des transactions.');
        }

        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès.');
    }
}
