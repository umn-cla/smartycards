/// <reference types="cypress" />
// ***********************************************
// This example commands.ts shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })
//
// declare global {
//   namespace Cypress {
//     interface Chainable {
//       login(email: string, password: string): Chainable<void>
//       drag(subject: string, options?: Partial<TypeOptions>): Chainable<Element>
//       dismiss(subject: string, options?: Partial<TypeOptions>): Chainable<Element>
//       visit(originalFn: CommandOriginalFn, url: string, options: Partial<VisitOptions>): Chainable<Element>
//     }
//   }
// }

import * as T from "../../../resources/client/types";

Cypress.Commands.add("getUserByUsername", (umndid: string) => {
  return cy.php(
    `App\\Models\\User::where('umndid', '${umndid}')->firstOrFail()`,
  );
});

Cypress.Commands.add("getUser", (userId: number) => {
  return cy.php(`App\\Models\\User::findOrFail(${userId});`);
});

Cypress.Commands.add(
  "createDeckForUser",
  (
    umndid: string,
    { name, description = "" }: { name: string; description: string },
  ) => {
    return cy.php(`
      $user = \\App\\Models\\User::where("umndid", "${umndid}")->first();
      $deck = \\App\\Models\\Deck::factory()->create([
        'name' => '${name}',
        'description' => '${description}',
        ]);
      $user->decks()->attach($deck, ["role" => "${T.MembershipRole.OWNER}"]);

      return $deck;
    `);
  },
);
