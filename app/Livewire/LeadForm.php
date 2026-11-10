<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\Product;
use App\Services\Telegram;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Livewire\Component;

class LeadForm extends Component
{
    public bool $open = false;
    public bool $sent = false;

    public string $name = '';
    public string $phone = '';
    public string $product = '';
    public ?int $productId = null;
    public ?int $variantId = null;
    public string $website = ''; // ловушка для ботов

    #[On('open-lead')]
    public function show(?string $product = null, ?int $productId = null, ?int $variantId = null): void
    {
        $this->resetErrorBag();
        $this->sent = false;
        $this->product = $product ?? '';
        $this->productId = $productId;
        $this->variantId = $variantId;
        $this->open = true;
    }

    public function submit(): void
    {
        $data = $this->validate([
            'name' => 'required|string|min:2|max:80',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s+\-()]{7,}$/'],
            'product' => 'required|string|max:200',
        ], [
            'name.required' => 'Укажите имя',
            'name.min' => 'Имя слишком короткое',
            'phone.required' => 'Укажите телефон',
            'phone.regex' => 'Проверьте номер телефона',
            'product.required' => 'Выберите модель',
        ]);

        if ($this->website !== '') {
            $this->sent = true;

            return;
        }

        $key = 'lead:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('phone', 'Слишком много заявок. Напишите нам в Telegram или WhatsApp.');

            return;
        }
        RateLimiter::hit($key, 600);

        $lead = Lead::create($data + [
            'product_id' => $this->productId,
            'variant_id' => $this->variantId,
            'source_url' => $this->productId ? route('product', Product::find($this->productId)?->slug ?? '') : url('/'),
        ]);

        $lead->update(['telegram_sent' => Telegram::sendLead($lead)]);

        $this->reset('name', 'phone');
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.lead-form', [
            'models' => Product::where('is_active', true)->orderBy('category_id')->orderBy('sort')->pluck('name'),
        ]);
    }
}
