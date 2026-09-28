const deckName = "Deck 1";
const deckDescription = "About deck 1";

const pages: { pathTemplate: string; textShownWhenLoaded: string | null }[] = [
  { pathTemplate: "/", textShownWhenLoaded: null },
  { pathTemplate: "/decks", textShownWhenLoaded: deckName },
  { pathTemplate: "/decks/:deckId", textShownWhenLoaded: deckName },
  { pathTemplate: "/decks/:deckId/share", textShownWhenLoaded: deckName },
  { pathTemplate: "/decks/:deckId/import", textShownWhenLoaded: deckName },
  { pathTemplate: "/decks/:deckId/clone", textShownWhenLoaded: deckName },
  {
    pathTemplate: "/decks/:deckId/cards/create",
    textShownWhenLoaded: deckName,
  },
  { pathTemplate: "/community/decks", textShownWhenLoaded: deckName },
  { pathTemplate: "/community/decks/:deckId", textShownWhenLoaded: deckName },
  { pathTemplate: "/profile", textShownWhenLoaded: null },
  {
    pathTemplate: "/decks/:deckId/activities/practice/embed",
    textShownWhenLoaded: deckName,
  },
  { pathTemplate: "/no-such-page", textShownWhenLoaded: null },
];

const allHeadings = "h1, h2, h3, h4, h5, h6";

function toHeadingLevel(heading: HTMLElement): number {
  return Number(heading.tagName.slice(1));
}

function expectNoSkippedHeadingLevels(levels: number[]): void {
  levels.forEach((level, index) => {
    const previousLevel = levels[index - 1] ?? 0;
    expect(level, `level of heading ${index + 1}`).to.be.at.most(
      previousLevel + 1,
    );
  });
}

describe("Page landmarks and headings", () => {
  let deckId: number;

  before(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });
    cy.createDeckForUser("user", {
      name: deckName,
      description: deckDescription,
    }).then((deck) => {
      deckId = deck.id;
    });
    cy.php(`\\App\\Models\\Deck::query()->update(['is_public' => true]);`);
  });

  beforeEach(() => {
    cy.login({ umndid: "user" });
  });

  pages.forEach(({ pathTemplate, textShownWhenLoaded }) => {
    it(`has one main with one h1, no skipped heading levels, and no deck description as a heading: ${pathTemplate}`, () => {
      cy.visit(pathTemplate.replace(":deckId", String(deckId)));

      if (textShownWhenLoaded) {
        cy.contains("main", textShownWhenLoaded);
      }

      cy.get("main h1").should("have.length", 1);
      cy.get("main").should("have.length", 1);

      cy.get("main")
        .find(allHeadings)
        .then(($headings) => {
          const levels = $headings.toArray().map(toHeadingLevel);
          expectNoSkippedHeadingLevels(levels);
        });

      cy.get("main").find(allHeadings).should("not.contain", deckDescription);
    });
  });
});
