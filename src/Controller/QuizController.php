<?php
namespace App\Controller;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Quiz;
use App\Entity\LeaderBoardEntry;
use Doctrine\ORM\EntityManagerInterface;

#[IsGranted('ROLE_USER')]
class QuizController extends AbstractController {
    #[Route('/quiz', name: 'quiz')]
    public function quizAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);
        $matrikelnummer = null;

        if (!$request->get('matrikelnummer')) {
            $matrikelnummer = $session->get('matrikelnummer');
        } else {
            $matrikelnummer = $request->get('matrikelnummer');
        }

        if (!$quiz || !$matrikelnummer) {
            return $this->render('error.html.twig', [
            ]);
        }

        $rightIndex = 0;

        $session->set('matrikelnummer', $matrikelnummer);
        $session->set('rightIndex', $rightIndex);
        
        $index = 0;

        return $this->render('quiz.html.twig', [
            'quiz' => $quiz,
            'matrikelnummer' => $matrikelnummer,
            'index' => $index,
            'rightIndex' => $rightIndex,
        ]);
    }

    #[Route('/list', name: 'list')]
    public function listAction(EntityManagerInterface $entityManager): Response {

        $quizRepository = $entityManager->getRepository(Quiz::class);

        $quizzes = $quizRepository->findAll();
        return $this->render('list.html.twig', [
            'quizzes' => $quizzes
        ]);
    }

    #[Route('/quiz-next', name: 'quiz-next')]
    public function quizNextAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);

        if (!$quiz || !$session->get('matrikelnummer')) {
            return $this->render('error.html.twig', [
            ]);
        }

        $matrikelnummer = $session->get('matrikelnummer');

        $index = $request->get('index');
        $rightIndex = $session->get('rightIndex');

        if ($request->get('answer') && isset($quiz->getQuestions()[$index]) && $quiz->getQuestions()[$index]->getAnswerRight() == $request->get('answer')) {
            $rightIndex = $rightIndex + 1;
            $session->set('rightIndex', $rightIndex);
            return $this->redirectToRoute('quiz-next', ['code' => $quiz->getCode(), 'matrikelnummer' => $matrikelnummer, 'index' => $index, 'rightIndex' => $rightIndex]);
        }

        $index = $index + 1;

        $rightIndex = $session->get('rightIndex');
    
        if (!isset($quiz->getQuestions()[$index])) {
            $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
            if (!$leaderBoardEntryRepository->findBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer])) {
                $leaderBoardEntry = new LeaderBoardEntry();

                $leaderBoardEntry->setMatrikelnumber($matrikelnummer);
                $leaderBoardEntry->setQuiz($quiz);
                $leaderBoardEntry->setScore($rightIndex);
        
                $entityManager->persist($leaderBoardEntry);
                $entityManager->flush();
            }

            return $this->redirectToRoute('quiz-finished', ['code' => $quiz->getCode()]);
        }

        return $this->render('quiz.html.twig', [
            'quiz' => $quiz,
            'matrikelnummer' => $matrikelnummer,
            'index' => $index,
            'rightIndex' => $rightIndex
        ]);
    }

    #[Route('/quiz-finished', name: 'quiz-finished')]
    public function quizFinishedAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);

        if (!$quiz) {
            return $this->render('error.html.twig', [
            ]);
        }

        $matrikelnummer = null;
        if($session->get('matrikelnummer')) {
            $matrikelnummer = $session->get('matrikelnummer');
        }

        $rightIndex = $session->get('rightIndex');

        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
        $leaderBoardEntries = $leaderBoardEntryRepository->findBy(['quiz' => $quiz], ['score' => 'DESC']);
        

        return $this->render('finished.html.twig', [
            'quiz' => $quiz,
            'matrikelnummer' => $matrikelnummer,
            'rightIndex' => $rightIndex,
            'leaderBoardEntries' => $leaderBoardEntries
        ]);
    }

    #[Route('/create-quiz', name: 'create-quiz')]
    public function createQuizAction(Request $request, EntityManagerInterface $entityManager): Response {
        if($request->get('quizname')) {
            $quiz = new Quiz();
            $quiz->setName($request->get('quizname'));
            $code = uniqid();
            $quiz->setCode($code);
            $entityManager->persist($quiz);
            $entityManager->flush();
            return $this->redirectToRoute('create-questions', ['code' => $code]);
        }

        return $this->render('create-quiz.html.twig', [
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
}