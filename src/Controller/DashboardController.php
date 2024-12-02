<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        if($request->get('quizId')) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $quiz = $quizRepository->findOneBy(['id' => $request->get('quizId')]);
        }

        if($quiz && $request->get('question')) {
            if ($quiz->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
            }

            $question = new Question();
            $question->setText($request->get('question'));
            $minCorrect = 0;
            if ($request->get('answer1')) {
                $answer = new Answer();
                $answer->setText($request->get('answer1'));
                if ($request->get('answer1-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                }
                $question->addAnswer($answer);
            }
            
            if ($request->get('answer2')) {
                $answer = new Answer();
                $answer->setText($request->get('answer2'));
                if ($request->get('answer2-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                }
                $question->addAnswer($answer);
            }

            if ($request->get('answer3')) {
                $answer = new Answer();
                $answer->setText($request->get('answer3'));
                if ($request->get('answer3-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                }
                $question->addAnswer($answer);
            }

            if ($request->get('answer4')) {
                $answer = new Answer();
                $answer->setText($request->get('answer4'));
                if ($request->get('answer4-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                }
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
                    'minCorrectError' => true
                ]);
            }

            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Frage erfolgreich zugefügt!'
            );

            return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
        }
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

        $entityManager->remove($question);
        $entityManager->flush();

        $this->addFlash(
            'danger',
            'Frage erfolgreich gelöscht!'
        );

        return $this->redirectToRoute('edit_quiz', ['id' => $question->getQuiz()->getId()]);
    }

    #[Route('/edit-question/{id}', name: 'edit_question')]
    public function editQuestionAction(Question $question, EntityManagerInterface $entityManager): Response {
        if ($question->getQuiz()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
        }
        return $this->render('dashboard/edit-question.html.twig', [
            'question' => $question,
            'minCorrectError' => false
        ]);
    }

    #[Route('/update-question', name: 'update_question')]
    public function updateQuestionAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('questionId')) {
            $questionRepository = $entityManager->getRepository(Question::class);
            $question = $questionRepository->findOneBy(['id' => $request->get('questionId')]);
            if ($question->getQuiz()->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Das ist dir nicht erlaubt. Sollte es sich um ein Fehler handeln, kontaktiere den Admin.');
            }
            $question->setText($request->get('question'));
            $minCorrect = 0;
            if ($request->get('answer1')) {
                $answer = $question->getAnswer($request->get('answerId1'));
                $answer->setText($request->get('answer1'));
                if ($request->get('answer1-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                } else {
                    $answer->setIsCorrect(false);
                }
            }
            
            if ($request->get('answer2')) {
                $answer = $question->getAnswer($request->get('answerId2'));
                $answer->setText($request->get('answer2'));
                if ($request->get('answer2-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                } else {
                    $answer->setIsCorrect(false);
                }
            }

            if ($request->get('answer3')) {
                $answer = $question->getAnswer($request->get('answerId3'));
                $answer->setText($request->get('answer3'));
                if ($request->get('answer3-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                } else {
                    $answer->setIsCorrect(false);
                }
            }

            if ($request->get('answer4')) {
                $answer = $question->getAnswer($request->get('answerId4'));
                $answer->setText($request->get('answer4'));
                if ($request->get('answer4-correct')) {
                    $minCorrect++;
                    $answer->setIsCorrect(true);
                } else {
                    $answer->setIsCorrect(false);
                }
            }

            if ($minCorrect == 0) {
                return $this->render('dashboard/edit-question.html.twig', [
                    'question' => $question,
                    'minCorrectError' => true
                ]);
            }

            $entityManager->persist($question);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Frage erfolgreich bearbeitet!'
            );

            return $this->redirectToRoute('edit_question', ['id' => $question->getId()]);
        }  
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
        $lineChart = null;
        if (count($quiz->getQuestions())) {
            $questionsCount = count($quiz->getQuestions());
            $scoreCounts = array_fill(0, $questionsCount, 0);

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
                        'title' => [
                            'display' => true,
                            'text' => 'Häufigkeit',
                        ],
                        'ticks' => [
                            'stepSize' => 1,  // Ganze Zahlen in Schritten von 1
                            'beginAtZero' => true,  // Beginne bei 0
                            'precision' => 0,  // Verhindert Dezimalstellen
                        ],
                    ],
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Punktezahl',
                        ],
                    ],
                ],
            ];

            // Erstellung eines Balkendiagramms
            $barChart = $this->createChart($chartBuilder, Chart::TYPE_BAR, $chartData, $chartOptions);

            // Erstellung eines Liniendiagramms
            $lineChart = $this->createChart($chartBuilder, Chart::TYPE_LINE, $chartData, $chartOptions);
        }

        $session = $request->getSession();
        return $this->render('dashboard/statistics.html.twig', [
            'leaderBoardEntries' => $leaderBoardEntries,
            'averageScorePercentage' => $averageScorePercentage,
            'quiz' => $quiz,
            'averageScore' => $averageScore,
            'matrikelnummerHash' => $session->get('matrikelnummerHash'),
            'chartBar' => $barChart,
            'chartLine' => $lineChart
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
        $scoreCounts = array_fill(0, $questionsCount, 0);

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
            $sortedEntries[$index]['question'] = $question;
            $sortedEntries[$index]['answerOne']['object'] = $question->getAnswers()[0];
            $sortedEntries[$index]['answerOne']['cnt'] = 0;
            $sortedEntries[$index]['answerTwo']['object'] = $question->getAnswers()[1];
            $sortedEntries[$index]['answerTwo']['cnt'] = 0;
            $sortedEntries[$index]['answerThree']['object'] = $question->getAnswers()[2];
            $sortedEntries[$index]['answerThree']['cnt'] = 0;
            $sortedEntries[$index]['answerFour']['object'] = $question->getAnswers()[3];
            $sortedEntries[$index]['answerFour']['cnt'] = 0;
            $sortedEntries[$index]['count'] = 0;
            foreach ($quiz->getLeaderBoardEntries() as $entry) {
                
                if ($entry->getAllAnswers()) {
                    foreach ($entry->getAllAnswers() as $answer) {
                        
                        if ($question->getId() == $answer['questionId']) {
                            foreach ($answer['answers'] as $playerAnswer) {
                                if ($playerAnswer == $question->getAnswers()[0]->getId()) {
                                    $sortedEntries[$index]['answerOne']['cnt']++;
                                }
                                if ($playerAnswer == $question->getAnswers()[1]->getId()) {
                                    $sortedEntries[$index]['answerTwo']['cnt']++;
                                }
                                if ($playerAnswer == $question->getAnswers()[2]->getId()) {
                                    $sortedEntries[$index]['answerThree']['cnt']++;
                                }
                                if ($playerAnswer == $question->getAnswers()[3]->getId()) {
                                    $sortedEntries[$index]['answerFour']['cnt']++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $this->render('dashboard/answer-stats.html.twig', [
            'sortedEntries' => $sortedEntries,
            'quiz' => $quiz,
        ]);
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