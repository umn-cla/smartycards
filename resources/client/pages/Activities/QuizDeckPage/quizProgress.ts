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

export function createQuizProgress(): QuizProgress {
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

export function gradeAnswer(
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
  totalQuestions: number,
): boolean {
  return progress.questionIndex === totalQuestions - 1;
}

export function goToNextQuestion(
  progress: QuizProgress,
  totalQuestions: number,
): QuizProgress {
  if (
    progress.answer.status !== "showingResult" ||
    isLastQuestion(progress, totalQuestions)
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
  correctChoiceText: string,
): string {
  if (answer.isCorrect) {
    return "Correct!";
  }

  return `Incorrect. The correct answer is ${correctChoiceText}.`;
}

export function describeQuizScore(progress: QuizProgress): string {
  const answeredCount = progress.correctCount + progress.incorrectCount;
  return `Quiz complete. ${progress.correctCount} of ${answeredCount} correct.`;
}
