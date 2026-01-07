<?php

namespace App\Jobs;

use App\Models\LtiAssignmentScore;
use App\Services\Lti\LtiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmitLtiGrade implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of attempts
     */
    public int $tries = 3;

    /**
     * Timeout in seconds
     */
    public int $timeout = 60;

    /**
     * Delete the job if models no longer exist
     */
    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public LtiAssignmentScore $assignmentScore
    ) {}

    /**
     * Backoff delays: 1min, 1hr
     * Returns: [60, 3600] = [1min, 1hr]
     */
    public function backoff(): array
    {
        return [60, 3600];
    }

    /**
     * Execute the job
     */
    public function handle(LtiService $ltiService): void
    {
        Log::info('Attempting LTI grade submission', [
            'assignment_score_id' => $this->assignmentScore->id,
            'user_id' => $this->assignmentScore->user_id,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
        ]);

        try {
            // Submit grade to Canvas using database-stored configuration
            $response = $ltiService->submitGrade($this->assignmentScore);

            // Mark as successfully submitted
            $this->assignmentScore->update([
                'submission_success' => true,
                'submitted_at' => now(),
                'submission_error' => null,
            ]);

            Log::info('LTI grade submitted successfully', [
                'assignment_score_id' => $this->assignmentScore->id,
                'user_id' => $this->assignmentScore->user_id,
                'score' => "{$this->assignmentScore->score}/{$this->assignmentScore->score_maximum}",
                'attempts' => $this->attempts(),
            ]);
        } catch (\Exception $e) {
            Log::warning('LTI grade submission failed', [
                'assignment_score_id' => $this->assignmentScore->id,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'error' => $e->getMessage(),
                'will_retry' => $this->attempts() < $this->tries,
            ]);

            // Re-throw to trigger Laravel's retry mechanism
            throw $e;
        }
    }

    /**
     * Handle permanent job failure after all retries exhausted
     */
    public function failed(\Throwable $exception): void
    {
        $errorMessage = "LTI Grade Submit job failed after {$this->tries} attempts. Last error: {$exception->getMessage()}";

        Log::error($errorMessage, [
            'assignment_score_id' => $this->assignmentScore->id,
            'user_id' => $this->assignmentScore->user_id,
            'resource_link_id' => $this->assignmentScore->lti_resource_link_id,
        ]);

        // Update the assignment score to reflect permanent failure
        $this->assignmentScore->update([
            'submission_success' => false,
            'submission_error' => $errorMessage,
        ]);

        // This will be captured by Sentry and sent to Slack
        throw new \Exception($errorMessage, 0, $exception);
    }
}
