<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurrentCategoryController extends Controller
{
    /**
     * Update the authenticated user's current team.
     *
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $category = Category::findOrFail($request->category_id);

        if (! $request->user()->switchCategory($category)) {
            abort(403);
        }

        return redirect()->back();
    }
}
