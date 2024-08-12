# SmartyCards

> Flashcards, but smarter

## Features

- [x] **Spaced Repetition.** See harder cards more frequently for faster learning.
- [x] **Collaborative.** Build flash card decks together, and share with friends.
- [x] **Multimedia.** Add text, images, audio, video, links.
- [x] **Hints.** Add hints to cards with Reveal blocks.
- [ ] **Quizzes and Games.** Use AI to generate quizzes or matching games from your deck of flashcards.
- [ ] **Multimodal.** Create cards with more than 2 "sides" to connect audio, visual, textual information.
- [ ] **Offline.** Study your flashcard deck anywhere – even without a network connection.
- [ ] **Import.** Import from other popular formats like Spreadsheets, Anki, Quizlet, and ChimeIn.
- [ ] **Stats.** See a summary of your deck stats, and how you've improved over time.
- [ ] **Badges and Streaks.** Earn in-app rewards for practicing.

## Getting Started with Local Development

Prereqs:

- PHP
- Docker
- NodeJS

```sh
# Create a .env file
cp .env.example .env

# add credentials to env
code .env

# copy auth.json.example to auth.json
# this is needed for installing Laravel Nova
cp auth.json.example auth.json

# add Laravel Nova license info
code auth.json

# Install php dependencies
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# Build
sail build

# Start Sail
sail up

# migrate the database
sail artisan migrate:fresh --seed

# Generate Key
sail artisan key:generate

# Directory Permissions
sail artisan storage:link
sail artisan config:clear

# install npm dependencies
npm install

## start vite
npm run dev

```

## Deploying

| environment | host                                                                       |
| ----------- | -------------------------------------------------------------------------- |
| dev         | [cla-smartycards-dev.oit.umn.edu](https://cla-smartycards-dev.oit.umn.edu) |
| prod        | [cla-smartcards-prd.oit.umn.edu](https://cla-smartcards-prd.oit.umn.edu)   |
