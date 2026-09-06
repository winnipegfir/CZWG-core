<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->seedCourse(
            'introduction-to-air-traffic-services',
            'Learn how Canadian air traffic services are organized, how ATIS information is used, and how VATSIM operations differ from real-world ATC.',
            [
                'air-traffic-services' => [16, null, 'ATS facilities, controller responsibilities, radar theory, transponders, and squawk codes.'],
                'the-atis' => [8, 'academy-assets/audio/introduction-to-air-traffic-services/the-atis/cywg-atis.mp3', 'How ATIS broadcasts work, what they contain, runway selection, and who maintains the ATIS on VATSIM.'],
                'vatsimisms' => [11, null, 'Top-down coverage, virtual airlines, text pilots, accessibility, new pilots, supervisors, and simulated emergencies.'],
            ],
            'Introduction to Air Traffic Services Knowledge Check',
            $this->introQuestions()
        );

        $this->seedCourse(
            'software-setup',
            'Install and configure EuroScope, Winnipeg sector files, vATIS, Audio for VATSIM, Discord, and TeamSpeak for Winnipeg FIR controlling.',
            [
                'initial-program-installation' => [9, null, 'Install EuroScope, download the Winnipeg FIR sector files, and open the CZWG profile.'],
                'euroscope-basic-functions' => [15, null, 'Open ASR displays, use lists and aliases, load METARs, and connect to VATSIM as an observer.'],
                'external-client-walkthrough' => [16, null, 'Install and configure vATIS and Audio for VATSIM, then connect them for a controlling session.'],
                'communication-platforms' => [7, null, 'Set up Discord and TeamSpeak for Winnipeg FIR and VATCAN communication and coordination.'],
            ],
            'Software Setup Knowledge Check',
            $this->softwareQuestions()
        );
    }

    private function seedCourse(string $courseSlug, string $description, array $modules, string $quizTitle, array $questions): void
    {
        $courseId = DB::table('academy_courses')->where('slug', $courseSlug)->value('id');
        if (! $courseId) {
            return;
        }

        DB::table('academy_courses')->where('id', $courseId)->update([
            'description' => $description,
            'updated_at' => now(),
        ]);

        $sortOrder = 1;
        foreach ($modules as $slug => [$slideCount, $audioUrl, $moduleDescription]) {
            DB::table('academy_modules')
                ->where('course_id', $courseId)
                ->where('slug', $slug)
                ->update([
                    'description' => $moduleDescription,
                    'google_slides_url' => null,
                    'slide_count' => $slideCount,
                    'slide_asset_path' => 'academy-assets/slides/'.$courseSlug.'/'.$slug,
                    'audio_url' => $audioUrl,
                    'sort_order' => $sortOrder++,
                    'published' => true,
                    'updated_at' => now(),
                ]);
        }

        $this->seedFinalKnowledgeCheck($courseId, $sortOrder, $quizTitle, $questions);
    }

    private function seedFinalKnowledgeCheck(int $courseId, int $sortOrder, string $quizTitle, array $questions): void
    {
        $finalModule = DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->where('slug', 'final-knowledge-check')
            ->first();

        $moduleId = $finalModule?->id;
        $quizId = $moduleId ? DB::table('academy_quizzes')->where('module_id', $moduleId)->value('id') : null;
        $hasSubmissions = $quizId
            ? DB::table('academy_quiz_submissions')->where('quiz_id', $quizId)->exists()
            : false;

        // Preserve any real attempts from an older/pre-release assessment by archiving that
        // module instead of deleting its submissions. Fresh/local assessments are simply replaced.
        if ($finalModule && $hasSubmissions) {
            DB::table('academy_modules')->where('id', $finalModule->id)->update([
                'slug' => 'archived-final-knowledge-check-'.$finalModule->id,
                'title' => 'Archived Knowledge Check',
                'published' => false,
                'updated_at' => now(),
            ]);
            $moduleId = null;
            $quizId = null;
        }

        if (! $moduleId) {
            $moduleId = DB::table('academy_modules')->insertGetId([
                'course_id' => $courseId,
                'title' => 'Final Knowledge Check',
                'slug' => 'final-knowledge-check',
                'description' => 'Complete this cumulative assessment after reviewing every module in the course.',
                'google_slides_url' => null,
                'slide_count' => 0,
                'slide_asset_path' => null,
                'audio_url' => null,
                'sort_order' => $sortOrder,
                'published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('academy_modules')->where('id', $moduleId)->update([
                'title' => 'Final Knowledge Check',
                'description' => 'Complete this cumulative assessment after reviewing every module in the course.',
                'google_slides_url' => null,
                'slide_count' => 0,
                'slide_asset_path' => null,
                'audio_url' => null,
                'sort_order' => $sortOrder,
                'published' => true,
                'updated_at' => now(),
            ]);
        }

        if (! $quizId) {
            $quizId = DB::table('academy_quizzes')->insertGetId([
                'module_id' => $moduleId,
                'title' => $quizTitle,
                'passing_score' => 80,
                'published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('academy_quizzes')->where('id', $quizId)->update([
                'title' => $quizTitle,
                'passing_score' => 80,
                'published' => true,
                'updated_at' => now(),
            ]);
            DB::table('academy_questions')->where('quiz_id', $quizId)->delete();
        }

        foreach ($questions as $index => $question) {
            $questionId = DB::table('academy_questions')->insertGetId([
                'quiz_id' => $quizId,
                'question' => $question['question'],
                'type' => $question['type'],
                'points' => $question['points'],
                'explanation' => $question['explanation'] ?? null,
                'rubric' => $question['rubric'] ?? null,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($question['answers'] ?? [] as $answerIndex => $answer) {
                DB::table('academy_answers')->insert([
                    'question_id' => $questionId,
                    'answer' => $answer,
                    'is_correct' => $answerIndex === $question['correct'],
                    'sort_order' => $answerIndex + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function introQuestions(): array
    {
        return [
            $this->mc(
                'A pilot at a smaller Canadian airport needs traffic and weather advisory information, but the facility is not issuing ATC clearances. Which ATS facility best matches that role?',
                ['Flight Information Centre (FIC)', 'Flight Service Station (FSS)', 'Area Control Centre (ACC)', 'Clearance Delivery'],
                1,
                'The course describes an FSS as providing advisory services, including traffic and weather information, without issuing ATC clearances.'
            ),
            $this->mc(
                'An IFR departure has been established enroute by Departure/Terminal. Which controller would normally receive the aircraft next?',
                ['Ground', 'Centre', 'Arrival', 'Clearance Delivery'],
                1,
                'Centre provides enroute IFR service after Departure or Terminal establishes the aircraft enroute.'
            ),
            $this->mc(
                'Why can a low-altitude aircraft lose radar coverage even between populated areas?',
                ['Radar coverage is line-of-sight and can be limited by terrain, site spacing, and aircraft altitude', 'SSR is disabled below 18,000 feet', 'Primary radar only works when the aircraft is transmitting', 'Controllers switch radar off outside terminal airspace'],
                0,
                'The slides explain that radar propagates outward and upward and is limited by line-of-sight, terrain, distance between sites, and aircraft altitude.'
            ),
            $this->mc(
                'Which statement correctly distinguishes Primary Surveillance Radar from Secondary Surveillance Radar?',
                ['PSR relies on reflected energy, while SSR interrogates an aircraft transponder and receives a transmitted reply', 'PSR receives transponder replies, while SSR reflects energy from the aircraft', 'Both systems identify an aircraft without a transponder code', 'SSR can only determine position and cannot receive aircraft information'],
                0,
                'PSR detects reflected radio energy; SSR receives a reply transmitted by the aircraft transponder.'
            ),
            $this->mc(
                'A pilot reports having ATIS information MIKE on initial contact. What does the identifier primarily allow ATC to determine?',
                ['That the pilot has received the current version of the airport information', 'That the aircraft is squawking Mode S', 'That the pilot is cleared for the active runway', 'That the flight plan was filed through ACARS'],
                0,
                'ATIS broadcasts use letter identifiers so pilots can tell controllers which version they have received.'
            ),
            $this->mc(
                'The reported wind is 360 degrees at 10 knots. Based on the runway-selection principle taught in the course, which runway would provide the most direct headwind?',
                ['Runway 18', 'Runway 27', 'Runway 36', 'Runway 09'],
                2,
                'Wind direction reports where the wind is coming from, so runway 36 most directly faces a 360-degree wind.'
            ),
            $this->mc(
                'You are controlling Winnipeg Ground and are the only controller online, so you are maintaining the ATIS. Tower later logs on. What does the course require?',
                ['The ATIS must immediately be transferred to Tower', 'The ATIS must immediately be transferred to Centre', 'There is no required transfer; if you choose to transfer it, tell the next controller the current identifier', 'The ATIS must be shut down until Tower requests it'],
                2,
                'The course says there is no requirement for a specific position to hold the ATIS on VATSIM, but the identifier should be passed if responsibility is switched.'
            ),
            $this->mc(
                'You log on as Winnipeg Departure and Tower, Ground, and Clearance Delivery are all unstaffed. Under VATSIM top-down coverage, which positions are you responsible for?',
                ['Departure only', 'Departure and Tower only', 'Departure, Tower, Ground, and Clearance Delivery', 'All positions including Centre'],
                2,
                'Top-down coverage gives a controller responsibility for every unfilled position below their own area of responsibility.'
            ),
            $this->mc(
                'A pilot is connected as receive-only. How should communication work?',
                ['The controller can transmit by voice, while the pilot replies through text', 'Both controller and pilot must use text only', 'The pilot can transmit voice but cannot hear the controller', 'The controller must transfer the pilot to UNICOM'],
                0,
                'Receive-only pilots can hear controller voice transmissions but send their responses by text.'
            ),
            $this->mc(
                'A pilot appears to be deliberately ignoring instructions and still does not contact you after a .contactme request. Which EuroScope command is taught for requesting supervisor assistance?',
                ['.chat', '.wallop', '.clrd', '.QD'],
                1,
                'The VATSIMisms module teaches .wallop for requesting supervisor help and recommends including the callsign and a brief description.'
            ),
            $this->written(
                'You are working a busy position when a pilot declares a simulated emergency. Explain how you would decide whether to accommodate it and what actions may be appropriate if you cannot.',
                [
                    'Evaluates whether the extra workload can be handled while maintaining service to other traffic',
                    'Recognizes that an accepted emergency requires priority handling and coordination with the pilot and adjacent controllers',
                    'Explains that excessive disruption/workload is a valid reason not to accommodate the simulated emergency',
                    'States that the controller may ask the pilot to disconnect or revert the simulated fault when it cannot reasonably be accommodated',
                ],
                'This is manually graded because the answer should demonstrate judgement and connect the workload, priority, coordination, and VATSIM-specific options taught in the slides.'
            ),
            $this->written(
                'A new pilot is struggling to follow instructions but appears to be trying in good faith. Describe how you would handle the situation if workload permits, including at least one useful EuroScope/VATSIM tool or resource from the course.',
                [
                    'Keeps the frequency and traffic situation safe and manageable first',
                    'Uses a patient, learning-focused approach rather than scolding the pilot',
                    'Mentions private chat via .chat and/or providing useful resources off-frequency',
                    'Recognizes the difference between a struggling pilot and a pilot deliberately ignoring instructions',
                ],
                'The slides emphasize helping new pilots when workload permits, using private messages/resources when useful, and keeping deliberate non-compliance separate from honest mistakes.'
            ),
        ];
    }

    private function softwareQuestions(): array
    {
        return [
            $this->mc(
                'You have installed EuroScope and extracted the Winnipeg Starter sector-file package. Which file should you open to launch the Winnipeg configuration?',
                ['CZWG.prf', 'CYWG_GND.asr', 'alias.txt', 'WinnipegProfile.json'],
                0,
                'The Initial Program Installation module instructs students to open the CZWG .prf file after extracting the Starter package.'
            ),
            $this->mc(
                'You want to switch EuroScope to the preset CYWG ground display. Which method matches the course?',
                ['Open the desired .asr file through the Open SCT menu', 'Import the Winnipeg vATIS JSON profile', 'Press F2 and enter CYWG', 'Generate a TeamSpeak token'],
                0,
                'Winnipeg ASR files are preset EuroScope displays and are opened through the Open SCT menu.'
            ),
            $this->mc(
                'In the Departure List, clicking the ASSR field for an aircraft can expose which function?',
                ['Automatic squawk-code assignment', 'Automatic runway selection', 'Automatic ATIS-letter selection', 'Automatic Discord role assignment'],
                0,
                'The course demonstrates the ASSR field opening squawk assignment options, including Auto assign.'
            ),
            $this->mc(
                'Which sequence loads the current CYWG METAR into EuroScope according to the slides?',
                ['Press F2 so .QD appears, type CYWG, then press Enter', 'Press F1, type .ASR CYWG, then press Enter', 'Open alias.txt and select CYWG from a menu', 'Use .wallop CYWG'],
                0,
                'F2 inserts the .QD command; adding the ICAO code and pressing Enter loads that station into the METAR list.'
            ),
            $this->mc(
                'An alias contains a field such as $1 and another field such as $arrrwy. What is the important difference?',
                ['$1 is a manual fillable field, while a named field such as $arrrwy can be populated automatically when EuroScope has the required data', '$1 is always the aircraft callsign, while $arrrwy must always be typed manually', 'Both fields are ignored until the controller reconnects', 'Both fields can only be filled by clicking the aircraft tag'],
                0,
                'Numbered dollar fields are manually fillable and can be tabbed through; named fields may be filled automatically from available EuroScope data.'
            ),
            $this->mc(
                'You are not certified on a controlling position but want to observe Winnipeg Ground traffic. Which setup best follows the course?',
                ['Connect as an approved observer such as WPG_OBS, prime the Ground frequency, and add it in AFV if you want to listen', 'Connect as CYWG_GND but never transmit', 'Connect through vATIS instead of EuroScope', 'Use TeamSpeak as the VATSIM observer connection'],
                0,
                'The module specifically warns students not to log onto uncertified controlling positions and demonstrates using WPG_OBS, priming the frequency, and adding it in AFV for audio.'
            ),
            $this->mc(
                'What format is the pre-built Winnipeg vATIS profile, and where does the course tell you to obtain it?',
                ['A .JSON file from the Winnipeg FIR Controller Dashboard under ATC Resources', 'A .PRF file from the VATCAN TeamSpeak server', 'An .ASR file from the EuroScope Open SCT menu', 'A .TXT file from the VATSIM supervisor client'],
                0,
                'The Winnipeg vATIS profile is downloaded as a JSON file from the Controller Dashboard ATC Resources area.'
            ),
            $this->mc(
                'In the vATIS walkthrough, which pairing correctly describes APRT COND and NOTAMs?',
                ['APRT COND is generally for runway/approach configuration information; NOTAMs is for pilot information such as closures or bird activity', 'APRT COND stores passwords; NOTAMs stores microphone settings', 'APRT COND stores controller rosters; NOTAMs stores flight strips', 'APRT COND stores Discord channels; NOTAMs stores TeamSpeak tokens'],
                0,
                'The course separates runway/approach configuration material in APRT COND from pilot notices such as closures and bird activity in NOTAMs.'
            ),
            $this->mc(
                'AFV shows an unexpected frequency such as 199.998 after you connect. What troubleshooting sequence best matches the slides?',
                ['Verify the EuroScope connection and selected position, then disconnect/reconnect AFV if needed', 'Delete all sector files and reinstall Windows', 'Change the active ATIS identifier', 'Generate a new VATCAN Discord integration'],
                0,
                'The AFV module recommends checking the EuroScope connection/position and reconnecting AFV when necessary.'
            ),
            $this->mc(
                'Why does the course have you generate a VATCAN TeamSpeak access token from the Integrations page?',
                ['It links VATSIM information such as rating and FIR so the server can give the correct access', 'It installs EuroScope automatically', 'It replaces the AFV push-to-talk key', 'It publishes the airport ATIS'],
                0,
                'The TeamSpeak token links the member\'s VATSIM information so the server can assign the appropriate permissions and channels.'
            ),
            $this->written(
                'You are preparing for a Winnipeg controlling session. Explain the distinct roles of EuroScope, vATIS, Audio for VATSIM, Discord, and TeamSpeak in the workflow taught by this course.',
                [
                    'EuroScope is the primary controlling/radar client and loads the Winnipeg profile/displays',
                    'vATIS creates and publishes the airport ATIS',
                    'Audio for VATSIM provides controller voice communications and radio frequencies',
                    'Discord supports network/division/FIR communication and training features',
                    'TeamSpeak is used for VATCAN/controller coordination and access is linked through the integration token',
                ],
                'This question is manually graded because students must correctly connect each application with its operational role rather than simply recognize a program name.'
            ),
            $this->written(
                'A new student says: “EuroScope opens, but I cannot see the Winnipeg layout. I am not certified to control yet, and I want to watch and listen to Winnipeg Ground.” Give a safe setup/troubleshooting sequence using the material from Modules 1 and 2.',
                [
                    'Confirms the Winnipeg Starter sector package was extracted and the CZWG.prf profile was opened',
                    'Opens the appropriate Winnipeg .asr display through the Open SCT menu',
                    'Connects only as an approved observer such as WPG_OBS rather than an uncertified controlling position',
                    'Primes the Winnipeg Ground frequency in EuroScope',
                    'Adds the same frequency in AFV if the student wants to listen to the radio traffic',
                ],
                'A strong answer combines the installation/display steps with the observer-only restriction and the separate AFV audio step.'
            ),
        ];
    }

    private function mc(string $question, array $answers, int $correct, string $explanation): array
    {
        return [
            'question' => $question,
            'type' => 'multiple_choice',
            'points' => 1,
            'answers' => $answers,
            'correct' => $correct,
            'explanation' => $explanation,
        ];
    }

    private function written(string $question, array $rubric, string $explanation): array
    {
        return [
            'question' => $question,
            'type' => 'written',
            'points' => 4,
            'rubric' => implode("\n", array_map(fn ($item) => '• '.$item, $rubric)),
            'explanation' => $explanation,
        ];
    }

    public function down(): void
    {
        foreach (['introduction-to-air-traffic-services', 'software-setup'] as $courseSlug) {
            $courseId = DB::table('academy_courses')->where('slug', $courseSlug)->value('id');
            if (! $courseId) {
                continue;
            }

            DB::table('academy_modules')
                ->where('course_id', $courseId)
                ->update(['slide_count' => 0, 'slide_asset_path' => null, 'audio_url' => null, 'updated_at' => now()]);
        }
    }
};
