<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $this->seedCourse(
                'departure',
                'Departure',
                'Learn radar departure control, SID restrictions, sequencing, non-towered releases, and special departure operations.',
                'fa-plane-departure',
                [
                    ['introduction-to-departure-control', 'Introduction to Departure Control', 'Departure workflow, releases, radar identification, first instructions, and handoffs.', '1j82L2t_eoJcCCUYHRZgGSuLEfiT0tSwX44i8jzjU2jw'],
                    ['sids-and-departure-procedures', 'SIDs & Departure Procedures', 'Vector, RNAV, and hybrid SIDs; published restrictions; headings; direct routing; and climbs.', '1xDc8msA39FrXhCsUFDtKZvyaSlTFHq9sGNeNU916jgE'],
                    ['departure-separation-and-sequencing', 'Departure Separation & Sequencing', 'Build stable departure sequences with lateral, vertical, release, and flow-management tools.', '1wX1fVWzbL6bJU3JRxJUBbOG5bBibtYe5o7Mdq_wS-Zg'],
                    ['non-towered-and-special-departures', 'Non-Towered & Special Departures', 'IFR releases, clearance-cancelled times, airborne pickups, and controlled-airspace transitions.', '10nWquK5nRpSdhQBl2F646r9SG4RroxbToVxur_0kkD8'],
                ],
                $this->departureQuestions()
            );

            $this->seedCourse(
                'arrival',
                'Arrival',
                'Learn arrival planning, STAR and descent management, sequencing, radar vectoring, approaches, and non-towered arrivals.',
                'fa-plane-arrival',
                [
                    ['introduction-to-arrival-control', 'Introduction to Arrival Control', 'Arrival workflow, handoff review, approach expectations, descent planning, and Tower transfer.', '1c9jBkPVn_3rCS2xfmPTSBXMTIzq0T5s3LByO-mqzTb0'],
                    ['stars-and-descent-management', 'STARs & Descent Management', 'STAR structure, published restrictions, vertical planning, speed management, and energy control.', '16Nzw5vGY-yyu6LBRHBR4V4sdRQZFhKmpUT-yZNTGE9c'],
                    ['arrival-sequencing-and-spacing', 'Arrival Sequencing & Spacing', 'Build a runway-ready order using speed, vectors, altitude, track miles, and early recovery.', '1wHZ9ZkKjh3N81a_Cnyxr77HcKZoyxUCMbe3zUZHz7TY'],
                    ['approaches-and-radar-vectoring', 'Approaches & Radar Vectoring', 'Visual, ILS, and RNAV clearances; flyable intercepts; and stable transfers to Tower.', '16-Skzf0vI-1KrZxqHpWjXoBjKoVGd5_yjv4sglSWBBA'],
                    ['non-towered-and-special-arrivals', 'Non-Towered & Special Arrivals', 'Non-towered approach clearances, northern airspace exits, service termination, and IFR cancellation.', '1KHkV-oWSNPZqZPwPe4eOzSO6zLwk25DFJ4-SyL3lH94'],
                ],
                $this->arrivalQuestions()
            );

            $this->seedCourse(
                'terminal',
                'Terminal',
                'Combined terminal operations: the CYWG_APP convention, VFR flight following, block airwork, and holding clearances.',
                'fa-route',
                [
                    ['terminal-operations', 'Terminal Operations', 'Combined-position login, VFR flight following, VFR and IFR block airwork, and holding clearances.', '1SePviLwYt0K0hYqrkvxgr33q1lo9uDzwhhh5cIf-V1E'],
                ],
                $this->terminalQuestions()
            );

            $this->seedCourse(
                'center',
                'Center',
                'Learn Winnipeg Centre airspace, IFR clearances, enroute and airport operations, sectorization, and traffic management.',
                'fa-map-signs',
                [
                    ['winnipeg-centre-airspace-and-operations', 'Winnipeg Centre Airspace & Operations', 'Six specialties, 22 operational sectors, staffing, boundaries, frequencies, and adjacent-unit coordination.', '1gICFFduLXLEjjs0wElsMIEOd3MGlf-9dDwnoABXq16o'],
                    ['centre-ifr-departures-and-clearances', 'Centre IFR Departures & Clearances', 'Clearances at FSS, direct-frequency, remote, and uncontrolled aerodromes plus airborne IFR pickups.', '1xngZeG2AiZyAUnRxwY8msDCJ-gCnzyd_KAh8cFcAbo0'],
                    ['enroute-and-airport-operations', 'Enroute & Airport Operations', 'Separation, handoffs, holds, non-towered approaches, flight following, and service termination.', '1GURnT7N4e4gTkttoXEgF_X6Wlt95sOc0zDzYXceyv2o'],
                    ['top-down-and-sector-management', 'Top-Down & Sector Management', 'Sector splits, high/low coordination, top-down climb limits, EDCTs, and traffic-management priorities.', '1gQHaI8ftOGS1OjL6y7UASRcylQvAjVrOik1ZNjKis8g'],
                ],
                $this->centerQuestions()
            );
        });
    }

    private function seedCourse(string $slug, string $title, string $description, string $icon, array $modules, array $questions): void
    {
        $course = DB::table('academy_courses')->where('slug', $slug)->first();
        if (! $course) {
            return;
        }

        DB::table('academy_courses')->where('id', $course->id)->update([
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'published' => true,
            'updated_at' => now(),
        ]);

        // The previous radar migration created one course-level placeholder module.
        // Reuse it as Module 1 so any existing student progress stays attached.
        $placeholder = DB::table('academy_modules')
            ->where('course_id', $course->id)
            ->where('slug', $slug)
            ->first();
        $firstModule = DB::table('academy_modules')
            ->where('course_id', $course->id)
            ->where('slug', $modules[0][0])
            ->first();

        if ($placeholder && ! $firstModule) {
            DB::table('academy_modules')->where('id', $placeholder->id)->update([
                'slug' => $modules[0][0],
                'updated_at' => now(),
            ]);
        } elseif ($placeholder) {
            DB::table('academy_modules')->where('id', $placeholder->id)->update([
                'published' => false,
                'updated_at' => now(),
            ]);
        }

        foreach ($modules as $index => [$moduleSlug, $moduleTitle, $moduleDescription, $presentationId]) {
            $this->upsertModule(
                $course->id,
                $moduleSlug,
                $moduleTitle,
                $moduleDescription,
                'https://docs.google.com/presentation/d/'.$presentationId.'/embed?start=false&loop=false&delayms=3000',
                $index + 1
            );
        }

        $this->seedAssessment($course->id, count($modules) + 1, $questions);
    }

    private function upsertModule(int $courseId, string $slug, string $title, string $description, string $url, int $sortOrder): void
    {
        $module = DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->where('slug', $slug)
            ->first();

        $values = [
            'title' => $title,
            'description' => $description,
            'google_slides_url' => $url,
            'slide_count' => 0,
            'slide_asset_path' => null,
            'audio_url' => null,
            'sort_order' => $sortOrder,
            'published' => true,
            'updated_at' => now(),
        ];

        if ($module) {
            DB::table('academy_modules')->where('id', $module->id)->update($values);
        } else {
            DB::table('academy_modules')->insert($values + [
                'course_id' => $courseId,
                'slug' => $slug,
                'created_at' => now(),
            ]);
        }
    }

    private function seedAssessment(int $courseId, int $sortOrder, array $questions): void
    {
        $assessmentSlugs = ['final-self-assessment', 'final-knowledge-check', 'self-assessment'];
        $module = DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->whereIn('slug', $assessmentSlugs)
            ->orderBy('id')
            ->first();

        $quizId = $module
            ? DB::table('academy_quizzes')->where('module_id', $module->id)->value('id')
            : null;
        $hasSubmissions = $quizId
            ? DB::table('academy_quiz_submissions')->where('quiz_id', $quizId)->exists()
            : false;

        if ($module && $hasSubmissions) {
            DB::table('academy_modules')->where('id', $module->id)->update([
                'slug' => 'archived-self-assessment-'.$module->id,
                'title' => 'Archived Self Assessment',
                'published' => false,
                'updated_at' => now(),
            ]);
            $module = null;
            $quizId = null;
        }

        if (! $module) {
            $moduleId = DB::table('academy_modules')->insertGetId([
                'course_id' => $courseId,
                'title' => 'Self Assessment',
                'slug' => 'final-self-assessment',
                'description' => 'Complete this cumulative self assessment after reviewing every module in the course.',
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
            $moduleId = $module->id;
            DB::table('academy_modules')->where('id', $moduleId)->update([
                'title' => 'Self Assessment',
                'slug' => 'final-self-assessment',
                'description' => 'Complete this cumulative self assessment after reviewing every module in the course.',
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
                'title' => 'Self Assessment',
                'passing_score' => 80,
                'published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('academy_quizzes')->where('id', $quizId)->update([
                'title' => 'Self Assessment',
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

    private function mc(string $question, array $answers, int $correct, string $explanation): array
    {
        return compact('question', 'answers', 'correct', 'explanation') + ['type' => 'multiple_choice', 'points' => 1];
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

    private function departureQuestions(): array
    {
        return [
            $this->mc('What is Departure responsible for after Tower launches an IFR aircraft?', ['Managing gates and apron traffic', 'Identifying, separating, climbing, routing, and preparing the Centre handoff', 'Issuing the destination landing clearance', 'Closing the flight plan'], 1, 'Departure connects Tower and Centre by identifying, separating, climbing, routing, and transferring an organized aircraft.'),
            $this->mc('A correlated radar tag already shows the correct callsign and route. What does that mean?', ['The aircraft is automatically positively identified', 'The aircraft is cleared direct on course', 'The system linked the return to a flight plan, but approved radar identification is still required', 'Tower has transferred control'], 2, 'Correlation is useful system information, but it is not a substitute for positive radar identification.'),
            $this->mc('A departure checks in without reporting its passing altitude. What should the controller do first?', ['Assume the tag altitude is correct', 'Ask the aircraft to say its passing altitude', 'Immediately clear it to flight level 230', 'Return it to Tower'], 1, 'The course phraseology is to request the passing altitude before completing the identification exchange.'),
            $this->mc('A SID contains a lateral turn restriction. What effect does a climb clearance have on that restriction?', ['It automatically cancels the restriction', 'It cancels it only above 5,000 feet', 'It has no automatic effect; the lateral trigger must still be satisfied', 'It converts the SID to a visual departure'], 2, 'A vertical clearance does not automatically cancel an independent lateral restriction.'),
            $this->mc('A faster departure will follow a slower aircraft on the same route. Which plan is strongest?', ['Launch both and wait for spacing to erode', 'Create extra room before release or use headings and altitude to prevent catch-up', 'Clear both direct and assign the same altitude', 'Transfer both immediately to Centre'], 1, 'Release spacing, headings, and altitude are proactive tools for controlling closure.'),
            $this->mc('When may planned divergence be used as the working separation method?', ['As soon as the flight plans appear to diverge', 'Only after actual tracks establish and maintain the required protected divergence', 'Whenever the aircraft have different callsigns', 'Only after both aircraft reach cruise altitude'], 1, 'The controller must observe the tracks and monitor until the separation method is actually established.'),
            $this->mc('At an aerodrome with FSS or a direct Centre frequency, when is the IFR clearance normally issued?', ['While parked before start', 'While taxiing or lining up', 'Only after takeoff', 'After the first handoff'], 1, 'The Winnipeg procedure normally issues this clearance while the aircraft is taxiing or lining up.'),
            $this->mc('At an FSS/direct-frequency aerodrome where a SID exists, which item should not be added verbally to the IFR clearance?', ['Destination', 'SID and flight-planned route', 'Departure runway', 'A separate altitude instruction'], 3, 'When a SID exists, its vertical instruction is used and a verbal altitude is not added to the clearance.'),
            $this->mc('What is normally required in a ground IFR clearance from a remote aerodrome without FSS?', ['A clearance-cancelled time', 'A visual approach clearance', 'A Tower frequency', 'An unrestricted immediate departure'], 0, 'A remote aerodrome without FSS requires a clearance-cancelled time to protect the release window.'),
            $this->mc('The leader turns late and the faster trailer is closing. What is the best controller response?', ['Wait until separation is lost', 'Act early with a climb stop, stabilizing heading, or rebuilt sequence', 'Clear both on course to reduce workload', 'Transfer the conflict to Centre'], 1, 'Early intervention preserves options and prevents the sequence from becoming a loss of separation.'),
            $this->written('Two Winnipeg departures will use the same route. The lead aircraft climbs slowly and the trailer is faster. Describe your plan from release through handoff.', ['Compare route, runway, performance, wake, and requested levels before release.', 'Choose an order and create sufficient release spacing.', 'Use a heading and/or altitude restriction to prevent catch-up when required.', 'Monitor actual spacing and hand off only when the pair is separated, stable, and predictable.'], 'A complete response should build the sequence before launch, actively monitor it, and deliver a solved problem to Centre.'),
            $this->written('Compare the IFR-clearance method at an aerodrome with FSS or a direct Centre frequency and at a remote aerodrome without FSS.', ['FSS/direct-frequency clearance is normally issued while taxiing or lining up.', 'With a SID, do not add a verbal altitude instruction.', 'Do not normally use a cancel time at the FSS/direct-frequency airport unless conflicting inbound traffic requires it.', 'At the remote no-FSS aerodrome, include route, altitude, code, runway as applicable, and a clearance-cancelled time.'], 'Credit should reflect the different timing, altitude, and cancellation-time rules in the Winnipeg procedure.'),
        ];
    }

    private function arrivalQuestions(): array
    {
        return [
            $this->mc('What is the difference between an approach expectation and an approach clearance?', ['There is no difference', 'An expectation helps the pilot prepare; a clearance authorizes the procedure', 'An expectation authorizes descent below all restrictions', 'A clearance is only advisory'], 1, 'The controller must make the actual approach clearance unmistakable; an expectation only establishes the likely plan.'),
            $this->mc('When is “when ready” descent appropriate?', ['Whenever the aircraft is on a STAR', 'When the pilot may choose the descent point without harming separation or the arrival plan', 'Only after the approach clearance', 'Only in uncontrolled airspace'], 1, 'Pilot-discretion timing is appropriate only when it does not compromise traffic, airspace, or the vertical plan.'),
            $this->mc('Which tool is normally best for an immediate order change or when extra track miles are required?', ['A small speed adjustment', 'A vector', 'An altimeter update', 'A frequency change'], 1, 'Vectors produce an immediate path change and can add known track miles.'),
            $this->mc('Why must arrival spacing be projected to the runway?', ['Radar separation is not required before final', 'A legal interval now can collapse because of groundspeed, wake, or runway occupancy', 'Tower always increases spacing', 'STAR restrictions stop applying on final'], 1, 'Arrival must deliver spacing that survives the final and supports Tower’s runway operation.'),
            $this->mc('Before issuing a visual approach clearance, what visual-reference step is required?', ['The controller must see the aircraft from the Tower', 'The pilot must report the required visual reference, such as the field in sight', 'The aircraft must cancel IFR', 'The aircraft must be below 3,000 feet'], 1, 'The course establishes the visual picture first and issues the visual clearance only after the required report.'),
            $this->mc('Which wording distinction is correct when vectoring to final?', ['ILS and RNAV both use “localizer”', 'ILS uses “localizer”; RNAV uses “final approach course”', 'RNAV uses “localizer”; ILS uses “runway track”', 'Neither clearance identifies the lateral course'], 1, 'The phraseology distinguishes an ILS localizer from an RNAV final approach course.'),
            $this->mc('What are the three jobs of a standard non-towered IFR approach clearance?', ['Name the destination, authorize an approach, and state the CTAF transfer point', 'Assign a gate, runway crossing, and squawk', 'Cancel IFR, close the flight plan, and terminate alerting', 'Issue a SID, altitude, and departure frequency'], 0, 'The clearance names the airport, authorizes an approach, and states when to monitor the local frequency.'),
            $this->mc('In which airspace should a controller not accept an IFR cancellation?', ['Class C and D', 'Class D and E', 'Class A and B', 'Class E only'], 2, 'The course states that IFR cancellation may be accepted in Class C, D, or E, but not in Class A or B.'),
            $this->mc('After accepting IFR cancellation in controlled Class C or D airspace, what should the controller do?', ['Assign squawk 1200 and end all service', 'Retain the discrete code and continue the control service appropriate to the airspace', 'Issue an IFR approach clearance', 'Immediately send the aircraft to UNICOM'], 1, 'The aircraft remains in controlled airspace; retain the code and continue the applicable control service.'),
            $this->mc('What must be stated when an aircraft leaves controlled airspace and surveillance service ends?', ['Only the next frequency', 'The boundary, surveillance-service termination, and the next frequency', 'Only the altimeter', 'The next waypoint and gate'], 1, 'The pilot should hear all three ideas plainly so there is no ambiguity about service status.'),
            $this->written('An arrival is high and fast approaching its runway branch. Explain how you would recover the arrival without destabilizing the sequence.', ['Protect the lower altitude and identify traffic constraints.', 'Use an early, achievable speed reduction when useful.', 'Add track miles only as necessary and keep descent and speed compatible.', 'Advise the likely approach, reassess, and delay the approach clearance until the setup is stable.'], 'The response should solve aircraft energy before the final vector and avoid layered, purposeless instructions.'),
            $this->written('Three aircraft are converging for one runway. Describe how you would choose the order, build spacing, clear the approaches, and transfer the sequence to Tower.', ['Choose a natural leader using position, destination, performance, and runway needs.', 'Use speed for modest corrections and vectors/track miles for immediate or larger corrections.', 'Project closure and runway occupancy so spacing survives to landing.', 'Issue exact, flyable approach clearances and transfer only when established, stable, and coordinated.'], 'A strong answer creates the order early, measures each correction, and gives Tower a predictable runway-ready sequence.'),
        ];
    }

    private function terminalQuestions(): array
    {
        return [
            $this->mc('Which callsign is used when one controller combines Winnipeg Arrival and Departure?', ['CYWG_DEP', 'CYWG_APP', 'WPG_CTR', 'CYWG_TWR'], 1, 'The Winnipeg FIR standard is to log in with the Arrival callsign, CYWG_APP, when both terminal flows are combined.'),
            $this->mc('What must a VFR flight receive before entering Winnipeg Class C airspace?', ['An explicit Class C clearance', 'An IFR clearance', 'A landing clearance', 'A clearance-cancelled time'], 0, 'VFR flight following does not by itself authorize Class C entry; the clearance must be explicit.'),
            $this->mc('A VFR aircraft is cleared to work from 3,500 blocking 5,500 feet within five miles of a location. What must the controller protect?', ['Only its reported altitude', 'The full altitude block and geographic area', 'Only the centre point of the area', 'Airspace above 5,500 feet only'], 1, 'Block airwork reserves the complete vertical block throughout the stated geographic area.'),
            $this->mc('What additional element belongs in an IFR block-airwork clearance?', ['A radio-failure plan stating when to commence an approach', 'A visual circuit direction', 'A takeoff clearance', 'A request to squawk VFR'], 0, 'The radio-failure approach plan is an operational part of the IFR block clearance.'),
            $this->mc('Which element belongs in a published holding clearance?', ['An expect-further-clearance time', 'A gate assignment', 'A STAR cancellation', 'A runway crossing'], 0, 'The published-hold pattern includes the waypoint, routing, “hold as published,” and an EFC time.'),
            $this->mc('What information is required when a hold is not simply “as published”?', ['Only the holding waypoint', 'Enough detail to define the inbound track or radial, turns, altitude, limits as applicable, and EFC time', 'Only the direction of turns', 'Only the expected approach runway'], 1, 'GPS and DME holds must be fully defined rather than relying on an unspecified pattern.'),
            $this->written('A VFR aircraft requests block airwork near Teulon. Describe the information you need and construct the clearance.', ['Confirm callsign/type, position, requested area, and requested altitude block.', 'Define the geographic limit using a distance and location.', 'State both the lower and upper altitude with “blocking” and identify the operation as VFR.', 'Protect the complete geographic and vertical block while the operation is active.'], 'A complete answer may use different safe example values but must define both the area and the full altitude block.'),
            $this->written('Construct a complete holding clearance for an aircraft sent direct to a published hold, then explain what additional items would be required for a non-published GPS or DME hold.', ['Published clearance includes the waypoint, present-position direct routing, “hold as published,” and an EFC time.', 'A GPS hold defines the waypoint, inbound track, turn direction, altitude, and EFC time.', 'A DME hold defines the fix/route, radial or localizer, DME limits, turn direction, altitude, and EFC time.', 'Phraseology clearly distinguishes the published pattern from a controller-defined hold.'], 'Credit should reflect a complete hold definition and an operationally useful EFC time.'),
        ];
    }

    private function centerQuestions(): array
    {
        return [
            $this->mc('How is Winnipeg Centre operationally organized?', ['Four sectors with no specialty groupings', 'Six specialties containing 22 operational sectors', 'One permanent sector for the entire FIR', 'Ten terminal sectors only'], 1, 'The course organizes the FIR into West, East, and North low/high specialties containing 22 operational sectors.'),
            $this->mc('What is the normal vertical division between low and high specialties?', ['10,000 feet', 'FL180', 'FL230', 'FL290'], 3, 'Low specialties control below FL290 and high specialties control FL290 and above unless coordinated otherwise.'),
            $this->mc('Which position is opened first when one controller covers the Winnipeg FIR?', ['WPG_CTR', 'WPG_WH', 'WPG_EH', 'CYWG_APP'], 0, 'WPG_CTR is the initial combined Centre position and covers the full FIR.'),
            $this->mc('At an FSS or direct-Centre-frequency aerodrome with a SID, which clearance method is correct?', ['Issue while parked with a mandatory cancel time and verbal altitude', 'Issue while taxiing or lining up, normally without a cancel time, and omit a separate verbal altitude', 'Wait until airborne in every case', 'Issue only a squawk code'], 1, 'The Winnipeg procedure uses the SID clearance while taxiing/lining up, without a routine cancel time or verbal altitude instruction.'),
            $this->mc('What is required for a remote ground IFR clearance from an aerodrome without FSS?', ['A clearance-cancelled time', 'A Tower release', 'A visual approach', 'No altitude'], 0, 'The cancelled time defines the protected release window at the remote aerodrome.'),
            $this->mc('Before issuing an airborne IFR pickup clearance, what must Centre complete?', ['A landing clearance', 'Positive radar identification', 'A runway inspection', 'A STAR cancellation'], 1, 'Obtain the request and complete an approved radar-identification method before granting IFR.'),
            $this->mc('What in-trail standards are taught for decreasing separation within a sector and before handoff?', ['3 NM within; 5 NM before handoff', '5 NM within; 10 NM before handoff', '10 NM within; 20 NM before handoff', 'No numeric standards'], 1, 'The course teaches at least 5 NM in trail within the sector and 10 NM before handoff unless the receiving unit directs otherwise.'),
            $this->mc('When should a handoff to the next staffed unit be initiated?', ['After the aircraft crosses the boundary', 'No later than 5 NM from the boundary', 'Only after the pilot requests it', 'At least 100 NM from the boundary in every case'], 1, 'Initiating no later than 5 NM from the boundary supports timely acceptance and frequency transfer.'),
            $this->mc('Which service is appropriate for a VFR flight-following aircraft?', ['An IFR approach clearance and IFR vectors', 'Traffic service first come, first served, workload permitting, without issuing an IFR approach clearance', 'Guaranteed separation from all unknown traffic', 'A clearance to enter Class A'], 1, 'VFR flight following is workload-permitting; the controller should not issue an IFR approach clearance to that VFR aircraft.'),
            $this->mc('When Centre is providing top-down terminal coverage, what initial altitude is used for applicable Winnipeg departures filed FL240 or above?', ['10,000 feet', 'FL180', 'FL230', 'The filed altitude immediately'], 2, 'FL230 preserves the terminal layer and supports a predictable handoff if Terminal logs in.'),
            $this->mc('What is an EDCT used for?', ['To assign a destination runway', 'To manage demand with a coordinated departure time and realistic departure window', 'To cancel all inbound traffic', 'To replace the aircraft callsign'], 1, 'An Expect Departure Clearance Time is a traffic-management restriction coordinated with the positions handling the flight.'),
            $this->mc('How is Bison represented in the course sector material?', ['As one of Winnipeg Centre’s 22 operational sectors', 'As overlying airspace shown on the supplied diagram, requiring confirmation of current Edmonton coverage', 'As the Winnipeg terminal arrival sector', 'As an uncontrolled airport'], 1, 'The diagram shows Bison as an overlay; it is not counted among Winnipeg Centre’s 22 operational sectors.'),
            $this->written('A remote no-FSS aerodrome has one IFR arrival inbound and an IFR departure ready. Explain how you would protect the airport and issue the departure clearance.', ['Give the arrival normal priority unless a conflict-free departure can be assured.', 'Apply one-in-one-out protection between the arrival and departure.', 'Use a hold-for-release condition or delay release until the path is protected.', 'Issue the remote clearance with route, altitude, code, and a clearance-cancelled time, then monitor the airborne report.'], 'The response should protect the uncontrolled airport before release and use the remote-aerodrome clearance pattern.'),
            $this->written('Traffic requires Winnipeg Centre to split from a combined position. Explain the staffing sequence, high/low boundary considerations, and how you would transfer the work.', ['Start from WPG_CTR and split High/Low or West/East/North according to demand.', 'Use FL290 as the default High/Low division unless the controllers agree otherwise.', 'Review horizontal and vertical boundaries and coordinate aircraft near them before the split.', 'Transfer tags, frequencies, restrictions, and active plans clearly so no aircraft or delegated airspace is missed.'], 'A complete answer treats sectorization as a workload tool and preserves control and communication through the split.'),
        ];
    }

    public function down(): void
    {
        // Course content is intentionally retained if this deployment is rolled back.
    }
};
