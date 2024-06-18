<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Quiz;
use App\Entity\Question;
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
            return $this->redirectToRoute('create-questions', ['quizId' => $quiz->getId()]);
        }

        return $this->render('create-quiz.html.twig', [
            'error' => false,
        ]);
    }

    #[Route('/create-questions', name: 'create-questions')]
    public function createQuestionsAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('quizId')) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $quiz = $quizRepository->findOneBy(['id' => $request->get('quizId')]);
        }

        if($quiz && $request->get('question')) {
            $question = new Question();
            $question->setText($request->get('question'));

            $question->setAnswerOne($request->get('answer1'));
            $question->setAnswerTwo($request->get('answer2'));
            $question->setAnswerThree($request->get('answer3'));
            $question->setAnswerFour($request->get('answer4'));

            $question->setAnswerRight($request->get('rightAnswer'));

            $question->setQuiz($quiz);

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('create-questions', ['quizId' => $quiz->getId()]);
        }

        return $this->render('create-questions.html.twig', [
            'questions' => $quiz->getQuestions(),
            'quiz' => $quiz,
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
                return $this->render('error-access-denied.html.twig', []);
            }

            $question = new Question();
            $question->setText($request->get('question'));

            $question->setAnswerOne($request->get('answer1'));
            $question->setAnswerTwo($request->get('answer2'));
            $question->setAnswerThree($request->get('answer3'));
            $question->setAnswerFour($request->get('answer4'));

            $question->setAnswerRight($request->get('rightAnswer'));

            $question->setQuiz($quiz);

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
        }
    }

    #[Route('/delete-quiz/{id}', name: 'delete_quiz')]
    public function deleteQuizAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            return $this->render('error-access-denied.html.twig', []);
        }

        $entityManager->remove($quiz);
        $entityManager->flush();

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/delete-question/{id}', name: 'delete_question')]
    public function deleteQuestionAction(Question $question, EntityManagerInterface $entityManager): Response {
        if ($question->getQuiz()->getUser() !== $this->getUser()) {
            return $this->render('error-access-denied.html.twig', []);
        }

        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('edit_quiz', ['id' => $question->getQuiz()->getId()]);
    }

    #[Route('/edit-question/{id}', name: 'edit_question')]
    public function editQuestionAction(Question $question, EntityManagerInterface $entityManager): Response {
        if ($question->getQuiz()->getUser() !== $this->getUser()) {
            return $this->render('error-access-denied.html.twig', []);
        }
        return $this->render('edit-question.html.twig', [
            'question' => $question
        ]);
    }

    #[Route('/update-question', name: 'update_question')]
    public function updateQuestionAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('questionId')) {
            $questionRepository = $entityManager->getRepository(Question::class);
            $question = $questionRepository->findOneBy(['id' => $request->get('questionId')]);
            if ($question->getQuiz()->getUser() !== $this->getUser()) {
                return $this->render('error-access-denied.html.twig', []);
            }
            $question->setText($request->get('question'));

            $question->setAnswerOne($request->get('answer1'));
            $question->setAnswerTwo($request->get('answer2'));
            $question->setAnswerThree($request->get('answer3'));
            $question->setAnswerFour($request->get('answer4'));

            $question->setAnswerRight($request->get('rightAnswer'));

            $entityManager->persist($question);
            $entityManager->flush();
        }
        return $this->redirectToRoute('edit_question', ['id' => $question->getId()]);
    }

    #[Route('/edit-quiz/{id}', name: 'edit_quiz')]
    public function editQuizAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            return $this->render('error-access-denied.html.twig', []);
        }

        return $this->render('edit-quiz.html.twig', [
            'questions' => $quiz->getQuestions(),
            'quiz' => $quiz,
            'error'=> false,
        ]);
    }

    #[Route('/update-quiz', name: 'update_quiz')]
    public function updateQuizAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('quizId')) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $quiz = $quizRepository->findOneBy(['id' => $request->get('quizId')]);
            if ($quiz->getUser() !== $this->getUser()) {
                return $this->render('error-access-denied.html.twig', []);
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

            $entityManager->persist($quiz);
            $entityManager->flush();
        }
        return $this->redirectToRoute('edit_quiz', ['id' => $quiz->getId()]);
    }

    #[Route('/leaderboard/{id}', name: 'leaderboard')]
    public function leaderboardAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if (!$quiz) {
            return $this->render('error.html.twig', [
            ]);
        }

        if ($quiz->getUser() !== $this->getUser()) {
            return $this->render('error-access-denied.html.twig', []);
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
            return $this->render('error-access-denied.html.twig', []);
        }
        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz]);

        foreach ($leaderBoardEntries as $entry) {
            $entityManager->remove($entry);
        }

        $entityManager->flush();

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
            return $this->render('error-access-denied.html.twig', []);
        }

        if (isset($data['isEnabled'])) {
            $quiz->setEnabled((bool) $data['isEnabled']);
            $entityManager->persist($quiz);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }
}