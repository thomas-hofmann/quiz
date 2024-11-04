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
        $firstNames = array(
            "Pascal", "Benjamin", "William", "James", "John",
            "Sara", "Katharina", "Anna-Lena", "Miriam", "Helen",
            "Rylee", "Kennedy", "Peyton", "Serenity", "Taylor",
            "Alexandra", "Melanie", "Allison", "Lauren", "Samantha",
            "Mackenzie", "Gabriella", "Caroline", "Inga", "Genesis",
            "Frieda", "Ella", "Eva", "Charlotte", "Paula",
            "Sarah", "Victoria", "Emily", "Ida", "Isabella",
            "Fiona", "Lucy", "Lisa", "Magdalena", "Anni",
            "Nora", "Leona", "Leonie", "Lara", "Mira",
            "Gianna", "Abigail", "Luna", "Scarlett", "Aria",
            "Daniel", "Carlos", "Adrián", "Fernando", "Manuel",
            "Jorge", "Luis", "Pedro", "Rafael", "Hugo",
            "Iker", "Mario", "Pablo", "Rubén", "Sergio",
            "Carolin", "Tessa", "Esther", "Romy", "Rosa",
            "Maximilian", "Alexander", "Leon", "Paul", "Finn",
            "Gary", "Timothy", "Jose", "Larry", "Kenneth",
            "Ronald", "Scott", "Justin", "Eric", "Stephen",
            "Walter", "Billy", "Austin", "Bruce", "Eugene",
            "Alan", "Howard", "Sophia", "Emma", "Mia",
            "Lukas", "Elias", "Luca", "Liam", "Jonas",
            "Niklas", "Tim", "Ben", "Felix", "Emil",
            "Moritz", "David", "Jan", "Jakob", "Fabian",
            "Hiroshi", "Yuki", "Akira", "Haruto", "Aiko",
            "Ayumi", "Takumi", "Natsumi", "Tatsuya", "Miyuki",
            "Shinji", "Yoko", "Kazuki", "Asuka", "Tsubasa",
            "Keiko", "Daichi", "Mitsuki", "Yuta", "Misaki",
            "Nao", "Rina", "Hayato", "Mei",
            "Simon", "Julian", "Noah", "Tom", "Anton",
            "Philipp", "Oskar", "Theo", "Mats", "Dominik",
            "Julius", "Johannes", "Samuel", "Vincent", "Tobias",
            "Henrik", "Nico", "Jannis", "Louis", "Benedikt",
            "Aurelia", "Carlotta", "Martha", "Raphaela", "Lydia",
            "Lucia", "Greta", "Ava", "Lilly", "Milena",
            "Kevin", "Mark", "Jason", "Jeffrey", "Ryan",
            "Sakura", "Hikari", "Kenji", "Riko", "Haruka",
            "Yuna", "Kaito", "Mio", "Ryota", "Emi",
            "Ren", "Sora", "Naoki", "Yui", "Riku",
            "Michael", "Richard", "Joseph", "Charles", "Thomas",
            "Hazel", "Aurora", "Grace", "Lily", "Zoey",
            "Riley", "Paisley", "Penelope", "Everly", "Layla",
            "Madelyn", "Natalie", "Lillian", "Kinsley", "Naomi",
            "Aaliyah", "Eleanor", "Stella", "Savannah", "Nova",
            "Mika", "Adrian", "Marvin", "Markus", "Till",
            "Sébastien", "Baptiste", "Guillaume", "Maxime", "Romain",
            "Damien", "Alexandre", "Nicolas", "Vincent", "François",
            "Javier", "Diego", "Mateo", "Santiago", "Alejandro",
            "Matthew", "Christopher", "Andrew", "Edward", "Joshua",
            "Diana", "Sofie", "Klara", "Emilie", "Lotta",
            "Marlene", "Fenja", "Franziska", "Nina", "Jule",
            "Lennard", "Christian", "Leonard", "Nils", "Daniel",
            "Lea", "Anna", "Lena", "Laura", "Amelie",
            "Luc", "Pierre", "Mathieu", "Théo", "Antoine",
            "Gabriel", "Louis", "Étienne", "Julien", "Rémi",
            "Luisa", "Clara", "Julia", "Sophie", "Alina",
            "Erik", "Jonathan", "Raphael", "Bastian", "Jona",
            "Brandon", "Gregory", "Jack", "Dennis", "Jerry",
            "Marlon", "Leander", "Robin", "Malte", "Yannick",
            "Anthony", "Robert", "Peter", "Brian", "Steven",
            "Bailey", "Jasmine", "Nevaeh", "Adeline", "Alyssa",
            "Claire", "Violet", "Skylar", "Bella", "Sadie",
        );
        
        $lastNames = array(
            "Müller", "Schmidt", "Schneider", "Fischer", "Weber",
            "Meyer", "Wagner", "Becker", "Schulz", "Hoffmann",
            "Schäfer", "Koch", "Bauer", "Richter", "Klein",
            "Wolf", "Schröder", "Neumann", "Schwarz", "Zimmermann",
            "Braun", "Krüger", "Hofmann", "Hartmann", "Lange",
            "Schmitt", "Werner", "Schmitz", "Krause", "Meier",
            "Lehmann", "Schmid", "Schulze", "Maier", "Köhler",
            "Herrmann", "König", "Mayer", "Huber", "Kaiser",
            "Fuchs", "Peters", "Lang", "Scholz", "Möller",
            "Weiß", "Jung", "Hahn", "Vogel", "Schubert",
            "Roth", "Beck", "Bergmann", "Lorenz", "Kraus",
            "Sauer", "Sommer", "Schuster", "Brandt", "Eckert",
            "Kuhn", "Franke", "Winter", "Voigt", "Haas",
            "Heinrich", "Graf", "Schreiber", "Friedrich", "Günther",
            "Conrad", "Herzog", "Reuter", "Seidel", "Kraft",
            "Böhm", "Thomason", "Ziegler", "Kramer", "Jäger",
            "Voß", "Stein", "Otto", "Ritter", "Rieger",
            "Adam", "Maurer", "Smith", "Johnson", "Williams",
            "Brown", "Jones", "Garcia", "Miller", "Davis",
            "Rodriguez", "Martinez", "Hernandez", "Lopez", "Gonzalez",
            "Wilson", "Anderson", "Taylor", "Moore", "Jackson",
            "Martin", "Lee", "Perez", "Thompson", "White",
            "Harris", "Sanchez", "Clark", "Ramirez", "Lewis",
            "Robinson", "Walker", "Young", "Allen", "King",
            "Wright", "Scott", "Torres", "Nguyen", "Hill",
            "Tanaka", "Yamamoto", "Suzuki", "Watanabe", "Ito",
            "Kobayashi", "Yoshida", "Yamada", "Sato", "Takahashi",
            "Inoue", "Ishikawa", "Morita", "Ikeda", "Kato",
            "Morgan", "Bell", "Murphy", "Bennett", "Wood",
            "Barnes", "Ross", "Henderson", "Coleman", "Jenkins",
            "Perry", "Powell", "Long", "Patterson", "Hughes",
            "Washington", "Butler", "Simmons", "Foster",
            "Gonzales", "Bryant", "Alexander", "Russell", "Griffin",
            "Hayes", "Myers", "Ford", "Hamilton",
            "Graham", "Sullivan", "Wallace", "Woods", "Cole",
            "West", "Jordan", "Owens", "Reynolds", "Fisher",
            "Ellis", "Harrison", "Gibson", "McDonald",
            "Marshall", "Ortiz", "Murray", "Freeman",
            "Wells", "Webb", "Simpson", "Stevens", "Tucker",
            "Porter", "Hunter", "Hicks", "Crawford", "Henry",
            "Nakamura", "Shimizu", "Matsumoto", "Hayashi", "Abe",
            "Kojima", "Nakajima", "Ogawa", "Sasaki", "Hasegawa",
            "Fujita", "Goto", "Okada", "Hirano", "Endo",
            "Miyamoto", "Fujiwara", "Ono", "Nakagawa", "Ishii",
            "Saito", "Ueda", "Ohta", "Matsuda", "Kudo",
            "Flores", "Green", "Adams", "Nelson", "Baker",
            "Hall", "Rivera", "Campbell", "Mitchell", "Carter",
            "Roberts", "Gomez", "Phillips", "Evans", "Turner",
            "Diaz", "Parker", "Cruz", "Edwards", "Collins",
            "Reyes", "Adler", "Bach", "Friedrichs", "Geiger",
            "Ivanov", "Jansen", "Keller", "Larsen",
            "Nikitin", "Olsen", "Petersen", "Quist", "Russo",
            "Svensson", "Unger", "Vasquez", "Wang",
            "Xu", "Yilmaz", "Zhang", "Hansen", 'Maximus',
            "Boyd", "Mason", "Morales", "Kennedy",
            "Warren", "Dixon", "Ramos", "Burns",
            "Gordon", "Shaw", "Holmes", "Rice", "Robertson",
            "Hunt", "Black", "Daniels", "Palmer", "Mills",
            "Nichols", "Grant", "Knight", "Ferguson", "Rose",
        );

        shuffle($firstNames);
        shuffle($lastNames);
        
        $leaderBoardEntryRepository = $entityManager->getRepository(LeaderBoardEntry::class);

        do {
            $matrikelnummer = $firstNames[random_int(0, count($firstNames) - 1)] . $lastNames[random_int(0, count($lastNames) - 1)] . random_int(10, 99);
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

        if (!$quiz->isEnabled()) {
            return $this->render('error-disabled.html.twig', [
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
        $session->set('allAnswers', []);
        $session->set('alert', false);

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

        if (!$quiz || !$quiz->getQuestions()) {
            return $this->render('error.html.twig', [
            ]);
        }

        $rightAnswer = $session->get('rightAnswer');
        $rightAnswersText = $session->get('rightAnswers');

        $playerAnswers = [];
        $rightAnswers = [];
        $correctCount = 0;
        $correctCountPlayer = 0;
        $answerReceived = false;
        $question = null;

        if (isset($quiz->getQuestions()[$index])) {
            $question = $quiz->getQuestions()[$index];
        }

        if ($question) {
            foreach ($question->getAnswers() as $answer) {
                if ($answer->getIsCorrect()) {
                    $correctCount++;
                }
            }
            $requestIndex = 0;
            foreach ($question->getAnswers() as $answer) {
                $requestIndex++;
                if ($answer->getIsCorrect()) {
                    $rightAnswers[] = $answer->getText();
                }
                if ($request->get('answer' . $requestIndex)) {
                    $answerReceived = true;
                    $playerAnswers[] = $request->get('answer' . $requestIndex);
                    if ($question->getAnswer($request->get('answer' . $requestIndex))->getIsCorrect()) {
                        $correctCountPlayer++;
                    } else {
                        $correctCountPlayer--;
                    }
                }
            }
        }
        
        if ($question && $answerReceived && $correctCount == $correctCountPlayer) {
            $session->set('rightAnswer', true);
            $session->set('rightAnswers', $rightAnswers);
            
            $allAnswers = $session->get('allAnswers');
            $allAnswers[] = ['questionId' => $question->getId(), 'answers' => $playerAnswers];
            $session->set('allAnswers', $allAnswers);

            $rightIndex = $rightIndex + 1;
            $session->set('rightIndex', $rightIndex);
        } else if ($question && $answerReceived) {
            $session->set('rightAnswer', false);
            $session->set('rightAnswers', $rightAnswers);

            $allAnswers = $session->get('allAnswers');
            $allAnswers[] = ['questionId' => $question->getId(), 'answers' => $playerAnswers];
            $session->set('allAnswers', $allAnswers);
        }

        if ($answerReceived) {
            $index = $index + 1;
            $session->set('index', $index);
            
            $diff = count($playerAnswers) + $correctCountPlayer;
            if (count($playerAnswers) > 0 && $diff != 0) {
                $session->set('alert', true);
            } else {
                $session->set('alert', false);
            }
            
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

                $leaderBoardEntry->setAllAnswers($session->get('allAnswers'));
            } else {
                $leaderBoardEntry = $leaderBoardEntryRepository->findOneBy(['quiz' => $quiz, 'matrikelnumber' => $matrikelnummer]);
                $leaderBoardEntry->setScore($rightIndex);

                $leaderBoardEntry->setAllAnswers($session->get('allAnswers'));
            }

            $entityManager->persist($leaderBoardEntry);
            $entityManager->flush();

            return $this->redirectToRoute('quiz-finished');
        };

        $questions = $quiz->getQuestions();
        $shuffledQuestions = [];

        foreach ($questions as $question) {
            $answers = [
                ['index' => 1, 'answerObject' => $question->getAnswers()[0]],
                ['index' => 2, 'answerObject' => $question->getAnswers()[1]],
                ['index' => 3, 'answerObject' => $question->getAnswers()[2]],
                ['index' => 4, 'answerObject' => $question->getAnswers()[3]],
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
            'rightAnswers' => $rightAnswersText,
            'alert' => $session->get('alert'),
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

        if (!$quiz || !$quiz->getQuestions()) {
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

        $averageScorePercentage = round(($averageScore / count($quiz->getQuestions())) * 100);

        $veryGoodMessages = [
            "Hervorragende Leistung!",
            "Fantastisch, weiter so!",
            "Exzellente Arbeit!",
            "Großartig! Sie sind auf dem richtigen Weg!",
            "Sehr gut!",
            "Sie haben das großartig gemacht!",
            "Spitzenleistung, weiter so!"
        ];
        
        $warningMessages = [
            "Ganz ordentlich, aber es gibt Raum für Verbesserung!",
            "Nicht schlecht, aber versuchen Sie es noch einmal!",
            "Das ist ein solider Anfang, aber es kann besser werden!",
            "Gute Ansätze, aber ein bisschen mehr Übung wäre hilfreich!",
            "Das geht noch besser!",
            "Sie kommen gut voran, aber noch nicht ganz dort!",
            "Ein paar kleine Fehler, aber das können Sie beheben!"
        ];
        
        $dangerMessages = [
            "Nicht aufgegeben, Sie können es besser!",
            "Es gibt noch viel zu lernen, aber Sie schaffen das!",
            "Lassen Sie sich nicht entmutigen, Übung macht den Meister!",
            "Gehen Sie die Fragen noch einmal durch, das hilft!",
            "Da müssen Sie noch etwas üben!",
            "Jede Übung bringt Sie näher an das Ziel!",
            "Nutzen Sie die Gelegenheit zum Lernen und Wachsen!"
        ];

        // Zufällige Auswahl
        $percentage = ($rightIndex / count($quiz->getQuestions())) * 100;
        $selectedMessage = '';
        if ($percentage >= 70) {
            $selectedMessage = $veryGoodMessages[array_rand($veryGoodMessages)];
        } elseif ($percentage >= 40) {
            $selectedMessage = $warningMessages[array_rand($warningMessages)];
        } else {
            $selectedMessage = $dangerMessages[array_rand($dangerMessages)];
        }

        return $this->render('finished.html.twig', [
            'quiz' => $quiz,
            'allAnswers' => $session->get('allAnswers'),
            'matrikelnummer' => $matrikelnummer,
            'averageScorePercentage' => $averageScorePercentage,
            'averageScore' => $averageScore,
            'rightIndex' => $rightIndex,
            'rightAnswer' => $session->get('rightAnswer'),
            'leaderBoardEntries' => $leaderBoardEntries,
            'alert' => $session->get('alert'),
            'selectedMessage' => $selectedMessage,
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
        $averageScorePercentage = 0;
        if ($numEntries > 0) {
            $averageScore = round($totalScore / $numEntries, 2);
            $averageScorePercentage = round(($averageScore / count($quiz->getQuestions())) * 100);
        }   
        
        return $this->render('leaderboard.html.twig', [
            'leaderBoardEntries' => $leaderBoardEntries,
            'quiz' => $quiz,
            'averageScore' => $averageScore,
            'averageScorePercentage' => $averageScorePercentage
        ]);
    }
}