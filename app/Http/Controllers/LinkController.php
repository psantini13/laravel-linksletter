<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Link;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class LinkController extends Controller
{
    public function index(): View|Factory|Application {
        $links = Link::query()
            // Filter by tenant id (user_id)
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('links.index', [
            'links' => $links,
        ]);
    }

    public function create(): View|Factory|Application {
        $users = User::all();

        return view('links.create', [
            'users' => $users,
        ]);
    }

    public function store(StoreLinkRequest $request): RedirectResponse {
        $link = Link::create(
            $request->validated() + [
                'user_id' => auth()->id(),
            ]
        );

        // If there is no position, set it to the last
        if (! $link->position) {
            // @phpstan-ignore-next-line
            $link->position = Link::max('position') + 1;
            $link->save();
        }

        return redirect()->route('links.index')
            ->with('message', 'Link created successfully.');
    }

    public function edit(Link $link): View|Factory|Application {
        // Check if user is the owner of the link
        abort_unless($link->user_id === auth()->id(), 404);

        $users = User::all();

        return view('links.edit', [
            'link' => $link,
            'users' => $users,
        ]);
    }

    public function update(UpdateLinkRequest $request, Link $link): RedirectResponse {
        abort_unless($link->user_id === auth()->id(), 404);

        $link->update($request->validated());

        return redirect()->route('links.index')
            ->with('message', 'Link updated successfully.');
    }

    public function destroy(Link $link): RedirectResponse {
        abort_unless($link->user_id === auth()->id(), 404);

        $link->delete();

        return redirect()->route('links.index')
            ->with('message', 'Link deleted successfully.');
    }
}
