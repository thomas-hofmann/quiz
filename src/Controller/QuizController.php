<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Quiz;
use App\Entity\LeaderBoardEntry;
use Doctrine\ORM\EntityManagerInterface;

class QuizController extends AbstractController {
    
    public function getRandomMatrikelnumber(Quiz $quiz, EntityManagerInterface $entityManager): string {
        require __DIR__ . '/../Utils/names.php';
        shuffle($firstNames);
        shuffle($lastNames);
        
        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);

        do {
            $matrikelnummer = $firstNames[random_int(0, count($firstNames) - 1)] . ' ' . $lastNames[random_int(0, count($lastNames) - 1)];
        } while ($leaderBoardEntryRepository->findBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer]));

        return $matrikelnummer;
    }

    #[Route('/quiz-update-name', name: 'update_name', format: 'json')]
    public function quizNewNameAction(Request $request, EntityManagerInterface $entityManager): JsonResponse {
        $session = $request->getSession();
        if ($session->get('code')) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);

            $matrikelnummer = $this->getRandomMatrikelnumber($quiz, $entityManager);

            $session->set('matrikelnummer', $matrikelnummer);

            return $this->json(['matrikelnummer' => $matrikelnummer]);
        }
    }

    #[Route('/quiz-initial', name: 'quiz-initial')]
    public function quizInitialAction(Request $request, EntityManagerInterface $entityManager): Response {
        if (!$request->get('code')) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        $session = $request->getSession();
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);

        if (!$quiz) {
            return $this->render('error/error-code.html.twig', [
            ]);
        }

        if (!$quiz->isEnabled()) {
            return $this->render('error/error-disabled.html.twig', [
            ]);
        }

        if (!$session->get('matrikelnummerHash')) {
            $matrikelnummer = $this->getRandomMatrikelnumber($quiz, $entityManager);
            $hash = strtolower(bin2hex(random_bytes(32)));
            $session->set('matrikelnummerHash', $hash);
        } else {
            $matrikelnummer = $session->get('matrikelnummer');
        }
        
        $session->set('matrikelnummer', $matrikelnummer);
        $session->set('code', $request->get('code'));
        $session->set('rightIndex', 0);
        $session->set('rightAnswer', false);
        $session->set('rightAnswerText', '');
        $session->set('index', 0);
        $session->set('allAnswers', []);
        $session->set('alert', false);

        return $this->redirectToRoute('quiz-start');
    }

    #[Route('/quiz-start', name: 'quiz-start')]
    public function quizStartAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();
        if (!$session->get('matrikelnummer') || !$session->get('code')) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);
        $matrikelnummer = $session->get('matrikelnummer');

        return $this->render('quiz/initial.html.twig', [
            'matrikelnummer' => $matrikelnummer,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/quiz', name: 'quiz')]
    public function quizAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();
        $quizRepository = $entityManager->getRepository(Quiz::class);

        if (!$session->get('matrikelnummer') || !$session->get('code')) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);
        $index = $session->get('index');
        $rightIndex = $session->get('rightIndex');
        $matrikelnummer = $session->get('matrikelnummer');

        if (!$quiz || !$quiz->getQuestions()) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        $rightAnswer = $session->get('rightAnswer');
        $rightAnswersText = $session->get('rightAnswers');

        $playerAnswers = [];
        $rightAnswers = [];
        $correctCount = 0;
        $correctCountPlayer = 0;
        $answerReceived = false;
        $question = null;

        if (isset($quiz->getQuestions()[$index])) {
            $question = $quiz->getQuestions()[$index];
        }

        if ($question) {
            foreach ($question->getAnswers() as $answer) {
                if ($answer->getIsCorrect()) {
                    $correctCount++;
                }
            }
            $requestIndex = 0;
            foreach ($question->getAnswers() as $answer) {
                $requestIndex++;
                if ($answer->getIsCorrect()) {
                    $rightAnswers[] = $answer->getText();
                }
                if ($request->get('answer' . $requestIndex)) {
                    $answerReceived = true;
                    $playerAnswers[] = $request->get('answer' . $requestIndex);
                    if ($question->getAnswer($request->get('answer' . $requestIndex))->getIsCorrect()) {
                        $correctCountPlayer++;
                    } else {
                        $correctCountPlayer--;
                    }
                }
            }
        }
        
        if ($question && $answerReceived && $correctCount == $correctCountPlayer) {
            $session->set('rightAnswer', true);
            $session->set('rightAnswers', $rightAnswers);
            
            $allAnswers = $session->get('allAnswers');
            $allAnswers[] = ['questionId' => $question->getId(), 'answers' => $playerAnswers];
            $session->set('allAnswers', $allAnswers);

            $rightIndex = $rightIndex + 1;
            $session->set('rightIndex', $rightIndex);
        } else if ($question && $answerReceived) {
            $session->set('rightAnswer', false);
            $session->set('rightAnswers', $rightAnswers);

            $allAnswers = $session->get('allAnswers');
            $allAnswers[] = ['questionId' => $question->getId(), 'answers' => $playerAnswers];
            $session->set('allAnswers', $allAnswers);
        }

        if ($answerReceived) {
            $index = $index + 1;
            $session->set('index', $index);
            
            $diff = count($playerAnswers) + $correctCountPlayer;
            if (count($playerAnswers) > 0 && $diff != 0) {
                $session->set('alert', true);
            } else {
                $session->set('alert', false);
            }
            
            return $this->redirectToRoute('quiz');
        }

        if (!isset($quiz->getQuestions()[$index])) {
            $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
            $session->set('finished', true);
            if ($session->get('matrikelnummerHash') && $leaderBoardEntryRepository->findOneBy(['quiz' => $quiz, 'hash' => $session->get('matrikelnummerHash')])) {
                $leaderBoardEntry = $leaderBoardEntryRepository->findOneBy(['quiz' => $quiz, 'hash' => $session->get('matrikelnummerHash')]);
                $leaderBoardEntry->setScore($rightIndex);
                $leaderBoardEntry->setAllAnswers($session->get('allAnswers'));

                $entityManager->persist($leaderBoardEntry);
                $entityManager->flush();
            } else {
                $leaderBoardEntry = new LeaderBoardEntry();

                $leaderBoardEntry->setMatrikelnumber($matrikelnummer);
                $leaderBoardEntry->setQuiz($quiz);
                $leaderBoardEntry->setScore($rightIndex);

                $leaderBoardEntry->setHash($session->get('matrikelnummerHash'));

                $leaderBoardEntry->setAllAnswers($session->get('allAnswers'));

                $entityManager->persist($leaderBoardEntry);
                $entityManager->flush();
            }

            return $this->redirectToRoute('quiz-finished');
        };

        $questions = $quiz->getQuestions();
        $shuffledQuestions = [];

        foreach ($questions as $question) {
            $answers = [
                ['index' => 1, 'answerObject' => $question->getAnswers()[0]],
                ['index' => 2, 'answerObject' => $question->getAnswers()[1]],
                ['index' => 3, 'answerObject' => $question->getAnswers()[2]],
                ['index' => 4, 'answerObject' => $question->getAnswers()[3]],
            ];

            shuffle($answers);

            $shuffledQuestions[] = [
                'question' => $question,
                'answers' => $answers
            ];
        }

        return $this->render('quiz/quiz.html.twig', [
            'quiz' => $quiz,
            'shuffledQuestions' => $shuffledQuestions,
            'matrikelnummer' => $matrikelnummer,
            'index' => $index,
            'rightIndex' => $rightIndex,
            'rightAnswer'=> $rightAnswer,
            'rightAnswers' => $rightAnswersText,
            'alert' => $session->get('alert'),
        ]);
    }

    #[Route('/quiz-finished', name: 'quiz-finished')]
    public function quizFinishedAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();
        if (!$session->get('matrikelnummer') || !$session->get('code')) {
            return $this->render('error/error.html.twig', [
            ]);
        }
        
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);
        $matrikelnummer = $session->get('matrikelnummer');

        if (!$quiz || !$quiz->getQuestions()) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        $rightIndex = $session->get('rightIndex');

        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz], ['score' => 'DESC', 'id' => 'ASC']);

        $totalScore = 0;
        $numEntries = count($leaderBoardEntries);

        foreach ($leaderBoardEntries as $entry) {
            $totalScore += $entry->getScore();
        }

        $averageScore = 0;
        if ($numEntries > 0) {
            $averageScore = round($totalScore / $numEntries, 2);
        }

        $averageScorePercentage = round(($averageScore / count($quiz->getQuestions())) * 100);

        $veryGoodMessages = [
            "Hervorragende Leistung!",
            "Fantastisch, weiter so!",
            "Exzellente Arbeit!",
            "Großartig! Sie sind auf dem richtigen Weg!",
            "Sehr gut!",
            "Sie haben das großartig gemacht!",
            "Spitzenleistung, weiter so!"
        ];
        
        $warningMessages = [
            "Ganz ordentlich, aber es gibt Raum für Verbesserung!",
            "Nicht schlecht, aber versuchen Sie es noch einmal!",
            "Das ist ein solider Anfang, aber es kann besser werden!",
            "Gute Ansätze, aber ein bisschen mehr Übung wäre hilfreich!",
            "Das geht noch besser!",
            "Sie kommen gut voran, aber noch nicht ganz dort!",
            "Ein paar kleine Fehler, aber das können Sie beheben!"
        ];
        
        $dangerMessages = [
            "Nicht aufgegeben, Sie können es besser!",
            "Es gibt noch viel zu lernen, aber Sie schaffen das!",
            "Lassen Sie sich nicht entmutigen, Übung macht den Meister!",
            "Gehen Sie die Fragen noch einmal durch, das hilft!",
            "Da müssen Sie noch etwas üben!",
            "Jede Übung bringt Sie näher an das Ziel!",
            "Nutzen Sie die Gelegenheit zum Lernen und Wachsen!"
        ];

        // Zufällige Auswahl
        $percentage = ($rightIndex / count($quiz->getQuestions())) * 100;
        $selectedMessage = '';
        if ($percentage >= 70) {
            $selectedMessage = $veryGoodMessages[array_rand($veryGoodMessages)];
        } elseif ($percentage >= 40) {
            $selectedMessage = $warningMessages[array_rand($warningMessages)];
        } else {
            $selectedMessage = $dangerMessages[array_rand($dangerMessages)];
        }

        return $this->render('quiz/finished.html.twig', [
            'quiz' => $quiz,
            'allAnswers' => $session->get('allAnswers'),
            'matrikelnummer' => $matrikelnummer,
            'averageScorePercentage' => $averageScorePercentage,
            'averageScore' => $averageScore,
            'rightIndex' => $rightIndex,
            'rightAnswer' => $session->get('rightAnswer'),
            'leaderBoardEntries' => $leaderBoardEntries,
            'alert' => $session->get('alert'),
            'selectedMessage' => $selectedMessage,
            'matrikelnummerHash' => $session->get('matrikelnummerHash'),
        ]);
    }

    #[Route('/quiz-leaderboard/{id}', name: 'quiz-leaderboard')]
    public function quizLeaderboardAction(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if (!$quiz) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz], ['score' => 'DESC', 'id' => 'ASC']);

        $totalScore = 0;
        $numEntries = count($leaderBoardEntries);

        foreach ($leaderBoardEntries as $entry) {
            $totalScore += $entry->getScore();
        }

        $averageScore = 0;
        $averageScorePercentage = 0;
        if ($numEntries > 0) {
            $averageScore = round($totalScore / $numEntries, 2);
            $averageScorePercentage = round(($averageScore / count($quiz->getQuestions())) * 100);
        }   
        $session = $request->getSession();
        return $this->render('quiz/leaderboard.html.twig', [
            'leaderBoardEntries' => $leaderBoardEntries,
            'quiz' => $quiz,
            'averageScore' => $averageScore,
            'averageScorePercentage' => $averageScorePercentage,
            'matrikelnummerHash' => $session->get('matrikelnummerHash'),
        ]);
    }
}