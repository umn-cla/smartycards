<?php

namespace App\Jobs;

use App\Models\LtiGradeSubmission;
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
        public LtiGradeSubmission $submission
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
            'submission_id' => $this->submission->id,
            'user_id' => $this->submission->user_id,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
        ]);

        try {
            // Submit using stored submission data (doesn't rely on cached launch)
            $response = $ltiService->submitGradeFromSubmission($this->submission);

            // Mark as successful
            $this->submission->update([
                'success' => true,
                'error_message' => null,
                'response_data' => [
                    'status' => 'success',
                    'submitted_at' => now()->toIso8601String(),
                    'response' => $response ?? null,
                ],
            ]);

            Log::info('LTI grade submitted successfully', [
                'submission_id' => $this->submission->id,
                'user_id' => $this->submission->user_id,
                'score' => "{$this->submission->score_given}/{$this->submission->score_maximum}",
                'attempts' => $this->attempts(),
            ]);
        } catch (\Exception $e) {
            Log::warning('LTI grade submission failed', [
                'submission_id' => $this->submission->id,
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
            'submission_id' => $this->submission->id,
            'user_id' => $this->submission->user_id,
            'resource_link_id' => $this->submission->lti_resource_link_id,
        ]);

        // Update the submission to reflect permanent failure
        $this->submission->update([
            'success' => false,
            'error_message' => $errorMessage,
        ]);

        // This will be captured by Sentry and sent to Slack
        throw new \Exception($errorMessage, 0, $exception);
    }
}
