<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('validate issues subject', function () {
    $user = User::factory()->create();

    actingAs($user);

    post(route('issues.store'), [
        'subject' => '',
        'header_text' => 'This is a test issue',
        'footer_text' => 'This is a test issue footer',
    ])
        ->assertStatus(302)
        ->assertSessionHasErrors([
            'subject' => 'The subject field is required.',
        ]);
});
