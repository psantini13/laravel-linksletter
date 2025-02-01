<?php

use App\Models\Issue;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('users can not see other users', function () {
    $user = User::factory()->create();
    $issueUser = Issue::factory()->create([
        'subject' => 'User Issue',
        'user_id' => $user->id,
    ]);

    $anotherUser = User::factory()->create();
    $issueAnotherUser = Issue::factory()->create([
        'subject' => 'Another User Issue',
        'user_id' => $anotherUser->id,
    ]);

    actingAs($user);

    get(route('issues.index'))
        ->assertStatus(200)
        ->assertSeeText($issueUser->subject)
        ->assertDontSeeText($issueAnotherUser->subject);
});

test('user can not see other links when creating an issue', function () {
    $user = User::factory()->create();
    $linkUser = Link::factory()->create([
        'Title' => 'User Title',
        'user_id' => $user->id,
    ]);

    $anotherUser = User::factory()->create();
    $linkAnotherUser = Link::factory()
        ->count(3)
        ->create([
            'title' => 'Another User Title',
            'user_id' => $anotherUser->id,
        ]);

    actingAs($user);

    get(route('issues.create'))
        ->assertStatus(200)
        ->assertSeeText($linkUser->title)
        ->assertDontSeeText($linkAnotherUser->pluck('title')->toArray());
});
