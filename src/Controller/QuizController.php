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
        $prefixes = array(
            "Shadow", "Dragon", "Mystic", "Silent", "Fire",
            "Dark", "Thunder", "Frost", "Storm", "Blade",
            "Night", "Bright", "Crystal", "Ghost", "Steel",
            "Phantom", "Doom", "Light", "Wolf", "Viper",
            "Iron", "Chaos", "Titan", "Wraith", "Mega",
            "Moon", "Soul", "Sun", "Earth", "Flame",
            "Spirit", "Ice", "Wind", "War", "Sky",
            "Void", "Abyss", "Savage", "Star",
            "Ocean", "Super", "Blue", "Blood", "Dread",
            "Nightmare", "Lunar", "Solar", "Radiant", "Grim",
            "Tempest", "Blaze", "Inferno", "Nebula", "Galactic",
            "Ironclad", "Thunderbolt", "Rising", "Serpent", "Lion",
            "Lava", "Water", "Mighty", "Bane", "Red",
            "Vortex", "Zephyr", "Rift", "Blight", "Eclipse",
            "Titanium", "Cobalt", "Onyx", "Emerald", "Ruby",
            "Cosmic", "Mystical", "Astral", "Nebulous", "Celestial",
            "Venomous", "Ethereal", "Voidborn", "Prime", "Solaris",
            "Twilight", "Eternal", "Obsidian", "Golden", "Radiance",
            "Feral", "Runic", "Spectral", "Quantum", "Arcane"
        );
        
        $suffixes = array(
            "Witch", "Slayer", "Mage", "Assassin", "Wizard",
            "Knight", "Warrior", "Sorcerer", "Bringer", "Master",
            "Stalker", "Raven", "Archer", "Rider", "Fist",
            "Striker", "Lord", "Fury", "Guardian", "Venom",
            "Priest", "Seeker", "Walker", "Caller", "Dancer",
            "Shaman", "King", "Sentinel", "Champ", "Defender",
            "Guard", "Ward", "Hunter", "Destroyer", "Commander",
            "Ranger", "Scout", "Invoker", "Conqueror", "Ravager",
            "Protector", "Avenger", "Templar", "Paladin", "Barbarian",
            "Swordsman", "Battlemage", "Marksman", "Necromancer", "Alchemist",
            "Rogue", "Warlord", "Enchanter", "Chieftain", "Juggernaut",
            "Revenant", "Vanguard", "Champion", "Crusader",
            "Monk", "Brawler", "Battler", "Assailant", "Strider",
            "Dreadnought", "Phalanx", "Nomad", "Wanderer", "Survivor",
            "Pathfinder", "Demon", "Lizard", "Herald", "Emissary",
            "Farmer", "Prodigy", "Adept", "Savant", "Virtuoso",
            "Whisperer", "Warden", "Shepherd", "Druid", "Sylvan",
            "Thorn", "Ember", "Gale", "Torrent", "Stonekin",
            "Valkyrie", "Seraph", "Phoenix", "Leviathan", "Kraken",
            "Minotaur", "Sphinx", "Siren", "Enigma", "Specter",
            "Harbinger", "Paradox", "Maelstrom", "Catalyst", "Anomaly",
            "Scourge", "Desolation", "Havoc", "Malice", "Carnage",
            "Warlock", "Witcher", "Invoker", "Astronomer", "Miner",
            "Lich", "Chronomancer", "Illusionist", "Nethermancer",
            "Runemaster", "Elementalist", "Geomancer", "Psychic", "Hexblade",
            "Beastmaster", "Spiritwalker", "Weaver",
        );
        

        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);

        do {
            $matrikelnummer = $prefixes[random_int(0, count($prefixes) - 1)] . $suffixes[random_int(0, count($suffixes) - 1)] . random_int(10, 99);
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
            return $this->render('error.html.twig', [
            ]);
        }

        $session = $request->getSession();
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $request->get('code')]);

        if (!$quiz) {
            return $this->render('error-code.html.twig', [
            ]);
        }

        if (!$session->get('matrikelnummer')) {
            $matrikelnummer = $this->getRandomMatrikelnumber($quiz, $entityManager);
        } else {
            $matrikelnummer = $session->get('matrikelnummer');
        }

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

        if (!$session->get('matrikelnummer') || !$session->get('code')) {
            return $this->render('error.html.twig', [
            ]);
        }

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

        if (!$session->get('matrikelnummer') || !$session->get('code')) {
            return $this->render('error.html.twig', [
            ]);
        }

        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);
        $index = $session->get('index');
        $rightIndex = $session->get('rightIndex');
        $matrikelnummer = $session->get('matrikelnummer');

        if (!$quiz) {
            return $this->render('error.html.twig', [
            ]);
        }

        $rightAnswer = $session->get('rightAnswer');
        $rightAnswerText = $session->get('rightAnswerText');

        if (isset($quiz->getQuestions()[$index]) && $quiz->getQuestions()[$index]->getAnswerRight() == $request->get('answer')) {
            $session->set('rightAnswer', true);
            $rightIndex = $rightIndex + 1;
            $session->set('rightIndex', $rightIndex);
        } else if (isset($quiz->getQuestions()[$index]) && $request->get('answer')) {
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
            $session->set('finished', true);
            if (!$leaderBoardEntryRepository->findBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer])) {
                $leaderBoardEntry = new LeaderBoardEntry();

                $leaderBoardEntry->setMatrikelnumber($matrikelnummer);
                $leaderBoardEntry->setQuiz($quiz);
                $leaderBoardEntry->setScore($rightIndex);
            } else {
                $leaderBoardEntry = $leaderBoardEntryRepository->findOneBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer]);
                $leaderBoardEntry->setScore($rightIndex);
            }

            $entityManager->persist($leaderBoardEntry);
            $entityManager->flush();

            return $this->redirectToRoute('quiz-finished');
        }

        $questions = $quiz->getQuestions();



        $questions = $quiz->getQuestions();
        $shuffledQuestions = [];

        foreach ($questions as $question) {
            $answers = [
                ['index' => 1, 'text' => $question->getAnswerOne()],
                ['index' => 2, 'text' => $question->getAnswerTwo()],
                ['index' => 3, 'text' => $question->getAnswerThree()],
                ['index' => 4, 'text' => $question->getAnswerFour()],
            ];

            shuffle($answers);

            $shuffledQuestions[] = [
                'question' => $question,
                'answers' => $answers
            ];
        }

        return $this->render('quiz.html.twig', [
            'quiz' => $quiz,
            'shuffledQuestions' => $shuffledQuestions,
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

        if (!$session->get('matrikelnummer') || !$session->get('code')) {
            return $this->render('error.html.twig', [
            ]);
        }

        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->findOneBy(['code' => $session->get('code')]);
        $matrikelnummer = $session->get('matrikelnummer');

        if (!$quiz) {
            return $this->render('error.html.twig', [
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
        
        return $this->render('finished.html.twig', [
            'quiz' => $quiz,
            'matrikelnummer' => $matrikelnummer,
            'averageScore' => $averageScore,
            'rightIndex' => $rightIndex,
            'rightAnswer' => $session->get('rightAnswer'),
            'rightAnswerText' => $session->get('rightAnswerText'),
            'leaderBoardEntries' => $leaderBoardEntries,
        ]);
    }

    #[Route('/quiz-leaderboard/{id}', name: 'quiz-leaderboard')]
    public function quizLeaderboardAction(Quiz $quiz, EntityManagerInterface $entityManager): Response {
        if (!$quiz) {
            return $this->render('error.html.twig', [
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
        if ($numEntries > 0) {
            $averageScore = round($totalScore / $numEntries, 2);
        }
        
        return $this->render('leaderboard.html.twig', [
            'leaderBoardEntries' => $leaderBoardEntries,
            'quiz' => $quiz,
            'averageScore' => $averageScore
        ]);
    }
}