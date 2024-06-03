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
    
    public function getRandomMatrikelnumber(Quiz $quiz, EntityManagerInterface $entityManager): string {
        $colors = [
            "Blue", "Red", "Green", "Yellow", "Purple", "Orange",
            "Pink", "Cyan", "Magenta", "Lime", "Indigo", "Violet", 
            "Gray", "Teal", "Turquoise", "Maroon", "Lavender", "Beige",
            "Mint", "Coral", "Navy", "Sky", "Peach", "Olive", "Aqua",
            "Silver", "Gold", "Crimson", "Chartreuse", "Ivory", "Salmon",
            "Plum", "Orchid", "Khaki", "Sapphire", "Amber", "Cerulean",
            "Fuchsia", "Periwinkle", "Amber", "Emerald", "Azure", "Rose",
            "Mint", "Mauve", "Tan", "Mustard", "Burgundy", "Lilac", "Denim"
        ];

        $items = [
            "Fox", "Eagle", "Lion", "Tiger", "Wolf", "Bear", "Giraffe", "Zebra", "Kangaroo",
            "Panda", "Dolphin", "Penguin", "Leopard", "Owl", "Frog", "Rabbit", "Deer", "Horse",
            "Parrot", "Squirrel", "Turtle", "Cat", "Peacock", "Butterfly", "Koala", "Cheetah",
            "Seal", "Otter", "Beaver", "Octopus", "Seahorse", "Chameleon", "Llama", "Alpaca",
            "Platypus", "Porcupine", "Hedgehog", "Bat", "Salamander", "Starfish", "Walrus", "Swan",
            "Pelican", "Flamingo", "Hummingbird", "Woodpecker", "Jellyfish", "Stingray", "Manatee", "Crab",
            "Moose", "Raccoon", "Armadillo", "Gazelle", "Mongoose", "Iguana", "Gecko", "Ferret",
            "Skunk", "Meerkat", "Bison", "Buffalo", "Lynx", "Marmot", "Capybara", "Wallaby",
            "Hyena", "Jackal", "Weasel", "Mink", "Badger", "Wolverine", "Caribou", "Elk",
            "Reindeer", "Coyote", "Jaguar", "Cougar", "Bobcat", "Ocelot", "Serval", "Caracal",
            "Kookaburra", "Emu", "Cassowary", "Aardvark", "Anteater", "Tapir", "Pangolin", "Okapi",
            "Quokka", "Numbat", "Bilby", "Bandicoot", "Kinkajou", "Coati"
        ];

        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);

        do {
            $matrikelnummer = $colors[array_rand($colors)] . $items[array_rand($items)] . rand(10, 99);
        } while ($leaderBoardEntryRepository->findBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer]));

        return $matrikelnummer;
    }

    #[Route('/quiz-initial', name: 'quiz-initial')]
    public function quizInitialAction(Request $request, EntityManagerInterface $entityManager): Response {
        if (!$request->get('code')) {
            return $this->render('error.html.twig', [
            ]);
        }

        $session = $request->getSession();
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);

        if (!$quiz) {
            return $this->render('error.html.twig', [
            ]);
        }

        $matrikelnummer = $this->getRandomMatrikelnumber($quiz, $entityManager);

        $session->set('matrikelnummer', $matrikelnummer);
        $session->set('code', $request->get('code'));
        $session->set('rightIndex', 0);
        $session->set('rightAnswer', false);
        $session->set('rightAnswerText', '');
        $session->set('index', 0);

        return $this->redirectToRoute('quiz-start');
    }

    #[Route('/quiz-start', name: 'quiz-start')]
    public function quizStartAction(Request $request, EntityManagerInterface $entityManager): Response {
        $session = $request->getSession();

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);
        $matrikelnummer = $session->get('matrikelnummer');

        return $this->render('initial.html.twig', [
            'matrikelnummer' => $matrikelnummer,
            'quiz' => $quiz,
        ]);
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

        $rightAnswer = $session->get('rightAnswer');
        $rightAnswerText = $session->get('rightAnswerText');

        if (isset($quiz->getQuestions()[$index]) && $quiz->getQuestions()[$index]->getAnswerRight() == $request->get('answer')) {
            $session->set('rightAnswer', true);
            $rightIndex = $rightIndex + 1;
            $session->set('rightIndex', $rightIndex);
        } else if (isset($quiz->getQuestions()[$index - 1]) && $request->get('answer')) {
            $session->set('rightAnswer', false);
            if ($quiz->getQuestions()[$index]->getAnswerRight() == 1) {
                $session->set('rightAnswerText', $rightAnswerText = $quiz->getQuestions()[$index]->getAnswerOne());
            } else if ($quiz->getQuestions()[$index]->getAnswerRight() == 2) {
                $session->set('rightAnswerText', $rightAnswerText = $quiz->getQuestions()[$index]->getAnswerTwo());
            } else if ($quiz->getQuestions()[$index]->getAnswerRight() == 3) {
                $session->set('rightAnswerText', $rightAnswerText = $quiz->getQuestions()[$index]->getAnswerThree());
            } else if ($quiz->getQuestions()[$index]->getAnswerRight() == 4) {
                $session->set('rightAnswerText', $rightAnswerText = $quiz->getQuestions()[$index]->getAnswerFour());
            }
        }
        
        if ($request->get('answer')) {
            $index = $index + 1;
            $session->set('index', $index);
            return $this->redirectToRoute('quiz');
        }

        if (!isset($quiz->getQuestions()[$index])) {
            $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);
            if (!$leaderBoardEntryRepository->findBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer])) {
                $leaderBoardEntry = new LeaderBoardEntry();

                $leaderBoardEntry->setMatrikelnumber($matrikelnummer);
                $leaderBoardEntry->setQuiz($quiz);
                $leaderBoardEntry->setScore($rightIndex);
            } else {
                $leaderBoardEntry = $leaderBoardEntryRepository->findOneBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer]);
                if ($leaderBoardEntry->getScore() < $rightIndex) {
                    $leaderBoardEntry->setScore($rightIndex);
                }
            }

            $entityManager->persist($leaderBoardEntry);
            $entityManager->flush();

            return $this->redirectToRoute('quiz-finished');
        }

        return $this->render('quiz.html.twig', [
            'quiz' => $quiz,
            'matrikelnummer' => $matrikelnummer,
            'index' => $index,
            'rightIndex' => $rightIndex,
            'rightAnswer'=> $rightAnswer,
            'rightAnswerText' => $rightAnswerText,
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
            'rightAnswerText' => $session->get('rightAnswerText'),
            'leaderBoardEntries' => $leaderBoardEntries,
        ]);
    }
}