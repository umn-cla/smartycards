<?php

namespace App\Http\Controllers;

use App\Exceptions\QuizGenerationException;
use App\Library\OpenAIService\OpenAIService;
use App\Library\QuizMaker;
use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;

class DeckQuizController extends Controller
{
    /**
     * generate a quiz for a deck
     */
    public function quiz(Request $request, Deck $deck, OpenAIService $openAI)
    {
        Gate::authorize('view', $deck);

        $validated = $request->validate([
            'cardSide' => 'required|in:front,back',
            'numberOfQuestions' => 'required|integer|min:1|max:10',
        ]);

        $quizMaker = new QuizMaker($deck, $validated, $openAI);

        try {
            $quiz = $quizMaker->generateQuiz();
        } catch (QuizGenerationException $e) {
            // Track how often generation still fails (e.g. truncation), but
            // return a clean 422 instead of a 500. (SMARTYCARDS-11)
            report($e);

            return response()->json([
                'message' => "We couldn't generate a quiz right now. Please try again.",
            ], 422);
        }

        return response()->json($quiz);
    }
}
