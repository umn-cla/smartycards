<script setup>
import GIFPlayer from '@/components/GIFPlayer.vue';
import matchingThumb from "../img/play-matching-game_thumb.png";
import matchingGif from '../img/play-matching-game.gif'
</script>

# Matching Game

<GIFPlayer
  :thumbSrc="matchingThumb"
  :src="matchingGif"
  alt="Matching game where users see a 4x4 grid of card sides: 8 famous paintings from art history, and 8 matching descriptions with authors, titles, and dates."
  class="my-4"
/>

Play a matching game with your flashcards. SmartyCards will select cards at random, and you'll match the front side with the back side.
