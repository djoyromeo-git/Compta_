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
     * Affiche la liste des sites.
     *
     * @return View La vue affichant la liste des sites
     */
    public function index() : View
    {
        return view('sites.index', [
            'sites' => Site::with(['category', 'person'])->get(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau site.
     * 
     * @return View La vue du formulaire de création
     */
    public function create() : View
    {
        return view('sites.create', [
            'people' => Person::all(),
            'categories' => SiteCategory::all()
        ]);
    }

    /**
     * Stocke un nouveau site dans la base de données.
     *
     * @param StoreSiteRequest $request La requête validée contenant les données du site
     * @return RedirectResponse Redirection vers la liste des sites avec un message de succès
     */
    public function store(StoreSiteRequest $request) : RedirectResponse
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
     * Affiche les détails d'un site spécifique.
     *
     * @param Site $site Le site à afficher
     * @return View La vue affichant les détails du site
     */
    public function show(Site $site) : View
    {
        $site->load(['category', 'person', 'transactions' => function($query) {
            $query->latest()->with(['type', 'currency']);
        }]);

        return view('sites.show', compact('site'));
    }

    /**
     * Affiche le formulaire de modification d'un site.
     * 
     * @param Site $site Le site à modifier
     * @return View La vue du formulaire de modification
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
     * Met à jour un site existant dans la base de données.
     *
     * @param UpdateSiteRequest $request La requête validée contenant les nouvelles données
     * @param Site $site Le site à mettre à jour
     * @return RedirectResponse Redirection vers la liste des sites avec un message de succès
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
     * Supprime un site de la base de données.
     *
     * @param Site $site Le site à supprimer
     * @return RedirectResponse Redirection vers la liste des sites avec un message de succès
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
