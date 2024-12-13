<?php

namespace App\Exports;

use App\Models\Card;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Style;

class CardsExport implements FromCollection, WithColumnWidths, WithDefaultStyles, WithHeadings, WithMapping, WithStyles
{
    protected $deckId;

    public function __construct($deckId)
    {
        $this->deckId = $deckId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Card::where('deck_id', $this->deckId)->get();
    }

    public function headings(): array
    {
        return [
            'deckId', // A
            'cardId', // B
            'side',  // C
            'blockId', // D
            'blockType', // E
            'content', // F
            'meta.alt', // G
            'meta.lang', // H
            'updatedAt', // I
        ];
    }

    protected function mapCardBlock(Card $card, string $sideName, array $cardBlock): array
    {
        $blockObj = (object) $cardBlock;
        $meta = (object) $blockObj?->meta ?? null;

        return [
            $card->deck_id,
            $card->id,
            $sideName,
            $blockObj->id,
            $blockObj->type,
            $blockObj->content,
            $meta?->alt ?? null,
            $meta?->lang ?? null,
            $card->updated_at,
        ];
    }

    public function map($card): array
    {
        $rows = [];

        foreach ($card->front as $cardBlock) {
            $rows[] = $this->mapCardBlock($card, 'front', $cardBlock);
        }

        foreach ($card->back as $cardBlock) {
            $rows[] = $this->mapCardBlock($card, 'back', $cardBlock);
        }

        return $rows;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'F' => [
                'alignment' => [
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 50,
            'G' => 50,
            'H' => 10,
            'I' => 20,
        ];
    }
}
