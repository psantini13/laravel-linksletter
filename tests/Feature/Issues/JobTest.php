<?php

use App\Jobs\GenerateIssueHtmlJob;
use App\Models\Issue;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertStringContainsString;

uses(RefreshDatabase::class);

test('job generate correct HTML file', function () {
    $issue = Issue::factory()
        ->has(Link::factory()->count(2))
        ->create([
            'header_text' => 'Header Text',
            'footer_text' => 'Footer Text',
        ]);

    (new GenerateIssueHtmlJob($issue->id))->handle();

    $issue = $issue->fresh(['links']);

    assertStringContainsString('Header Text', $issue->links_html);
    assertStringContainsString('Footer Text', $issue->links_html);
    assertStringContainsString($issue->links[0]->title, $issue->links_html);
    assertStringContainsString($issue->links[1]->title, $issue->links_html);
    assertStringContainsString($issue->links[0]->url, $issue->links_html);
    assertStringContainsString($issue->links[1]->url, $issue->links_html);
});

test('job is called when issue is created', function () {
    Queue::fake();

    $user = User::factory()->create();

    actingAs($user);

    post(route('issues.store'), [
        'subject' => 'Test Issue',
    ])
        ->assertStatus(302)
        ->assertRedirect(route('issues.index'))
        ->assertSessionHas('message', 'Issue created successfully.');

    Queue::assertPushed(GenerateIssueHtmlJob::class);
});
