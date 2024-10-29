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

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController {

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboardAction(EntityManagerInterface $entityManager): Response {
        $quizRepository = $entityManager->getRepository(Quiz::class);
        /** @var \Symfony\Component\Security\Core\User\UserInterface $user */
        $user = $this->getUser();
        $quizzes = [];
        $quizzes = $quizRepository->findBy(['user' => $user], ['id' => 'DESC']);
        return $this->render('dashboard.html.twig', [
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
                    return $this->render('create-quiz.html.twig', [
                        'error' => true,
                    ]);
                }
                $code = $request->get('quizcode');
                $quiz->setCode($request->get('quizcode'));
            } else {
                $code = uniqid();
                $quiz->setCode($code);
            }

            $quiz->setUser($this->getUser());
            $entityManager->persist($quiz);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Quiz erfolgreich erstellt!'
            );

            return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
        }

        return $this->render('create-quiz.html.twig', [
            'error' => false,
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
                return $this->render('edit-quiz.html.twig', [
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
        return $this->render('edit-question.html.twig', [
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
                return $this->render('edit-question.html.twig', [
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
        return $this->render('edit-quiz.html.twig', [
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

            if (!$quizRepository->findOneBy(['code' => $request->get('quizCode')])) {
                $quiz->setCode($request->get('quizCode'));
            } else if ($quiz->getCode() !== $request->get('quizCode')){
                return $this->render('edit-quiz.html.twig', [
                    'questions' => $quiz->getQuestions(),
                    'quiz' => $quiz,
                    'error'=> true,
                ]);
            }

            $categoryId = $request->get('category');
            if ($categoryId) {
                $category = $categoryRepository->find($categoryId);
                if ($category) {
                    $quiz->setCategory($category); // Setze die neue Kategorie
                }
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

    #[Route('/leaderboard/{id}', name: 'leaderboard')]
    public function leaderboardAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if (!$quiz) {
            return $this->render('error.html.twig', [
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
        
        return $this->render('statistics.html.twig', [
            'leaderBoardEntries' => $leaderBoardEntries,
            'averageScorePercentage' => $averageScorePercentage,
            'quiz' => $quiz,
            'averageScore' => $averageScore
        ]);
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
            return $this->render('error.html.twig', [
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
        
        return $this->render('answer-stats.html.twig', [
            'sortedEntries' => $sortedEntries,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/create-category', name: 'create-category')]
    public function createCategoryAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('categoryname')) {
            $category = new Category();
            $category->setName($request->get('categoryname'));

            $category->setUser($this->getUser());
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Kategorie erfolgreich erstellt!'
            );
        }

        return $this->render('create-category.html.twig', [
            'error' => false,
        ]);
    }
}