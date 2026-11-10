<?php

namespace App\Livewire\Admin;

use App\Models\Lead;
use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Обзор')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'leadsToday' => Lead::whereDate('created_at', today())->count(),
            'leadsNew' => Lead::where('status', 'new')->count(),
            'productsCount' => Product::count(),
            'inStock' => (int) ProductVariant::sum('stock'),
            'lowStock' => ProductVariant::with('product')->whereBetween('stock', [1, 2])->count(),
            'outOfStock' => ProductVariant::where('stock', 0)->count(),
            'leads' => Lead::latest()->limit(6)->get(),
            'products' => Product::with('variants')->whereHas('category', fn ($q) => $q->where('slug', 'iphone'))->orderBy('sort')->get(),
        ]);
    }
}
