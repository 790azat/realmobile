<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ShopController extends Controller
{
    public function home()
    {
        $phones = Product::with('variants')->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('slug', 'iphone'))->orderBy('sort')->get();

        return view('home', [
            'phones' => $phones,
            'flagship' => $phones->first(),
            'accCats' => Category::where('parent_id', Category::accessoriesRoot()?->id)->withCount(['products' => fn ($q) => $q->where('is_active', true)])->orderBy('sort')->get(),
        ]);
    }

    public function accessories(?string $slug = null)
    {
        $root = Category::accessoriesRoot();
        abort_unless($root, 404);
        $cats = $root->children()->withCount(['products' => fn ($q) => $q->where('is_active', true)])->get();
        $current = $slug ? $cats->firstWhere('slug', $slug) : null;
        abort_if($slug && ! $current, 404);

        $products = Product::with('variants')->where('is_active', true)
            ->whereIn('category_id', $current ? [$current->id] : $cats->pluck('id'))
            ->orderBy('sort')->get();

        return view('accessories', compact('cats', 'current', 'products'));
    }

    public function sitemap()
    {
        $urls = collect([route('home'), route('catalog'), route('accessories')])
            ->merge(Category::whereNotNull('parent_id')->pluck('slug')->map(fn ($s) => route('accessories.category', $s)))
            ->merge(Product::where('is_active', true)->get(['slug', 'updated_at'])->map(fn ($p) => route('product', $p->slug)));

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $lines = ['User-agent: *', 'Disallow: /admin', 'Disallow: /livewire', 'Sitemap: '.route('sitemap')];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain');
    }
}
