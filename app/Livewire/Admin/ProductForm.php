<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public ?int $category_id = null;
    public string $name = '';
    public string $slug = '';
    public string $badge = '';
    public string $description = '';
    public string $image = '';
    public bool $is_active = true;
    public string $meta_title = '';
    public string $meta_description = '';

    /** @var array<int, array{key:string, value:string}> */
    public array $specs = [];

    /** @var array<int, array<string, mixed>> */
    public array $variants = [];

    // Быстрое создание вариантов: все сочетания памяти и цветов
    public string $genStorages = '';
    public string $genColors = '';
    public ?int $genPrice = null;

    public $upload;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product->load('variants');
            $this->fill($product->only('category_id', 'name', 'slug', 'is_active'));
            foreach (['badge', 'description', 'image', 'meta_title', 'meta_description'] as $f) {
                $this->{$f} = (string) $product->{$f};
            }
            $this->specs = collect($product->specs ?? [])->map(fn ($v, $k) => ['key' => $k, 'value' => $v])->values()->all();
            $this->variants = $product->variants->map(fn ($v) => $v->only('id', 'storage', 'color', 'color_hex', 'price', 'old_price', 'stock', 'image'))->all();
        } else {
            $this->category_id = Category::where('slug', request('category', 'iphone'))->value('id') ?? Category::value('id');
            $this->specs = [['key' => 'Гарантия', 'value' => '12 месяцев']];
            $this->variants = [$this->blankVariant()];
        }
    }

    private function blankVariant(array $over = []): array
    {
        return $over + ['id' => null, 'storage' => '', 'color' => '', 'color_hex' => '', 'price' => null, 'old_price' => null, 'stock' => 0, 'image' => ''];
    }

    public function updatedName(): void
    {
        if (! $this->product) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedUpload(): void
    {
        $this->validate(['upload' => 'image|max:4096']);
        if (env('VERCEL')) {
            $this->addError('upload', 'В демо-версии загрузка файлов отключена: вставьте ссылку на фото. На боевом хостинге загрузка работает.');

            return;
        }
        try {
            $path = $this->upload->store('products', 'public');
            $this->image = '/storage/'.$path;
        } catch (\Throwable $e) {
            $this->addError('upload', 'На демо-хостинге загрузка файлов отключена. Вставьте ссылку на фото.');
        }
    }

    public function addSpec(): void
    {
        $this->specs[] = ['key' => '', 'value' => ''];
    }

    public function removeSpec(int $i): void
    {
        unset($this->specs[$i]);
        $this->specs = array_values($this->specs);
    }

    public function addVariant(): void
    {
        $last = end($this->variants) ?: [];
        $this->variants[] = $this->blankVariant(['price' => $last['price'] ?? null, 'color_hex' => $last['color_hex'] ?? '', 'color' => $last['color'] ?? '', 'image' => $last['image'] ?? '']);
    }

    public function removeVariant(int $i): void
    {
        unset($this->variants[$i]);
        $this->variants = array_values($this->variants);
    }

    public function generate(): void
    {
        $storages = array_values(array_filter(array_map('trim', explode(',', $this->genStorages)))) ?: [''];
        $colors = array_values(array_filter(array_map('trim', explode(',', $this->genColors)))) ?: [''];
        $this->variants = array_values(array_filter($this->variants, fn ($v) => $v['id'] || $v['price']));
        foreach ($storages as $s) {
            foreach ($colors as $c) {
                $this->variants[] = $this->blankVariant(['storage' => $s, 'color' => $c, 'price' => $this->genPrice]);
            }
        }
        $this->reset('genStorages', 'genColors', 'genPrice');
    }

    public function save()
    {
        $this->slug = Str::slug($this->slug ?: $this->name);
        $data = $this->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:120|unique:products,slug,'.($this->product?->id ?? 'NULL'),
            'badge' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'image' => 'required|string|max:2000',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:160',
            'meta_description' => 'nullable|string|max:300',
            'variants' => 'required|array|min:1',
            'variants.*.storage' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:40',
            'variants.*.color_hex' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'variants.*.price' => 'required|integer|min:1',
            'variants.*.old_price' => 'nullable|integer|min:1',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|string|max:2000',
        ], [
            'image.required' => 'Добавьте фото товара: загрузите файл или вставьте ссылку.',
            'variants.*.price.required' => 'Укажите цену',
            'variants.*.color_hex.regex' => 'Формат #RRGGBB',
        ]);

        $specs = collect($this->specs)->filter(fn ($s) => trim($s['key']) !== '')->mapWithKeys(fn ($s) => [trim($s['key']) => trim($s['value'])])->all();

        DB::transaction(function () use ($data, $specs) {
            $fields = collect($data)->except('variants')->map(fn ($v) => $v === '' ? null : $v)->all();
            $fields['specs'] = $specs;
            $fields['family'] = $data['name'];

            $product = $this->product ?? new Product(['sort' => (int) Product::max('sort') + 1]);
            $product->fill($fields)->save();

            $keep = [];
            foreach ($data['variants'] as $v) {
                $gb = $this->storageToGb($v['storage'] ?? '');
                $row = [
                    'storage' => $v['storage'] ?: null,
                    'storage_gb' => $gb,
                    'color' => $v['color'] ?: null,
                    'color_hex' => $v['color_hex'] ?: null,
                    'price' => (int) $v['price'],
                    'old_price' => $v['old_price'] ? (int) $v['old_price'] : null,
                    'stock' => (int) $v['stock'],
                    'image' => $v['image'] ?: null,
                ];
                $variant = ! empty($v['id']) ? $product->variants()->find($v['id']) : null;
                $variant ? $variant->update($row) : $variant = $product->variants()->create($row);
                $keep[] = $variant->id;
            }
            $product->variants()->whereNotIn('id', $keep)->delete();
            $this->product = $product;
        });

        session()->flash('saved', 'Сохранено');

        return $this->redirectRoute('admin.products.edit', $this->product, navigate: true);
    }

    public function delete()
    {
        $this->product?->delete();

        return $this->redirectRoute('admin.products', navigate: true);
    }

    private function storageToGb(string $s): ?int
    {
        if (! preg_match('/(\d+)\s*(тб|tb|гб|gb)?/iu', $s, $m)) {
            return null;
        }

        return (int) $m[1] * (in_array(mb_strtolower($m[2] ?? ''), ['тб', 'tb']) ? 1024 : 1);
    }

    public function render()
    {
        return view('livewire.admin.product-form', [
            'categories' => Category::orderBy('parent_id')->orderBy('sort')->get()->filter(fn ($c) => $c->slug !== 'accessories'),
        ])->title($this->product ? $this->product->name : 'Новый товар');
    }
}
