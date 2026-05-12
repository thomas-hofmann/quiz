<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

use App\Entity\Quiz;
use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Answer;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\LeaderBoardEntry;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController {

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboardAction(EntityManagerInterface $entityManager): Response {
        $quizRepository = $entityManager->getRepository(Quiz::class);
        /** @var \Symfony\Component\Security\Core\User\UserInterface $user */
        $user = $this->getUser();
        $quizzes = [];
        $quizzes = $quizRepository->findBy(['user' => $user], ['name' => 'ASC']);
        return $this->render('dashboard/dashboard.html.twig', [
            'quizzes' => $quizzes,
            'user' => $user,
        ]);
    }

    #[Route('/create-quiz', name: 'create-quiz')]
    public function createQuizAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('quizname')) {
            $quiz = new Quiz();
            $quiz->setName($request->get('quizname'));

            if($request->get('codeBool') == 'self' && $request->get('quizcode')) {
                $quizRepository = $entityManager->getRepository(Quiz::class);
                if ($quizRepository->findBy(['code' => $request->get('quizcode')])) {
                    return $this->render('dashboard/create-quiz.html.twig', [
                        'user' => $this->getUser(),
                        'error' => true,
                    ]);
                }
                $code = $request->get('quizcode');
                $quiz->setCode($request->get('quizcode'));
            } else {
                $code = uniqid();
                $quiz->setCode($code);
            }

            if ($request->get('without-leaderboard') == true) {
                $quiz->setWithoutLeaderboard(true);
            }

            $quiz->setUser($this->getUser());

            $categoryId = $request->get('category');
            if ($categoryId) {
                $categoryRepository = $entityManager->getRepository(Category::class);
                $category = $categoryRepository->find($categoryId);
                if ($category) {
                    $quiz->setCategory($category); // Setze die neue Kategorie
                }
            }

            $entityManager->persist($quiz);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Quiz erfolgreich erstellt!'
            );

            return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
        }

        return $this->render('dashboard/create-quiz.html.twig', [
            'error' => false,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/update-questions', name: 'update_questions')]
    public function updateQuestionsAction(Request $request, EntityManagerInterface $entityManager): Response {
        $quizId = $request->request->get('quizId') ?? $request->query->get('quizId');
        $questionText = trim((string) ($request->request->get('question') ?? $request->query->get('question')));
        $questionImage = $this->normalizeImageUrl($request->request->get('question-image') ?? $request->query->get('question-image'));
        $quiz = null;

        if($quizId) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $quiz = $quizRepository->findOneBy(['id' => $quizId]);
        }

        if (!$quiz) {
            return $this->redirectToRoute('dashboard');
        }

        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        if ($questionText === '' && $questionImage === null) {
            return $this->render('dashboard/edit-quiz.html.twig', [
                'questions' => $quiz->getQuestions(),
                'quiz' => $quiz,
                'error'=> false,
                'minCorrectError' => false,
                'answerContentError' => false,
                'questionContentError' => true,
                'user' => $this->getUser(),
            ]);
        }

        if($quiz) {
            $question = new Question();
            $question->setText($questionText);
            $question->setImage($questionImage);
            $minCorrect = 0;
            for ($i = 1; $i <= 4; $i++) {
                $answerText = trim((string) ($request->request->get('answer' . $i) ?? $request->query->get('answer' . $i)));
                $answerImage = $this->normalizeImageUrl($request->request->get('answer' . $i . '-image') ?? $request->query->get('answer' . $i . '-image'));

                if ($answerText === '' && $answerImage === null) {
                    return $this->render('dashboard/edit-quiz.html.twig', [
                        'questions' => $quiz->getQuestions(),
                        'quiz' => $quiz,
                        'error'=> false,
                        'minCorrectError' => false,
                        'answerContentError' => true,
                        'questionContentError' => false,
                        'user' => $this->getUser(),
                    ]);
                }

                $answer = new Answer();
                $answer->setText($answerText);
                if ($request->request->get('answer' . $i . '-correct') ?? $request->query->get('answer' . $i . '-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                }
                $answer->setImage($answerImage);
                $question->addAnswer($answer);
            }

            $question->setQuiz($quiz);

            if (count($quiz->getQuestions())) {
                $positions = array_map(function($question) {
                    return $question->getPosition();
                }, $quiz->getQuestions());
                
                $highestPosition = max($positions);

                $question->setPosition($highestPosition + 1);
             } else {
                $question->setPosition(1);
             }
            
            if ($minCorrect == 0) {
                return $this->render('dashboard/edit-quiz.html.twig', [
                    'questions' => $quiz->getQuestions(),
                    'quiz' => $quiz,
                    'error'=> false,
                    'minCorrectError' => true,
                    'answerContentError' => false,
                    'questionContentError' => false,
                    'user' => $this->getUser(),
                ]);
            }

            $quiz->setUpdatedAtValue();
            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Frage erfolgreich zugefügt!'
            );

            return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
        }

        return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
    }

    private function normalizeImageUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (mb_strlen($url) > 2048) {
            $this->addFlash('danger', 'Bild-Link ist zu lang (maximal 2048 Zeichen) und wurde ignoriert.');
            return null;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->addFlash('danger', 'Bild-Link ist ungueltig und wurde ignoriert.');
            return null;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            $this->addFlash('danger', 'Bild-Link muss mit http:// oder https:// starten.');
            return null;
        }

        return $url;
    }

    #[Route('/delete-quiz/{id}', name: 'delete_quiz')]
    public function deleteQuizAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        $entityManager->remove($quiz);
        $entityManager->flush();

        $this->addFlash(
            'danger',
            'Quiz erfolgreich gelöscht!'
        );

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/delete-question/{id}', name: 'delete_question')]
    public function deleteQuestionAction(Question $question, EntityManagerInterface $entityManager): Response {
        if ($question->getQuiz()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        $quiz = $question->getQuiz();
        $quiz->setUpdatedAtValue();
        $entityManager->remove($question);
        $entityManager->flush();

        $this->addFlash(
            'danger',
            'Frage erfolgreich gelöscht!'
        );

        return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
    }

    #[Route('/edit-question/{id}', name: 'edit_question')]
    public function editQuestionAction(Question $question, EntityManagerInterface $entityManager): Response {
        if ($question->getQuiz()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }
        return $this->render('dashboard/edit-question.html.twig', [
            'question' => $question,
            'minCorrectError' => false,
            'answerContentError' => false,
            'questionContentError' => false,
        ]);
    }

    #[Route('/update-question', name: 'update_question')]
    public function updateQuestionAction(Request $request, EntityManagerInterface $entityManager): Response {
        $questionId = $request->request->get('questionId') ?? $request->query->get('questionId');
        $questionText = trim((string) ($request->request->get('question') ?? $request->query->get('question')));
        $questionImage = $this->normalizeImageUrl($request->request->get('question-image') ?? $request->query->get('question-image'));
        $question = null;

        if($questionId) {
            $questionRepository = $entityManager->getRepository(Question::class);
            $question = $questionRepository->findOneBy(['id' => $questionId]);
            if (!$question) {
                return $this->redirectToRoute('dashboard');
            }
            if ($question->getQuiz()->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
            }
            if ($questionText === '' && $questionImage === null) {
                return $this->render('dashboard/edit-question.html.twig', [
                    'question' => $question,
                    'minCorrectError' => false,
                    'answerContentError' => false,
                    'questionContentError' => true,
                ]);
            }

            $question->setText($questionText);
            $question->setImage($questionImage);
            $minCorrect = 0;
            for ($i = 1; $i <= 4; $i++) {
                $answer = $question->getAnswer($request->request->get('answerId' . $i) ?? $request->query->get('answerId' . $i));
                if (!$answer) {
                    $this->addFlash('danger', 'Antwort konnte nicht gefunden werden. Bitte Seite neu laden und erneut versuchen.');
                    return $this->redirectToRoute('edit_question', ['id' => $question->getId()]);
                }
                $answerText = trim((string) ($request->request->get('answer' . $i) ?? $request->query->get('answer' . $i)));
                $answerImage = $this->normalizeImageUrl($request->request->get('answer' . $i . '-image') ?? $request->query->get('answer' . $i . '-image'));

                if ($answerText === '' && $answerImage === null) {
                    return $this->render('dashboard/edit-question.html.twig', [
                        'question' => $question,
                        'minCorrectError' => false,
                        'answerContentError' => true,
                        'questionContentError' => false,
                    ]);
                }

                $answer->setText($answerText);
                $answer->setImage($answerImage);
                if ($request->request->get('answer' . $i . '-correct') ?? $request->query->get('answer' . $i . '-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                } else {
                    $answer->setIsCorrect(false);
                }
            }

            if ($minCorrect == 0) {
                return $this->render('dashboard/edit-question.html.twig', [
                    'question' => $question,
                    'minCorrectError' => true,
                    'answerContentError' => false,
                    'questionContentError' => false,
                ]);
            }

            $question->getQuiz()->setUpdatedAtValue();
            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Frage erfolgreich bearbeitet!'
            );

            return $this->redirectToRoute('edit_question', ['id' => $question->getId()]);
        }

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/edit-quiz/{id}', name: 'edit_quiz')]
    public function editQuizAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }
        return $this->render('dashboard/edit-quiz.html.twig', [
            'questions' => $quiz->getQuestions(),
            'quiz' => $quiz,
            'user' => $this->getUser(),
            'error'=> false,
            'minCorrectError' => false,
            'answerContentError' => false,
            'questionContentError' => false,
        ]);
    }

    #[Route('/update-quiz', name: 'update_quiz')]
    public function updateQuizAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('quizId')) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $categoryRepository = $entityManager->getRepository(Category::class);
            $quiz = $quizRepository->findOneBy(['id' => $request->get('quizId')]);
            if ($quiz->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
            }
            $quiz->setName($request->get('quizName'));

            if ($request->get('without-leaderboard') == true) {
                $quiz->setWithoutLeaderboard(true);
            } else {
                $quiz->setWithoutLeaderboard(false);
            }

            if (!$quizRepository->findOneBy(['code' => $request->get('quizCode')])) {
                $quiz->setCode($request->get('quizCode'));
            } else if ($quiz->getCode() !== $request->get('quizCode')){
                return $this->render('dashboard/edit-quiz.html.twig', [
                    'questions' => $quiz->getQuestions(),
                    'quiz' => $quiz,
                    'user' => $this->getUser(),
                    'minCorrectError' => false,
                    'error'=> true,
                ]);
            }

            $categoryId = $request->get('category');
            if ($categoryId && $categoryId !== 'no-category') {
                $category = $categoryRepository->find($categoryId);
                if ($category) {
                    $quiz->setCategory($category); // Setze die neue Kategorie
                }
            } else if ($categoryId == 'no-category') {
                $quiz->setCategory(null);
            }

            $entityManager->persist($quiz);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Quiz erfolgreich bearbeitet!'
            );

            return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
        }
        
    }

    #[Route('/refresh-quiz-image-cache/{id}', name: 'refresh_quiz_image_cache', methods: ['POST'])]
    public function refreshQuizImageCacheAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        $quiz->setUpdatedAtValue();
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Bildcache wurde aktualisiert!'
        );

        return $this->redirectToRoute('dashboard');
    }

    // Funktion zum Erstellen des Charts
    function createChart($chartBuilder, $type, $data, $options)
    {
        $chart = $chartBuilder->createChart($type);
        $chart->setData($data);
        $chart->setOptions($options);
        return $chart;
    }

    #[Route('/leaderboard/{id}', name: 'leaderboard')]
    public function leaderboardAction(Request $request, Quiz $quiz, EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder): Response {
        if (!$quiz) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
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

        $barChart = null;
        if (count($quiz->getQuestions())) {
            $questionsCount = count($quiz->getQuestions());
            $scoreCounts = array_fill(0, $questionsCount + 1, 0);

            // Berechnung der Score-Häufigkeiten
            foreach ($leaderBoardEntries as $entry) {
                $score = $entry->getScore();
                if (isset($scoreCounts[$score])) {
                    $scoreCounts[$score]++;
                } else {
                    $scoreCounts[$score] = 1;
                }
            }

            ksort($scoreCounts);

            // Gemeinsame Datenstruktur
            $chartData = [
                'datasets' => [
                    [
                        'label' => 'Anzahl',
                        'backgroundColor' => 'rgb(33, 37, 41)',
                        'borderColor' => 'rgb(33, 37, 41)',
                        'data' => array_values($scoreCounts),
                    ],
                ],
                'labels' => array_keys($scoreCounts),
            ];
            // dd(max($scoreCounts));
            // Gemeinsame Optionenstruktur
            $chartOptions = [
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => max($scoreCounts),
                        'ticks' => [
                            'stepSize' => 1,  // Ganze Zahlen in Schritten von 1
                            'beginAtZero' => true,  // Beginne bei 0
                            'precision' => 0,  // Verhindert Dezimalstellen
                        ],
                    ],
                ],
            ];

            // Erstellung eines Balkendiagramms
            $barChart = $this->createChart($chartBuilder, Chart::TYPE_BAR, $chartData, $chartOptions);
        }

        $session = $request->getSession();
        return $this->render('dashboard/statistics.html.twig', [
            'leaderBoardEntries' => $leaderBoardEntries,
            'averageScorePercentage' => $averageScorePercentage,
            'quiz' => $quiz,
            'averageScore' => $averageScore,
            'matrikelnummerHash' => $session->get('matrikelnummerHash'),
            'chartBar' => $barChart,
        ]);
    }

    #[Route('/chart-data/{id}', name: 'chart-data')]
    public function getChartData(Quiz $quiz, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz ID is required'], 400);
        }

        if (!$quiz) {
            return new JsonResponse(['error' => 'Quiz not found'], 404);
        }

        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz]);

        $questionsCount = count($quiz->getQuestions());
        $scoreCounts = array_fill(0, $questionsCount + 1, 0);

        // Score-Häufigkeiten berechnen
        foreach ($leaderBoardEntries as $entry) {
            $score = $entry->getScore();
            if (isset($scoreCounts[$score])) {
                $scoreCounts[$score]++;
            } else {
                $scoreCounts[$score] = 1;
            }
        }

        ksort($scoreCounts);

        // Berechne den maximalen Wert für suggestedMax
        $maxScore = max(array_keys($scoreCounts));  // Der höchste Punktwert

        $chartData = [
            'datasets' => [
                [
                    'label' => 'Anzahl',
                    'backgroundColor' => 'rgb(33, 37, 41)',
                    'borderColor' => 'rgb(33, 37, 41)',
                    'data' => array_values($scoreCounts),
                ],
            ],
            'labels' => array_keys($scoreCounts),
            'maxScore' => $maxScore,  // Füge maxScore hinzu
        ];

        return new JsonResponse($chartData);
    }

    #[Route('/clear-leaderboard/{id}', name: 'clear_leaderboard')]
    public function clearLeaderboardAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }
        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz]);

        foreach ($leaderBoardEntries as $entry) {
            $entityManager->remove($entry);
        }

        $entityManager->flush();

        $this->addFlash(
            'danger',
            'Satistik erfolgreich gelöscht!'
        );

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/question-sort', name: 'question-sort')]
    public function sort(Request $request, EntityManagerInterface $entityManager): Response
    {
        $positions = $request->get('positions');

        foreach ($positions as $id => $position) {
            $question = $entityManager->getRepository(Question::class)->find($id);
            if ($question) {
                $question->setPosition($position);
                $entityManager->persist($question);
            }
        }

        $entityManager->flush();

        return new Response('Sortierung erfolgreich gespeichert.');
    }

    #[Route('/toggle_quiz/{id}', name: 'toggle_quiz', methods: ['POST'])]
    public function toggleQuiz(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        if (isset($data['isEnabled'])) {
            $quiz->setEnabled((bool) $data['isEnabled']);
            $entityManager->persist($quiz);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }

    #[Route('/answer-stats/{id}', name: 'answer-stats')]
    public function answerStatsAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if (!$quiz) {
            return $this->render('error/error.html.twig', [
            ]);
        }

        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        $sortedEntries = [];
        foreach ($quiz->getQuestions() as $index => $question) {
            $sortedEntries[$index] = $this->buildQuestionStats($quiz, $question);
        }

        return $this->render('dashboard/answer-stats.html.twig', [
            'sortedEntries' => $sortedEntries,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/answer-stats/{quizId}/question/{questionId}', name: 'answer-stats-question')]
    public function answerQuestionStatsAction(
        #[MapEntity(id: 'quizId')] Quiz $quiz,
        #[MapEntity(id: 'questionId')] Question $question
    ): Response {
        if ($quiz->getUser() !== $this->getUser() || $question->getQuiz()?->getId() !== $quiz->getId()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        $questionIndex = 0;
        foreach ($quiz->getQuestions() as $index => $quizQuestion) {
            if ($quizQuestion->getId() === $question->getId()) {
                $questionIndex = $index;
                break;
            }
        }

        return $this->render('dashboard/_answer-stat-question.html.twig', [
            'entry' => $this->buildQuestionStats($quiz, $question),
            'index' => $questionIndex,
            'quiz' => $quiz,
        ]);
    }

    private function buildQuestionStats(Quiz $quiz, Question $question): array
    {
        $answers = $question->getAnswers();
        $stats = [
            'question' => $question,
            'rightCount' => 0,
            'percent' => 0,
            'playerCount' => count($quiz->getLeaderBoardEntries()),
            'answerOne' => ['object' => $answers[0], 'cnt' => 0],
            'answerTwo' => ['object' => $answers[1], 'cnt' => 0],
            'answerThree' => ['object' => $answers[2], 'cnt' => 0],
            'answerFour' => ['object' => $answers[3], 'cnt' => 0],
            'count' => 0,
        ];
        $answerStatsKeys = ['answerOne', 'answerTwo', 'answerThree', 'answerFour'];
        $rightIndex = 0;

        for ($answerIndex = 0; $answerIndex < 4; $answerIndex++) {
            $answer = $answers[$answerIndex];
            if ($answer->getIsCorrect()) {
                $rightIndex++;
            }
        }

        foreach ($quiz->getLeaderBoardEntries() as $entry) {
            $playerRightIndex = 0;

            if ($entry->getAllAnswers()) {
                foreach ($entry->getAllAnswers() as $entryAnswer) {
                    if ($question->getId() == $entryAnswer['questionId']) {
                        foreach ($entryAnswer['answers'] as $playerAnswer) {
                            for ($answerIndex = 0; $answerIndex < 4; $answerIndex++) {
                                $answer = $answers[$answerIndex];
                                if ($playerAnswer == $answer->getId()) {
                                    $stats[$answerStatsKeys[$answerIndex]]['cnt']++;
                                    $playerRightIndex += $answer->getIsCorrect() ? 1 : -1;
                                }
                            }
                        }

                        if ($rightIndex == $playerRightIndex) {
                            $stats['rightCount']++;
                        }
                    }
                }
            }
        }

        if ($stats['playerCount']) {
            $stats['percent'] = round(($stats['rightCount'] / $stats['playerCount']) * 100, 2);
        }

        return $stats;
    }

    #[Route('/categories', name: 'categories')]
    public function categoriesAction(Request $request, EntityManagerInterface $entityManager): Response {
        return $this->render('dashboard/categories.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/create-category', name: 'create-category')]
    public function createCategoryAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('categoryname') && $request->get('categoryname') !== 'no-category') {
            $category = new Category();
            $category->setName($request->get('categoryname'));

            $category->setUser($this->getUser());
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Kategorie erfolgreich erstellt!'
            );
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('dashboard/create-category.html.twig', [
            'error' => false,
        ]);
    }

    #[Route('/edit-category/{id}', name: 'edit-category')]
    public function editCategoryAction(Category $category, Request $request, EntityManagerInterface $entityManager): Response {
        if ($category->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        return $this->render('dashboard/edit-category.html.twig', [
            'error' => false,
            'category' => $category,
        ]);
    }

    #[Route('/update-category', name: 'update-category')]
    public function updateCategoryAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('categoryname') && $request->get('categoryId')) {
            $categoryRepository = $entityManager->getRepository(Category::class);
            $category = $categoryRepository->findOneBy(['id' => $request->get('categoryId')]);

            if ($category->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
            }
            if($request->get('categoryname')) {
                $category->setName($request->get('categoryname'));

                $entityManager->persist($category);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Kategorie erfolgreich erstellt!'
                );
                
            }
        }

        return $this->redirectToRoute('categories');
    }

    #[Route('/delete-category/{id}', name: 'delete-category')]
    public function deleteCategoryAction(Category $category, Request $request, EntityManagerInterface $entityManager): Response {
        if ($category->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }

        if($category) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Kategorie erfolgreich gelöscht!'
            );

            return $this->redirectToRoute('categories');
        }

        return $this->redirectToRoute('categories');
    }
}
