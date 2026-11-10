<?php

namespace Tests\Feature;

use App\Livewire\LeadForm;
use App\Livewire\ProductPage;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_public_pages_open(): void
    {
        foreach (['/', '/iphone', '/iphone/iphone-18-pro-max', '/accessories', '/accessories/cases', '/sitemap.xml', '/robots.txt'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/iphone/iphone-17')->assertSee('iPhone 17')->assertSee('wa.me/79060010090', false)->assertSee('t.me/+79163774630', false);
    }

    public function test_every_variant_has_a_real_image(): void
    {
        foreach (\App\Models\ProductVariant::with('product')->get() as $v) {
            $key = ltrim(str_replace('/img/', '', $v->image), '/');
            $this->assertNotEmpty(config("phone_images.$key"), "Нет фото для {$v->product->name} {$v->color}");
        }
    }

    public function test_variant_switch_changes_price(): void
    {
        Livewire::test(ProductPage::class, ['slug' => 'iphone-18-pro'])
            ->call('selectStorage', '1 ТБ')
            ->assertSee('169 990');
    }

    public function test_lead_is_saved_and_sent_to_telegram(): void
    {
        config(['services.telegram.bot_token' => 'test', 'services.telegram.chat_id' => '1']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        Livewire::test(LeadForm::class)
            ->call('show', 'iPhone 17 · 256 ГБ · Белый')
            ->set('name', 'Иван')->set('phone', '+7 900 111-22-33')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseHas('leads', ['name' => 'Иван', 'telegram_sent' => true]);
        Http::assertSent(fn ($r) => str_contains($r['text'], 'iPhone 17 · 256 ГБ · Белый'));
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        config(['shop.admin_password' => 'secret']);
        $this->post('/admin/login', ['login' => 'admin', 'password' => 'secret'])->assertRedirect('/admin');
        $this->get('/admin/products/1/edit')->assertOk();
        $this->get('/admin/leads')->assertOk()->assertSee(Lead::first()->name);
    }
}
