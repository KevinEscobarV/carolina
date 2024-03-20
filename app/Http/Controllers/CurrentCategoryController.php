<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CurrentCategoryController extends Controller
{
        /**
     * Update the authenticated user's current team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
