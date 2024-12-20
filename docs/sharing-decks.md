<script setup>
import { IconRefresh } from "@/components/icons"
</script>

# Sharing Decks

![Deck sharing page](/img/share-deck.png)

[Deck owners and editors]() can invite other users to view or collaborate on their deck.

To start sharing, click `Share` on your deck's page.

::: info
Users with **view-only** permissions to a deck cannot invite others, or see the list of members. See [Deck Roles and Permissions](#what-exactly-can-an-owner-editor-and-viewer-do-with-my-deck) for details.
:::

On the Share page, you'll see two sections: Invite and Members

## Invite

![Deck Invite links](/img/share-page-invite-links.png)

Deck members can have three roles:

- **owner** – the deck creator, with full permissions
- **editor** - can edit the deck and invite others
- **viewer** - can view the deck, practice, and play

In the invite section, you'll see a `View` invite links and an `Edit` invite link.

Share the link with users you wish to invite. Once they click on them (and sign in), they'll join your deck.

::: warning
Be careful sharing the `edit` link – any user who clicks this link will have full edit permissions. If you send an email to a user, and they forward it to another, and so on, you may get a number of unexpected editors.
:::

::: tip
Accidentally shared a link? Need to stop members from joining? Click the <IconRefresh /> refresh icon next to the link to generate a new link.
:::

## Members List

![List of deck members on the share page](/img/share-deck-members-list.png)

Below the invites, are a list of all current deck members and their roles.

You can promote, demote, or remove users in this list.

## Deck Roles and Permissions

Here's a more granular list of what deck owners, editors, and viewers can do:

| Permission                                       | Owner | Editor | Viewer |
| :----------------------------------------------- | ----- | ------ | ------ |
| **DECK**                                         |       |        |        |
| Delete Deck                                      | x     | -      | -      |
| Change deck name/description                     | x     | x      | -      |
| Add/Remove/Edit Cards                            | x     | x      | -      |
| **REPORTING**                                    |       |        |        |
| view avg card score (all members)                | x     | -      | -      |
| member participation summary                     | x     | -      | -      |
| **SHARING**                                      |       |        |        |
| share deck with others                           | x     | x      | -      |
| view member list                                 | x     | x      | -      |
| promote/demote members<br>(up to the same level) | x     | x      | -      |
| remove members                                   | x     | x      | -      |
| reset share links                                | x     | x      | -      |
| **ACTIVITIES**                                   |       |        |        |
| Practice Deck                                    | x     | x      | x      |
| Quiz                                             | x     | x      | x      |
| Matching                                         | x     | x      | x      |

## Which role should I use?

It depends on your goal. Here are a few common scenarios.

#### Self-Study

You've created a deck as a self-study tool and want to share it with a friend to help them study, but don't want them to change anything.

- **Viewers** – friends, study group

They will see your new cards, but won't be able to edit. Their practice and play history will be separate from your own.

#### Class Study

You've create a deck to help your class practice and study.

- **Editors** – TA's or instructional support staff to help you create/edit cards, and manage invites.
- **Viewers** – Students

:::tip
Post your `View` invite link in your Canvas classroom, so that students can add themselves.
:::

#### Collaborative Study

Many hands make light work, and smartycards makes it easy to build a deck together, as a classroom activity or with a small study group.

- **Editors** – friends, study group, students
