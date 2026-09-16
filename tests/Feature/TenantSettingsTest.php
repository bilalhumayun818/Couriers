<?php

namespace Tests\Feature;

use App\Models\{User, TenantSetting, Trip, Van, Customer};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): array
    {
        return ['company_name' => 'Courier Limited', 'currency_code' => 'GBP', 'currency_symbol' => '£', 'tax_rate' => '15.50', 'tax_label' => 'Sales Tax'];
    }

    public function test_admin_settings_are_saved_and_used_for_new_trips(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->get('/settings/tenant')->assertOk()->assertSee('Save Settings');
        $this->put('/settings/tenant', $this->settings())->assertRedirect(route('settings.tenant'));
        $this->assertDatabaseHas('tenant_settings', $this->settings());
        $this->get('/settings/tenant')->assertSee('Courier Limited');
        $van = Van::create(['plate_number' => 'TEST', 'make_model' => 'Test', 'year' => 2025, 'status' => 'active']);
        $customer = Customer::create(['company_name' => 'Test customer']);
        $data = ['van_id' => $van->id, 'customer_id' => $customer->id, 'trip_date' => now()->toDateString(), 'origin' => 'A', 'destination' => 'B', 'fare_amount' => 100];
        $this->post('/operations/trips', $data)->assertSessionHasNoErrors();
        $this->assertSame('15.50', Trip::first()->tax_amount);
        $this->put('/settings/tenant', array_merge($this->settings(), ['tax_rate' => 0]));
        $this->post('/operations/trips', $data)->assertSessionHasNoErrors();
        $this->assertSame('15.50', Trip::first()->tax_amount);
        $this->assertSame('0.00', Trip::latest('id')->first()->tax_amount);
        $this->get('/operations/trips')->assertOk()->assertSee('Sales Tax')->assertSee('£');
        $this->get('/demo/operations/trips')->assertOk()->assertSee('VAT (8%)')->assertDontSee('Sales Tax');
    }

    public function test_settings_are_validated_and_admin_only(): void
    {
        $this->put('/settings/tenant', $this->settings())->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'User']));
        $this->put('/settings/tenant', $this->settings())->assertForbidden();
        $this->get('/settings/tenant')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->put('/settings/tenant', array_merge($this->settings(), ['tax_rate' => 101, 'currency_code' => 'BAD', 'company_name' => '']))
            ->assertSessionHasErrors(['tax_rate', 'currency_code', 'company_name']);
        $this->assertSame(0, TenantSetting::count());
    }
}
