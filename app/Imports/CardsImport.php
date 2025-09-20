<?php

namespace App\Imports;

use App\Models\Card;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Str;

class CardsImport implements SkipsEmptyRows, ToModel
{
    protected $deckId;
    protected $currentRow = 0;
    protected $frontColumnIndex = 0;
    protected $backColumnIndex = 1;

    const FRONT_HEADER = 'front';
    const BACK_HEADER = 'back';

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

    protected function isFirstRow(): bool
    {
        return $this->currentRow === 1;
    }

    protected function rowContainsHeaders($row): bool
    {
        return $row[0] === self::FRONT_HEADER && $row[1] === self::BACK_HEADER || $row[0] === self::BACK_HEADER && $row[1] === self::FRONT_HEADER;
    }

    protected function setupFrontBackColumnIndices($row): void
    {
        // swap column indices if headers are [back, front]
        if ($row[0] === self::BACK_HEADER && $row[1] === self::FRONT_HEADER) {
            $this->frontColumnIndex = 1;
            $this->backColumnIndex = 0;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->currentRow++;

        if ($this->isFirstRow() && $this->rowContainsHeaders($row)) {
            // swap front and back indices if needed
            $this->setupFrontBackColumnIndices($row);
            // don't import the header row as card
            return;
        }

        $front = trim($row[$this->frontColumnIndex]);
        $back = trim($row[$this->backColumnIndex]);

        return new Card([
            'deck_id' => $this->deckId,
            //create a single block for each side of the card
            'front' => [$this->createBlock($front)],
            'back' => [$this->createBlock($back)],
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string',
            '1' => 'required|string',
        ];
    }
}
