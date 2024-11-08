<script setup>
import { IconDragHandle } from '@/components/icons';
import GIFPlayer from '@/components/GIFPlayer.vue';
import createDeckThumb from './img/create-deck_thumb.png';
import createDeckGif from './img/create-deck.gif';
import createCardThumb from './img/create-card_thumb.png'
import createCardGif from './img/create-card.gif';

</script>

# Quick Start

## 1. Sign in

Go to: <https://smartycards.cla.umn.edu> to sign in.

## 2. Create a deck

<GIFPlayer
  :thumbSrc="createDeckThumb"
  :src="createDeckGif"
  alt="Creating a new deck."
/>

Creating a deck of cards is easy.

1. Click `Create Deck`.
2. Enter your deck name and description.
3. Tap the deck's &rarr; to see the deck contents.
4. Add cards by clicking the `Create Card` button.

## 3. Create a card in your deck

<GIFPlayer
  :thumbSrc="createCardThumb"
  :src="createCardGif"
  alt="This animated GIF shows the process of creating a new digital flashcard. The user enters text, formats it, adds an audio file, and uploads an image to complete the card. They then save the card and view it in the deck."
/>

Create a card by clicking "create card". On the card edit screen, you can add one or more blocks of media to each side of the card:

- text
- image
- audio
- video
- math (LaTeX)
- embeds (iframes)

::: tip
Drag the <span class="bg-black/10 inline-block rounded-sm -mb-0.5" ><IconDragHandle ckass="inline-block" /></span> block handle icon to move blocks around.
:::

::: tip
<span class="bg-black/5 italic">Highlight text</span> to add formatting or links.
:::

## 4. Join a community deck (recommended)

It's probably easiest to explore SmartyCards with a pre-created deck. You can find a few example decks under `Menu > Community Decks` . Click `Join`.

![Join a community deck. Step 1: select "Community" from the menu. Step 2: Click "Join" on a a community deck.](/img/join-community-decks.png)

After joining, you should see the deck listed under your `Shared Decks`.

![Deck appears in the sidebar and on the Decks page under "Shared Decks"](/img/shared-decks.png)

## 5. Practice and play

![On the smartycards deck page, there are three buttons for the types of activities: Practice, Quiz, and Matching](/img/deck-activities.png)

SmartyCards has 3 activities to help you study:

- [Practice](/activities/practice-flashcards)
- [Quiz](/activities/quiz)
- [Matching Game](/activities/matching-game)

::: info
Have an idea for a new activity? Share it: [latistecharch@umn.edu](mailto:latistecharch@umn.edu)
:::
