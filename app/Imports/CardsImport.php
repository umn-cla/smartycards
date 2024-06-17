<?php

namespace App\Imports;

use App\Models\Card;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CardsImport implements ToModel, WithHeadingRow
{
    protected $deckId;

    public function __construct($deckId)
    {
        $this->deckId = $deckId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new Card([
            'deck_id' => $this->deckId,
            'front' => [
                'type' => $row['front_type'],
                'content' => $row['front_content'],
                'meta' => isset($row['front_alt']) && $row['front_alt']
                ? ['alt' => $row['front_alt']]
                : null,
            ],
            'back' => [
                'type' => $row['back_type'],
                'content' => $row['back_content'],
                'meta' => isset($row['back_alt']) && $row['back_alt']
                ? ['alt' => $row['back_alt']]
                : null,
            ],
        ]);
    }

    public function rules(): array
    {
        return [
            'front_type' => 'required|string',
            'front_content' => 'required|string',
            'front_alt' => 'sometimes|nullable|string',

            'back_type' => 'required|string',
            'back_content' => 'required|string',
            'back_alt' => 'sometimes|nullable|string',
        ];
    }
}
