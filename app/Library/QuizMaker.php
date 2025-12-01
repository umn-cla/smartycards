<?php

namespace App\Library;

use App\Library\OpenAIService\OpenAIService;
use App\Models\Deck;
use Illuminate\Support\Collection;

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
            "You generate high quality multiple choice quizzes from a set of json flash card data at the {$challengeLevel} level. Include challenging distractors that are not part of the flash card data set. Never include the answer or distractor in the question prompt. The prompt and choices should be in proper markdown. LaTeX math expressions should be wrapped with $ or $$. For example, output `\\frac{1}{2}` as `$ \\frac{1}{2} $`. In your data, we've removed media content like images and replaced with placeholder data like `[Image: description]`. However, when displayed, the user will see the media with the prompt. If the data does not include a meaningful media description, do your best to use contextual clues from the flash card deck name, description, and other flash cards. Infer the difficulty of the quiz from the data, and match distractors to the inferred difficulty.";

        return $systemText;
    }

    public function getResponseSchema()
    {
        return [
            'name' => 'quiz',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'difficulty' => ['type' => 'string'],
                    'questions' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'sourceCardId' => ['type' => 'number'],
                                'prompt' => ['type' => 'string'],
                                'choices' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'string'],
                                    'additionalProperties' => false,
                                ],
                                'correctChoiceIndex' => ['type' => 'number'],
                            ],
                            'required' => ['sourceCardId', 'prompt', 'choices', 'correctChoiceIndex'],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
                'required' => ['difficulty', 'questions'],
                'additionalProperties' => false,
            ],
        ];
    }

    private function normalizeTextBlock(string $text)
    {
        // remove any base64 images that may be embedded in the text
        $text = preg_replace('/!\[.*?\]\(data:image\/.*?;base64,.*?\)/s', '[Image]', $text);

        // strip any remaining HTML tags
        $text = strip_tags($text);

        return trim($text);
    }

    /**
     * converts a card side to a string by only
     * considering text content blocks, and then joining them
     *
     * @return string
     */
    private function normalizeCardSide(array $cardSide)
    {
        // a card side is just an array of content blocks
        $contentBlocks = $cardSide;

        return collect($contentBlocks)
            ->map(function ($block) {
                switch ($block['type']) {
                    case 'math':
                        return $block['content'];
                    case 'text':
                        return $this->normalizeTextBlock($block['content']);
                    case 'image':
                        $alt = $block['meta']['alt'] ?? 'Unknown';
                        return "[Image: {$alt}]";
                    default:
                        return "[{$block['type']}]";
                }
            })
            ->join(' ');
    }

    /**
     * Normalizes a card by converting its sides to strings.
     *
     * @return array
     */
    private function normalizeCard(object $card)
    {
        return [
            'cardId' => $card->id,
            'front' => $this->normalizeCardSide($card->front),
            'back' => $this->normalizeCardSide($card->back),
        ];
    }

    /**
     * Retrieves and normalizes a limited number of cards from the deck.
     */
    public function getNormalizedCards(): Collection
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
            'easy' => "Generate a quiz of {$numberOfQuestions} questions from the following flash cards. Use the {$cardSide} side of the card as the basis for a question prompt. Include only the required information in the prompt. If the question has mathematical content, the question should require the user to apply the mathematical concept to solve a problem and not use the exact same numbers.",
            'medium' => "Generate a quiz of {$numberOfQuestions} questions from the following flash cards, testing both front to back and back to front knowledge. The questions should be at a higher level of Bloom's Taxonomy, requiring application, analysis, or synthesis of multiple cards to answer.",
        ];

        $prompt = $prompts[$level];

        return "{$prompt} {$stringifiedCards}";
    }

    public function shuffleQuestionChoices(array $question)
    {
        $choices = $question['choices'];
        $correctChoiceIndex = $question['correctChoiceIndex'];
        $correctChoice = $choices[$correctChoiceIndex];

        // Remove the correct choice and shuffle the remaining choices
        $shuffledChoices = collect($choices)
            ->except($correctChoiceIndex)
            ->shuffle()
            ->values()
            ->toArray();

        // Insert the correct choice at a random position
        $randomIndex = rand(0, count($shuffledChoices));
        array_splice($shuffledChoices, $randomIndex, 0, $correctChoice);

        // Return the new question with updated choices and correct index
        return [
            ...$question,
            'choices' => $shuffledChoices,
            'correctChoiceIndex' => $randomIndex,
        ];
    }

    public function randomizeQuiz($quiz)
    {
        $questions = $quiz['questions'];
        $randomizedQuestions = array_map([$this, 'shuffleQuestionChoices'], $questions);

        return [
            ...$quiz,
            'questions' => $randomizedQuestions,
        ];
    }

    public function generateQuiz()
    {
        $response = $this->openAI->request(
            prompt: $this->getPrompt(),
            systemText: $this->getSystemText(),
            responseSchema: $this->getResponseSchema()
        );

        $quiz = json_decode($response, true);

        $sourceCardIds = collect($quiz['questions'])->map(fn ($question) => $question['sourceCardId']);

        // get cards the quiz questions are based on
        $cards = $this->deck->cards()->whereIn('id', $sourceCardIds)->get();

        // create a card lookup
        $cardLookup = $cards->keyBy('id');

        // add the card data to the quiz
        foreach ($quiz['questions'] as &$question) {
            $card = $cardLookup[$question['sourceCardId']];

            $question['sourceCard'] = $card;
            $question['sourceCardSide'] = $this->options['cardSide'];
        }

        return $this->randomizeQuiz($quiz);
    }
}
