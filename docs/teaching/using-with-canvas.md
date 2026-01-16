# Using with Canvas (LTI Integration)

SmartyCards can be integrated with Canvas using LTI (Learning Tools Interoperability). This allows you to create assignments that link directly to SmartyCards decks, with automatic grade passback to the Canvas gradebook.

## Setting Up an Assignment in Canvas


1. In Canvas, create a new **Assignment**. Add your assignment details (name, description, points, etc.).

2. For **Submission Type**, select **External Tool**
![Select External Tool as submission type](../img/lti-1_external-tool.png)

3. Click **Find** to see a list of available tools:
![Select SmartyCards from the list of external tool
s](../img/lti-2_choose-find.png)

4. Choose **SmartyCards** from the list:
![Select SmartyCards from the list of external tools](../img/lti-3_click-smartycards.png)

::: warning
Your Canvas administrator must first add and enable the SmartyCards LTI for your institution. If you don't see SmartyCards in the External Tool list, contact your Canvas admin or email <latistecharch@umn.edu>.
:::


5. **Choose the deck** you want to assign (or create a new deck), and click **Create Assignment**:
![Select or create a deck to assign](../img/lti-4_select-deck.png)

6. Click **Select** to confirm your choice:
![Click Select to confirm the deck choice](../img/lti-5_choose-select.png)

7. **Save** the assignment

8. Your assignment is now set up. You should see it embedded in the assignment page (or a link to open SmartyCards in a new tab):
![Final SmartyCards assignment in Canvas](../img/lti-6_embedded-in-canvas.png)

9. The assignment column is automatically created in the Gradebook. When a student completes their practice session, students will be awarded 100% for completing the activity:
![Grade passback in Canvas gradebook](../img/lti-7_grade-recorded.png)


### Student Experience

When students click the assignment:

1. They are taken directly to the SmartyCards practice activity
2. They practice all cards in the deck
3. Once complete, their grade (100%) is automatically submitted to Canvas

Students can return to the assignment to practice again, but their grade is recorded on first completion.

Students may also access the practice deck directly in SmartyCards, 

### How Grading Works

SmartyCards assignments are graded on **participation, not correctness**. Students earn 100% once they complete the practice session by going through all cards in the deck. The goal is to encourage practice and engagement, not to penalize students for getting answers wrong while learning.

If you have a large deck, consider breaking it into smaller decks for multiple assignments to make it more manageable for students.

## Embedding Activities (no grades recorded)

You can also embed SmartyCards activities into any Canvas page without grading. You will need to be a deck **owner** to access the embed code.

### Step by Step

To embed into a Canvas page:

1. Go your the SmartyCards deck, and click `Share`.

   ![Click the Share button](../img/click-share.png)

2. On the Share page, find the Embed Deck section. You will see embed code for each deck activity: [Practice (Flashcards)](../activities/practice-flashcards.md), [Quiz](../activities/quiz.md), [Matching](../activities/matching-game.md). **Copy the embed code** of the activity you wish to embed in Canvas.

   ![Embed section on share page. There is 3 options for embedding: practice, quiz, and matching](../img/share-page-embed-code.png)

3. Now, go to your Canvas classroom, and create or choose the page to place the embed:

   ![On the the Canvas page, choose the Edit button to access the page editor](../img/click-edit-page-in-canvas.png)

4. In the editor, click the `</>` HTML Editor icon, to switch to HTML mode:

   ![Change to HTML Editor mode in the page editor](../img/click-html-editor-button.png)

5. At the bottom, **paste your embed code**:

   ![Paste embed code at the bottom of the html, while in HTML Edit mode](../img/paste-embed-in-html-editor.png)

6. Click `Save`. Then you should see your embedded SmartyCards activity. (Or you may see page to sign-in to smartycards first, which will redirect you to the activity):

   ![Final embedded activity in SmartyCards](../img/final-practice-embedded-in-canvas.png)

7. To see participation and avg card difficulty,

## FAQ

- **How can I see participation and average card difficulty?**

  Use the [Deck Summary Report](./deck-summary-report.md) to see a summary of participation.

- **Can I set up a SmartyCards assignment to grade students for participation?**

  Yes! See [LTI Assignments](#setting-up-an-assignment-in-canvas) above. You can create Canvas assignments with automatic grade passback. Students earn 100% when they complete the practice session.

- **Can students see one another's activity?**

  No. The deck summary report is only visible to deck owners.

- **I have another question/suggestion about using SmartyCards with Canvas!**

  We're always happy to help! Email <latistecharch@umn.edu>.
