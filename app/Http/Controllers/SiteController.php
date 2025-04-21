<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Models\Site;
use App\Models\Person;
use App\Models\SiteCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    /**
     * Display a listing of the sites.
     */
    public function index() : View
    {
        return view('sites.index', [
            'sites' => Site::with(['category', 'person'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new site.
     */
    public function create() : View
    {
        return view('sites.create', [
            'people' => Person::all(),
            'categories' => SiteCategory::all()
        ]);
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(StoreSiteRequest $request): RedirectResponse
    {
        Site::create($request->only([
            'name',
            'site_category_id',
            'person_id',
            'address'
        ]));

        return redirect()->route('sites.index')
            ->with('success', 'Site créé avec succès.');
    }

    /**
     * Display the specified site.
     */
    public function show(Site $site) : View
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
        return view('sites.edit', [
            'people' => Person::all(),
            'categories' => SiteCategory::all(),
            'site' => $site
        ]);
    }

    /**
     * Update the specified site in storage.
     */
    public function update(UpdateSiteRequest $request, Site $site) : RedirectResponse
    {
        $site->update($request->only([
            'name',
            'site_category_id',
            'person_id',
            'address'
        ]));

        return redirect()->route('sites.index')
            ->with('success', 'Site mis à jour avec succès.');
    }

    /**
     * Remove the specified site from storage.
     */
    public function destroy(Site $site) : RedirectResponse
    {
        if ($site->transactions()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce site car il contient des transactions.');
        }

        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site supprimé avec succès.');
    }
}
