<?php

use App\Enums\UserRole;
use App\Livewire\Actions\UserApproval;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login from the pending approval page', function () {
    $this->get(route('approval.pending'))->assertRedirect(route('login'));
});

test('unapproved users are redirected from admin pages to the pending approval page', function () {
    $user = User::factory()->unapproved()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('approval.pending'));
});

test('unapproved users can see the pending approval page', function () {
    $user = User::factory()->unapproved()->create();

    $this->actingAs($user)
        ->get(route('approval.pending'))
        ->assertOk()
        ->assertSee('Menunggu Persetujuan');
});

test('approved users are redirected away from the pending approval page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('approval.pending'))
        ->assertRedirect(route('dashboard'));
});

test('approved users can visit the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('newly registered users are not approved', function () {
    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'baru@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'baru@example.com')->firstOrFail();

    expect($user->isApproved())->toBeFalse()
        ->and($user->role)->toBe(UserRole::Peserta);
});

test('non admins cannot access the user approval page', function () {
    $user = User::factory()->peserta()->create();

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('approved peserta can visit the dashboard but not content pages', function () {
    $user = User::factory()->peserta()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
    $this->actingAs($user)->get(route('posts.index'))->assertForbidden();
    $this->actingAs($user)->get(route('pelatihan.index'))->assertForbidden();
    $this->actingAs($user)->get(route('faqs.index'))->assertForbidden();
});

test('admins can access the user approval page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk();
});

test('admins can approve an unapproved user', function () {
    $this->actingAs(User::factory()->admin()->create());
    $pending = User::factory()->unapproved()->create();

    Livewire::test(UserApproval::class)
        ->call('approve', $pending->id);

    expect($pending->fresh()->isApproved())->toBeTrue();
});

test('admins can reject an unapproved user', function () {
    $this->actingAs(User::factory()->admin()->create());
    $pending = User::factory()->unapproved()->create();

    Livewire::test(UserApproval::class)
        ->call('confirmDelete', $pending->id)
        ->call('delete');

    expect(User::find($pending->id))->toBeNull();
});

test('approved users cannot be rejected from the approval page', function () {
    $this->actingAs(User::factory()->admin()->create());
    $approved = User::factory()->create();

    Livewire::test(UserApproval::class)
        ->call('confirmDelete', $approved->id)
        ->call('delete');

    expect(User::find($approved->id))->not->toBeNull();
});

test('role cannot be mass assigned', function () {
    $user = User::factory()->peserta()->create();
    $user->update(['name' => 'Baru', 'role' => 'admin']);

    expect($user->fresh()->role)->toBe(UserRole::Peserta);
});
