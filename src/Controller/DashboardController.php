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
class DashboardController extends AbstractController {
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboardAction(): Response {

        return $this->render('dashboard.html.twig', []);
    }

    #[Route('/list', name: 'list')]
    public function listAction(EntityManagerInterface $entityManager): Response {

        $quizRepository = $entityManager->getRepository(Quiz::class);

        $quizzes = $quizRepository->findAll();
        return $this->render('list.html.twig', [
            'quizzes' => $quizzes
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