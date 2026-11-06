<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Category;
use App\Models\Parcel;
use App\Models\Scopes\CategoryScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public homepage: the company, its projects and the account statement lookup.
     */
    public function __invoke(): View
    {
        $figures = Cache::remember('home.figures', now()->addMinutes(10), fn () => $this->figures());

        return view('home', [
            'projects' => $figures['projects'],
            'stats' => $figures['stats'],
            'company' => config('home.company'),
        ]);
    }

    /**
     * Figures of every campaign, whoever is signed in (the category scope would limit them to one).
     *
     * @return array{
     *     projects: list<array<string, mixed>>,
     *     stats: array{projects: int, lots: int, sold: int, families: int, square_meters: int}
     * }
     */
    private function figures(): array
    {
        $lotsByCategory = Parcel::withoutGlobalScope(CategoryScope::class)
            ->toBase()
            ->selectRaw('category_id, COUNT(*) as lots')
            ->selectRaw('SUM(CASE WHEN promise_id IS NULL THEN 1 ELSE 0 END) as available')
            ->selectRaw('MIN(NULLIF(area_m2, 0)) as min_area')
            ->selectRaw('SUM(area_m2) as area')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        $projects = Category::query()->orderBy('id')->get()->map(function (Category $category) use ($lotsByCategory) {
            $lots = $lotsByCategory->get($category->id);
            $content = config("home.projects.{$category->name}", config('home.default_project'));
            $total = (int) ($lots->lots ?? 0);
            $available = (int) ($lots->available ?? 0);

            return [
                ...$content,
                'name' => $category->name,
                'lots' => $total,
                'available' => $available,
                'sold' => $total - $available,
                'min_area' => $lots?->min_area ? (int) round((float) $lots->min_area) : null,
                'status' => match (true) {
                    $total === 0 => 'soon',
                    $available === 0 => 'sold_out',
                    $available / $total <= 0.25 => 'few_left',
                    default => 'on_sale',
                },
            ];
        })->sortBy(fn (array $project) => $project['status'] === 'soon')->values()->all();

        $families = Buyer::withoutGlobalScope(CategoryScope::class)
            ->whereHas('promises', fn ($query) => $query->withoutGlobalScope(CategoryScope::class))
            ->pluck('document_number')
            ->map(fn (string $document) => str_replace(['.', ' ', '-'], '', $document))
            ->unique()
            ->count();

        return [
            'projects' => $projects,
            'stats' => [
                'projects' => count($projects),
                'lots' => (int) $lotsByCategory->sum('lots'),
                'sold' => (int) ($lotsByCategory->sum('lots') - $lotsByCategory->sum('available')),
                'families' => $families,
                'square_meters' => (int) round((float) $lotsByCategory->sum('area')),
            ],
        ];
    }
}
