<?php

namespace App\Jobs;

use App\Models\Issue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateIssueHtmlJob implements ShouldQueue
{
    use Queueable;

    private Issue $issue;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $issueId)
    {
        $this->issue = Issue::with('links')->findOrFail($this->issueId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $html = view('issues.components.issueHtml', [
            'issue' => $this->issue,
        ]);

        $this->issue->links_html = $html->render();
        $this->issue->save();
    }
}
