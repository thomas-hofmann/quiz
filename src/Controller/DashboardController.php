<?php
namespace App\Controller;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Quiz;
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
        $quizzes = $quizRepository->findBy(['user' => $user]);
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
            return $this->redirectToRoute('create-questions', ['code' => $code]);
        }

        return $this->render('create-quiz.html.twig', [
            'error' => false,
        ]);
    }

    #[Route('/create-questions', name: 'create-questions')]
    public function createQuestionsAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('code')) {
            $quizRepository = $entityManager->getRepository(Quiz::class);
            $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);
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

            return $this->redirectToRoute('create-questions', ['code' => $quiz->getCode()]);
        }

        return $this->render('create-questions.html.twig', [
            'code' => $request->get('code'),
            'questions' => $quiz->getQuestions()
        ]);
    }

    #[Route('/delete-quiz/{id}', name: 'delete_quiz')]
    public function deleteQuizAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if ($quiz->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to delete this quiz.');
        }

        $entityManager->remove($quiz);
        $entityManager->flush();

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/delete-question/{id}', name: 'delete_question')]
    public function deleteQuestionAction(Question $question, EntityManagerInterface $entityManager): Response {
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('edit_quiz', ['id' => $question->getQuiz()->getId()]);
    }

    #[Route('/edit-quiz/{id}', name: 'edit_quiz')]
    public function editQuizAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {

        return $this->render('edit-quiz.html.twig', [
            'code' => $quiz->getCode(),
            'questions' => $quiz->getQuestions()
        ]);
    }

    #[Route('/leaderboard/{id}', name: 'leaderboard')]
    public function leaderboardAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz], ['score' => 'DESC']);
        

        return $this->render('leaderboard.html.twig', [
            'quiz' => $quiz,
            'leaderBoardEntries' => $leaderBoardEntries
        ]);
    }

    #[Route('/clear-leaderboard/{id}', name: 'clear_leaderboard')]
    public function clearLeaderboardAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz]);

        foreach ($leaderBoardEntries as $entry) {
            $entityManager->remove($entry);
        }

        $entityManager->flush();

        return $this->redirectToRoute('dashboard');
    }
}