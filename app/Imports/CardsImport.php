<?php

namespace App\Imports;

use App\Models\Card;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Str;

class CardsImport implements ToModel, WithHeadingRow
{
    protected $deckId;

    public function __construct($deckId)
    {
        $this->deckId = $deckId;
    }

    protected function createBlock(?string $content, string $type = 'text')
    {
        return [
            'id' => Str::uuid()->toString(),
            'type' => $type,
            'content' => $content ?? '',
            'meta' => null,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new Card([
            'deck_id' => $this->deckId,
            //create a single block for each side of the card
            'front' => [$this->createBlock($row['front'])],
            'back' => [$this->createBlock($row['back'])],
        ]);
    }

    public function rules(): array
    {
        return [
            'front' => 'required|string',
            'back' => 'required|string',
        ];
    }
}
