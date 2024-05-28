<?php
namespace App\Controller;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Quiz;
use App\Entity\LeaderBoardEntry;
use Doctrine\ORM\EntityManagerInterface;

class QuizController extends AbstractController {

    #[Route('/quiz-initial', name: 'quiz-initial')]
    public function quizInitialAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();

        $session->set('matrikelnummer', $request->get('matrikelnummer'));
        $session->set('code', $request->get('code'));
        $session->set('rightIndex', 0);
        $session->set('rightAnswer', false);
        $session->set('index', 0);

        if (!$request->get('code') || !$request->get('matrikelnummer')) {
            return $this->render('error.html.twig', [
            ]);
        }

        return $this->redirectToRoute('quiz');
    }

    #[Route('/quiz', name: 'quiz')]
    public function quizAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();
        $quizRepository = $entityManager->getRepository(Quiz::class);

        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);  
        $index = $session->get('index');
        $rightIndex = $session->get('rightIndex');
        $matrikelnummer = $session->get('matrikelnummer');

        if (!$quiz || !$matrikelnummer) {
            return $this->render('error.html.twig', [
            ]);
        }

        $rightAnswer = false;
        
        if (isset($quiz->getQuestions()[$index]) && $quiz->getQuestions()[$index]->getAnswerRight() == $request->get('answer')) {
            $rightAnswer = true;
            $rightIndex = $rightIndex + 1;
            $session->set('rightIndex', $rightIndex);
        }

        $session->set('rightAnswer', $rightAnswer);
        
        if ($request->get('answer')) {
            $index = $index + 1;
            $session->set('index', $index);
        }

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

            return $this->redirectToRoute('quiz-finished');
        }

        return $this->render('quiz.html.twig', [
            'quiz' => $quiz,
            'matrikelnummer' => $matrikelnummer,
            'index' => $index,
            'rightIndex' => $rightIndex,
            'rightAnswer'=> $rightAnswer,
        ]);
    }

    #[Route('/quiz-finished', name: 'quiz-finished')]
    public function quizFinishedAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);

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
            'rightAnswer' => $session->get('rightAnswer'),
            'leaderBoardEntries' => $leaderBoardEntries
        ]);
    }
}