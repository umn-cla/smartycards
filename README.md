# SmartyCards

> Flashcards, but smarter

## Introduction

SmartyCards is a free, collaborative platform for studying with digital flashcards, quizzes, and games. Any student, faculty, or staff at any University of Minesota campus can use SmartyCards.

- Sign in: [smartycards.cla.umn.edu](https://smartycards.cla.umn.edu)
- Learn more: [umn-cla.github.io/smartycards](https://umn-cla.github.io/smartycards)

## Features

- Multiple media types: text, image, audio, video, math (LaTeX), embeds
- Easy collaboration. Make and share decks with friends and colleagues.
- Hints: add mnemonics to cards to help remember
- Practice with spaced repetition: learn new content more quickly
- Play matching games
- AI generated practice quizzes
- Web based – no app to download
- Free!
- Made by UMN College of Liberal Arts, by the people who brought you ChimeIn, Z, and Elevator.

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

We use [deployer](https://deployer.org/) to deploy to an environment:

```sh
dep deploy <environment> --branch <branchname>
```

| environment | host                                                                       |
| ----------- | -------------------------------------------------------------------------- |
| dev         | [cla-smartycards-dev.oit.umn.edu](https://cla-smartycards-dev.oit.umn.edu) |
| stage       | [cla-smartycards-tst.oit.umn.edu](https://cla-smartycards-tst.oit.umn.edu) |
| prod        | [cla-smartcards-prd.oit.umn.edu](https://cla-smartcards-prd.oit.umn.edu)   |

Server configuration is managed via [UMN CLA's Ansible Playbook](https://github.com/umn-cla/ansible-rhel9).

## Documentation

Docs use [vitepress](https://vitepress.dev/) and are found in [./docs](./docs/).

```sh
# develop docs locally
npm run docs:dev

# deploy docs
npm run docs:deploy
```
