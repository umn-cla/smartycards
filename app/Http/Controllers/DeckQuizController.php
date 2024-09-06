<?php

namespace App\Http\Controllers;

use App\Library\QuizMaker;
use App\Models\Deck;
use Gate;
use Illuminate\Http\Request;

class DeckQuizController extends Controller
{
    /**
     * generate a quiz for a deck
     */
    public function quiz(Request $request, Deck $deck)
    {
        Gate::authorize('view', $deck);

        $validated = $request->validate([
            'cardSide' => 'required|in:front,back',
            'numberOfQuestions' => 'required|integer|min:1|max:10',
        ]);

        $quizMaker = new QuizMaker($deck, $validated);
        $quiz = $quizMaker->generateQuiz();

        return response()->json($quiz);
    }
}
