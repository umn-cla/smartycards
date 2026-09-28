import type * as T from "@/types";

interface ChoosingAnswer {
  status: "choosingAnswer";
  selectedChoiceIndex: number | null;
}

interface ShowingResult {
  status: "showingResult";
  selectedChoiceIndex: number;
  isCorrect: boolean;
}

type AnswerState = ChoosingAnswer | ShowingResult;

export interface QuizProgress {
  questionIndex: number;
  correctCount: number;
  incorrectCount: number;
  answer: AnswerState;
}

export function startQuizProgress(): QuizProgress {
  return {
    questionIndex: 0,
    correctCount: 0,
    incorrectCount: 0,
    answer: { status: "choosingAnswer", selectedChoiceIndex: null },
  };
}

export function selectChoice(
  progress: QuizProgress,
  choiceIndex: number,
): QuizProgress {
  if (progress.answer.status !== "choosingAnswer") {
    return progress;
  }

  return {
    ...progress,
    answer: { status: "choosingAnswer", selectedChoiceIndex: choiceIndex },
  };
}

export function checkAnswer(
  progress: QuizProgress,
  question: T.QuizQuestion,
): QuizProgress {
  const { answer } = progress;
  if (
    answer.status !== "choosingAnswer" ||
    answer.selectedChoiceIndex === null
  ) {
    return progress;
  }

  const isCorrect = answer.selectedChoiceIndex === question.correctChoiceIndex;
  const answerResult: ShowingResult = {
    status: "showingResult",
    selectedChoiceIndex: answer.selectedChoiceIndex,
    isCorrect,
  };

  if (isCorrect) {
    return {
      ...progress,
      correctCount: progress.correctCount + 1,
      answer: answerResult,
    };
  }

  return {
    ...progress,
    incorrectCount: progress.incorrectCount + 1,
    answer: answerResult,
  };
}

export function isLastQuestion(
  progress: QuizProgress,
  questionCount: number,
): boolean {
  return progress.questionIndex === questionCount - 1;
}

export function goToNextQuestion(
  progress: QuizProgress,
  questionCount: number,
): QuizProgress {
  if (
    progress.answer.status !== "showingResult" ||
    isLastQuestion(progress, questionCount)
  ) {
    return progress;
  }

  return {
    ...progress,
    questionIndex: progress.questionIndex + 1,
    answer: { status: "choosingAnswer", selectedChoiceIndex: null },
  };
}

export function describeAnswerResult(
  answer: ShowingResult,
  question: T.QuizQuestion,
): string {
  if (answer.isCorrect) {
    return "Correct!";
  }

  const correctChoiceNumber = question.correctChoiceIndex + 1;
  return `Incorrect. The correct answer is choice ${correctChoiceNumber}.`;
}

export function describeQuizScore(progress: QuizProgress): string {
  const answeredCount = progress.correctCount + progress.incorrectCount;
  return `Quiz complete. ${progress.correctCount} of ${answeredCount} correct.`;
}
