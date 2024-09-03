<?php

use App\Models\Deck;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function addArtHistoryExampleDeck()
    {
        // create art history example deck
        $artHistDeck = Deck::create([
            'name' => 'Art History',
            'description' => 'Example Deck',
            'is_public' => true,
        ]);

        // create cards for art history example deck
        $artHistDeck->cards()->createMany([
            [
                'front' => [
                    [
                        'id' => '5028f2c9-1ab8-4d6a-a45e-0dc44dbd2849',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/Van_Gogh_-_Starry_Night_-_Google_Art_Project.jpg/700px-Van_Gogh_-_Starry_Night_-_Google_Art_Project.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '63501157-13f8-4204-9a27-20361df77f12',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Starry Night</strong></p><p>Vincent van Gogh</p><p>1889</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => '58af662b-27b1-4d5e-8197-19048195fcbe',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Mona_Lisa%2C_by_Leonardo_da_Vinci%2C_from_C2RMF_retouched.jpg/540px-Mona_Lisa%2C_by_Leonardo_da_Vinci%2C_from_C2RMF_retouched.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '3a1266a9-e9dc-4347-b8bc-9dc41678ca94',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>Mona Lisa</strong></p><p>Leonardo da Vinci</p><p>c. 1503-1506</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => 'e6c83bdd-e67b-4c8b-9e10-317a97f5d38e',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/en/d/dd/The_Persistence_of_Memory.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '1fa0110c-73e8-4b2d-9121-6f2180a2d055',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Persistence of Memory</strong></p><p>Salvador Dalí</p><p>1931</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => 'b265b31c-3ec2-4620-9b3b-abe42efb132d',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Sandro_Botticelli_-_La_nascita_di_Venere_-_Google_Art_Project_-_edited.jpg/800px-Sandro_Botticelli_-_La_nascita_di_Venere_-_Google_Art_Project_-_edited.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '1feaf526-c2dc-4af5-9edd-d1be021f9139',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Birth of Venus</strong></p><p>Sandro Botticelli</p><p>c. 1484-1486</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => 'b8f01a01-fc91-4eb9-9af8-957e4b58da74',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Edvard_Munch%2C_1893%2C_The_Scream%2C_oil%2C_tempera_and_pastel_on_cardboard%2C_91_x_73_cm%2C_National_Gallery_of_Norway.jpg/440px-Edvard_Munch%2C_1893%2C_The_Scream%2C_oil%2C_tempera_and_pastel_on_cardboard%2C_91_x_73_cm%2C_National_Gallery_of_Norway.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '67d9b258-b92a-44aa-b5ee-fb6cc03cbba6',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Scream</strong></p><p>Edvard Munch</p><p>1893</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => 'cf66e2d0-8829-4f54-ba0c-1b61493eb2f3',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://static3.museoreinasofia.es/sites/default/files/obras/DE00050_0.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '87a1e9f6-de17-4c1c-872e-8f9a6d213dc4',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>Guernica</strong></p><p>Pablo Picasso</p><p>1937</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => '20a1d1f5-1ad7-41f2-a6e1-b67f6354a775',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/The_Night_Watch_-_HD.jpg/760px-The_Night_Watch_-_HD.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '131b775c-3de2-46ed-98f2-8d7a4d57a53e',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Night Watch</strong></p><p>Rembrandt van Rijn</p><p>1642</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => '5944cfde-e064-4e4f-bb97-bccda264f269',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Grant_Wood_-_American_Gothic_-_Google_Art_Project.jpg/600px-Grant_Wood_-_American_Gothic_-_Google_Art_Project.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '8cc61525-422a-4329-b24c-aeeb053aa4f0',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>American Gothic</strong></p><p>Grant Wood</p><p>1930</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => 'befa2597-4da6-4097-93ac-2250a00b4bd6',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/The_Arnolfini_portrait_%281434%29.jpg/540px-The_Arnolfini_portrait_%281434%29.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => '48c3c85f-40a7-4c35-82a7-2fb4daf8016c',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Arnolfini Portrait</strong></p><p>Jan van Eyck</p><p>1434</p>',
                    ],
                ],
            ],
            [
                'front' => [
                    [
                        'id' => 'bd88530b-8632-42fc-98a0-5ac8202013b1',
                        'meta' => [
                            'alt' => null,
                        ],
                        'type' => 'image',
                        'content' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/The_Kiss_-_Gustav_Klimt_-_Google_Cultural_Institute.jpg/600px-The_Kiss_-_Gustav_Klimt_-_Google_Cultural_Institute.jpg',
                    ],
                ],
                'back' => [
                    [
                        'id' => 'c664fa7d-7a6d-431b-bc5c-d731398d96e4',
                        'meta' => null,
                        'type' => 'text',
                        'content' => '<p><strong>The Kiss</strong></p><p>Gustav Klimt</p><p>1907-1908</p>',
                    ],
                ],
            ],
        ]);
    }

    public function addBeginningItalianExampleDeck()
    {
        $deck = Deck::create([
            'name' => 'Beginning Italian',
            'description' => 'Example Deck',
            'is_public' => true,
        ]);

        // create cards for beginning italian example deck
        $deck->cards()->createMany(
            [
                [
                    'front' => [
                        [
                            'id' => 'a7dfab38-3e07-466e-a777-77691e2e9e78',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Goodbye</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '38ff8604-4465-49fd-bd74-b8cab0162036',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Arrivederci</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '3a9dc515-e6f9-45cf-a034-cd70e289c2e2',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Hello</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '6abea3a7-294f-4161-9f0c-587afee7e0fb',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Ciao</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'fd9cd70a-04b5-47f7-a4dc-74ea1fd3668b',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Please</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '0b7e3df3-978b-4fb1-8046-02b025f7390a',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Per favore</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '3046d65c-4ac9-4d4f-999f-5976acbbf345',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Thank You</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'f99fe5f9-c02e-41de-b4d1-d9261f6ba312',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Grazie</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'cdf4885f-1e0f-4485-afa7-cf3ec86c6bc1',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Yes</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'ab43f68b-3a05-4cd0-9ba0-b894c577a3e9',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Si</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '10cd97e4-133c-4eec-b3a0-df8eee73d500',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>No</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '20ce11af-875f-459c-9c07-16a7e1bdb6bf',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>No</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '70e5fe64-aeea-4a2f-b3e1-490f695fdbc6',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Excuse me</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'fb3be634-d16e-446d-a704-60b36b0f92c5',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Scusi</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '42538d88-9ef0-4104-8075-9f273aef5c55',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Do you speak English?</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '30b7c178-a622-473e-b899-36ad3c1b63b9',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Parla inglese?</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '11ab5453-486e-47a9-8b6a-198ecc11e916',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>How much does it cost?</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'de8c7a00-367e-48dc-aa68-d5dd5bed3fae',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Quanto costa?</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'ab1acd0f-942c-4df3-a2e5-2e9296b125d7',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Where is the restroom?</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '3efc611e-8045-4ad8-93ac-83784bda7a1b',
                            'meta' => null,
                            'type' => 'text',
                            'content' => "<p>Dov'è la toilette?</p>",
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'dd098cc9-00bd-4e40-bbeb-686d9dbe9c0e',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Water</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '5943db29-a8a3-43a2-a61d-60e2901a6a37',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Acqua</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'd9bd5b8a-73d5-41e1-b2b8-b051a637ba06',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Food</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'f0c3c7ef-01ce-4fec-9438-2d0e4b53d3b8',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Cibo</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'd4b0b1d8-2e94-4b56-970b-cd380f09a439',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Help!</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '868057b0-6c19-4545-a95a-c008e99f3c4e',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Aiuto!</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '204398f9-663c-4697-861b-e88deeb4a821',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Where is...?</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '4ca1ce7f-20f2-40e0-a08b-940e24039ea9',
                            'meta' => null,
                            'type' => 'text',
                            'content' => "<p>Dov'è...?</p>",
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '72fdf16c-cb2d-4b89-a629-c86e134f68b4',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Train station</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'fdbbb155-59b2-46ef-baed-72423817596b',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Stazione ferroviaria</p>',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '1381ce5e-c488-45e8-97a4-d3b373164797',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Hotel</p>',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '240bbd76-c86f-49b4-af6e-a8e59c96a8a1',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Albergo</p>',
                        ],
                    ],
                ],
            ]
        );
    }

    public function addBackyardBirdsDeck()
    {
        $deck = Deck::create([
            'name' => 'Backyard Birds',
            'description' => 'Example Deck',
            'is_public' => true,
        ]);

        $deck->cards()->createMany(
            [
                [
                    'front' => [
                        [
                            'id' => 'ebdce9c7-f4fe-42f4-8be4-995b8b15a60a',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/550633.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'd0ce0357-97d6-4429-9924-4b51baf69675',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>American Goldfinch</p>',
                        ],
                        [
                            'id' => '24fdae29-b805-4f91-86c6-db44993998f4',
                            'meta' => [
                                'alt' => 'American Goldfinch',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/306710541-480px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '130b71bc-f7e4-4755-97bf-26d97f63f174',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/551661.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '250ed505-17c7-4ac4-a277-ebb086c91188',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Northern Cardinal</p>',
                        ],
                        [
                            'id' => 'c5280c58-1b4a-4e43-876b-1eddf89e27fc',
                            'meta' => [
                                'alt' => null,
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/63667311-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '4581fcf8-69f3-4f0a-85bd-e496e65190bf',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/549106.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'fa2758e8-65ac-4b4c-966e-aed157542333',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Bald Eagle</p>',
                        ],
                        [
                            'id' => '7c5c45a9-4fd8-4261-af93-dbcb475ec988',
                            'meta' => [
                                'alt' => 'Bald Eagle',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/60328971-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'f3d36365-be46-46f4-a6a6-0a21ee5a94a2',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/549204.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'e7c7a860-1ed0-47e8-81cb-95dadb7e6b29',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Barred Owl</p>',
                        ],
                        [
                            'id' => '7b1872d4-d211-4041-aab3-caae4a2c9d00',
                            'meta' => [
                                'alt' => 'Barred Owl',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/297388681-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'ed6d19da-2306-42ac-85a7-02234e57b3bc',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/550317.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'f9168aad-9728-4686-812d-ecfb7d66bd4b',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Northern Mockingbird</p>',
                        ],
                        [
                            'id' => '3cb82540-04a8-46ad-9a1e-63b9a56afa95',
                            'meta' => [
                                'alt' => 'Northern Mockingbird',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/63743751-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '6175bb0a-8862-4235-8318-c2d85bb47a0e',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/551140.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '7cc8c7ce-f54c-428c-922f-97ca93b2bf37',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Baltimore Oriole</p>',
                        ],
                        [
                            'id' => '4bec93b1-1195-4d1f-811c-06e004c01310',
                            'meta' => [
                                'alt' => 'Baltimore Oriole',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/306380871-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '11a64707-0887-42e5-81ea-7836edd73d5f',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/549365.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '1e7079cb-ce29-42f7-8b13-39b09c87c877',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Pileated Woodpecker</p>',
                        ],
                        [
                            'id' => '83c6b5e3-4e3e-4ea9-99c9-1a42cbb83809',
                            'meta' => [
                                'alt' => null,
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/617520228-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'f9004313-c311-46f9-bef0-40bdd08242d8',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/549875.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'ae51b0d6-0afe-430c-a3b0-e8fba0631151',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Black-capped Chickadee</p>',
                        ],
                        [
                            'id' => '5e3b2c13-de93-4ac0-88db-5f6f96b4c39e',
                            'meta' => [
                                'alt' => 'Black-capped Chickadee',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/302472691-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'fed7fdf1-89ab-4713-9822-d69532e8c002',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/548952.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'b15e36cf-2dee-4674-9753-9cede4ec5424',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Common Loon</p>',
                        ],
                        [
                            'id' => 'a1e69247-3e30-4471-bc65-e2499efd9a8a',
                            'meta' => [
                                'alt' => null,
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/308049951-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => '563c7007-4f5e-4d1a-bb27-29f0e64d3642',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/547969.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '8ccc28c8-b48e-4b15-bdf2-4289da75cf01',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Trumpeter Swan</p>',
                        ],
                        [
                            'id' => 'ee0ae7d8-3175-4517-a5b2-60c5f46fc9e7',
                            'meta' => [
                                'alt' => null,
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/295430011-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'b0af4006-19b3-4c96-8485-32791a8aecd5',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/551158.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => 'ac392f7d-7c3b-4f11-bc6d-80b4acc5d45c',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Red-winged Blackbird</p>',
                        ],
                        [
                            'id' => 'd4df76d4-76dc-4e1a-b716-ab0674453dc9',
                            'meta' => [
                                'alt' => 'Red-winged Blackbird',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/306392131-720px.jpg',
                        ],
                    ],
                ],
                [
                    'front' => [
                        [
                            'id' => 'd9085de4-c43e-469b-91bf-c972bf5f5d0f',
                            'meta' => null,
                            'type' => 'audio',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/sound/535882.mp3',
                        ],
                    ],
                    'back' => [
                        [
                            'id' => '7ee94aac-14bd-4528-bdbf-6db1eae63a7a',
                            'meta' => null,
                            'type' => 'text',
                            'content' => '<p>Ruby-throated Hummingbird</p>',
                        ],
                        [
                            'id' => '17aeee60-6212-4663-853a-88bc3e15253f',
                            'meta' => [
                                'alt' => 'Ruby-throated Hummingbird',
                            ],
                            'type' => 'image',
                            'content' => 'https://www.allaboutbirds.org/guide/assets/photo/303881521-720px.jpg',
                        ],
                    ],
                ],
            ]

        );
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addArtHistoryExampleDeck();
        $this->addBeginningItalianExampleDeck();
        $this->addBackyardBirdsDeck();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove decks
        Deck::where('name', 'Art History')
            ->where('description', 'Example Deck')
            ->where('is_public', true)
            ->delete();

        Deck::where('name', 'Beginning Italian')
            ->where('description', 'Example Deck')
            ->where('is_public', true)
            ->delete();

        Deck::where('name', 'Backyard Birds')
            ->where('description', 'Example Deck')
            ->where('is_public', true)
            ->delete();

    }
};
