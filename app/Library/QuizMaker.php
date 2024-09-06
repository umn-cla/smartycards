<?php

namespace App\Library;

use App\Library\OpenAIService\OpenAIService;
use App\Models\Deck;

class QuizMaker
{
    private Deck $deck;

    private array $options;

    private array $defaultOptions = [
        'cardSide' => 'front',
        'numberOfQuestions' => 10,
        'challenge_level' => 'undergrad',
    ];

    private $openAI;

    public function __construct(Deck $deck, ?array $options = [])
    {
        $this->deck = $deck;
        $this->options = array_merge($this->defaultOptions, $options);
        $this->openAI = new OpenAIService;
    }

    public function getSystemText()
    {
        $challengeLevel = $this->options['challenge_level'];
        $systemText =
"You generate multiple choice quizzes from a set of json flash card data at the {$challengeLevel} level. You can include some distractors that are part of the data set, but also include good distractors that are not part of the flash card data set. Your response should use the following formats and respond in JSON. Both the prompt and choices should be plain text only with no markup.".

"```ts
interface Question {
  prompt: string; // the question the user will be asked
  choices: string[];
  correctChoiceIndex: number;
}

interface Quiz {
   difficulty: 'hs' | 'undergrad' | 'grad';
   questions: Question[];
}
```
".
"Be sure you're only returning valid minimized JSON with no additional markup or markdown, like wrapping with '```json' nor any `\n` characters.";

        return $systemText;
    }

    /**
     * converts a card side to a string by only
     * considering text content blocks, and then joining them
     */
    private function normalizeCardSide($cardSide)
    {
        // a card side is just an array of content blocks
        $contentBlocks = $cardSide;

        return collect($contentBlocks)
            ->filter(fn ($block) => $block['type'] === 'text')
            ->map(fn ($block) => $block['content'])
            ->join('');
    }

    private function normalizeCard($card)
    {
        return [
            'front' => $this->normalizeCardSide($card->front),
            'back' => $this->normalizeCardSide($card->back),
        ];
    }

    public function getNormalizedCards()
    {
        $numberOfQuestions = min($this->options['numberOfQuestions'], $this->deck->cards()->count());

        return $this->deck
            ->cards()
            ->inRandomOrder()
            ->limit($numberOfQuestions)
            ->get()
            ->map(fn ($card) => $this->normalizeCard($card));
    }


    public function getPrompt($level = 'easy')
    {
        $stringifiedCards = $this->getNormalizedCards()->toJson();
        $numberOfQuestions = $this->options['numberOfQuestions'];
        $cardSide = $this->options['cardSide'];

        $prompts = [
            'easy' => "Generate a quiz of {$numberOfQuestions} questions from the following flash cards. Use the {$cardSide} side of the card as the basis for a question prompt (but you may need to give more context for the question to make sense) and the other side as the correct answer.",

            'medium' =>  "Generate a quiz of {$numberOfQuestions} questions from the following flash cards, testing both front to back and back to front knowledge. The questions should be at a higher level of Bloom's Taxonomy, requiring application, analysis, or synthesis of multiple cards to answer.",
        ];

        $prompt = $prompts[$level];

        return "{$prompt} {$stringifiedCards}";
    }

    public function generateQuiz()
    {
        try {
            $response = $this->openAI->request($this->getPrompt(), $this->getSystemText());

            $quiz = json_decode($response, true);

            return $quiz;
        } catch (\Exception $e) {
            throw new \Exception("Failed to generate quiz: {$e->getMessage()}");
        }
    }
}
