<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// ─────────────────────────────────────────────────────────────────────────────
// Helper
// ─────────────────────────────────────────────────────────────────────────────

function makeAdmin(array $overrides = []): User
{
    return User::factory()->create(array_merge([
        'email'    => 'admin@dost-sdn.gov.ph',
        'password' => Hash::make('Admin@2026!'),
    ], $overrides));
}

// ─────────────────────────────────────────────────────────────────────────────
// Login page
// ─────────────────────────────────────────────────────────────────────────────

describe('login page', function () {

    it('loads for guests', function () {
        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Admin Login');
    });

    it('redirects authenticated users away from login page', function () {
        $this->actingAs(makeAdmin())
            ->get(route('login'))
            ->assertRedirect(route('admin.dashboard'));
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Login attempts
// ─────────────────────────────────────────────────────────────────────────────

describe('login attempts', function () {

    it('logs in and redirects to dashboard with correct credentials', function () {
        $user = makeAdmin();

        $this->post(route('login'), [
            'email'    => 'admin@dost-sdn.gov.ph',
            'password' => 'Admin@2026!',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    });

    it('returns validation error with wrong password', function () {
        makeAdmin();

        $this->post(route('login'), [
            'email'    => 'admin@dost-sdn.gov.ph',
            'password' => 'WrongPassword',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('returns validation error for non-existent email', function () {
        $this->post(route('login'), [
            'email'    => 'nobody@nowhere.com',
            'password' => 'anypassword',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    });

    it('fails when email field is absent', function () {
        $this->post(route('login'), ['password' => 'Admin@2026!'])
            ->assertSessionHasErrors('email');
    });

    it('fails when password field is absent', function () {
        $this->post(route('login'), ['email' => 'admin@dost-sdn.gov.ph'])
            ->assertSessionHasErrors('password');
    });

    it('fails when email has an invalid format', function () {
        $this->post(route('login'), [
            'email'    => 'not-a-valid-email',
            'password' => 'Admin@2026!',
        ])->assertSessionHasErrors('email');
    });

    it('regenerates the session ID after successful login', function () {
        makeAdmin();
        $sessionBefore = session()->getId();

        $this->post(route('login'), [
            'email'    => 'admin@dost-sdn.gov.ph',
            'password' => 'Admin@2026!',
        ]);

        expect(session()->getId())->not->toBe($sessionBefore);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Logout
// ─────────────────────────────────────────────────────────────────────────────

describe('logout', function () {

    it('clears session and redirects to login page', function () {
        $user = makeAdmin();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    });

    it('is not accessible via GET (405 method not allowed)', function () {
        makeAdmin();
        $this->get('/logout')->assertStatus(405);
    });

    it('redirects guests who attempt logout to login', function () {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Middleware protection
// ─────────────────────────────────────────────────────────────────────────────

describe('middleware protection', function () {

    it('redirects unauthenticated users to login', function (string $method, string $routeName, array $params) {
        $this->{$method}(route($routeName, $params))
            ->assertRedirect(route('login'));
    })->with([
        'dashboard'  => ['get',    'admin.dashboard', []],
        'print view' => ['get',    'admin.logs.print', []],
        'csv export' => ['get',    'admin.export.csv', []],
    ]);

    it('allows authenticated admin to reach the dashboard', function () {
        $this->actingAs(makeAdmin())
            ->get(route('admin.dashboard'))
            ->assertOk();
    });
});
