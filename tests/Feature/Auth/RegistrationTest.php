<?php

namespace Tests\Feature\Auth;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyFeature(Features::registration());
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_links_existing_email_based_expenses()
    {
        Expense::factory()->create([
            'group_id' => null,
            'paid_by' => null,
            'payer_email' => 'test-link@example.com',
        ]);

        ExpenseSplit::factory()->create([
            'user_id' => null,
            'member_email' => 'test-link@example.com',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Linked User',
            'email' => 'test-link@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $this->assertDatabaseHas('expenses', [
            'payer_email' => null,
            'paid_by' => $user->id,
        ]);

        $this->assertDatabaseHas('expense_splits', [
            'member_email' => 'test-link@example.com',
            'user_id' => $user->id,
        ]);
    }
}
