<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameExistingAssessments();

        $this->seedCourse('clearance-delivery', [
            'flight-plans' => [23, 'SIDs, mandatory IFR routing, EuroScope departure-list fields, flight-plan editing, and altitude assignment.'],
            'issuing-airways' => [12, 'Issue standard and amended IFR clearances, verify readbacks, issue VFR clearances, and send PDCs.'],
        ], $this->clearanceQuestions());

        $this->seedCourse('ground', [
            'basic-ground-operations' => [26, 'Aerodrome charts, apron operations, runway crossings, taxi instructions, run-ups, traffic management, and airport familiarization.'],
            'advanced-ground-operations' => [21, 'A-SMGCS, tags and primary returns, RIMCAS, CPDLC, and reduced/low visibility ground procedures.'],
        ], $this->groundQuestions());

        $this->seedCourse('tower', [
            'winnipeg-airspace-familiarization' => [13, 'Winnipeg-area control zones, TCA structure, landmarks, St. Andrews airspace, and the CYWG-CYAV corridor.'],
            'ifr-tower-control' => [24, 'Radar releases, IFR and wake-turbulence separation, departures, arrivals, backtracking, and go-arounds.'],
            'vfr-tower-control' => [27, 'VFR departures and arrivals, circuits, sequencing, flight following, traffic information, and controller coordination.'],
            'st-andrews-tower' => [8, 'St. Andrews corridor, runway and circuit preferences, ground operations, and local weather information.'],
        ], $this->towerQuestions());

        $this->seedCourse('advanced-tower', [
            'changing-flight-rules' => [10, 'Pop-up IFR, IFR cancellation and alerting services, composite flight plans, and VFR departures of IFR aircraft.'],
            'visual-contact-approaches' => [7, 'Visual and contact approach requirements, traffic management, and coordination with radar controllers.'],
            'special-operations' => [12, 'Training exercises, simulated approaches, emergencies, helicopters, and Special VFR.'],
            'specialty-towers' => [15, 'Local procedures and restrictions for Saskatoon, Regina, and Thunder Bay Towers.'],
        ], $this->advancedTowerQuestions());
    }

    private function renameExistingAssessments(): void
    {
        // Keep the database's historical/internal "quiz" table names for compatibility,
        // but remove Quiz / Knowledge Check wording from everything students and staff see.
        $assessmentModules = DB::table('academy_modules')
            ->where(function ($q) {
                $q->where('slug', 'final-knowledge-check')
                  ->orWhere('slug', 'final-self-assessment')
                  ->orWhere('title', 'like', '%Knowledge Check%')
                  ->orWhere('title', 'like', '%Quiz%');
            })->get();

        foreach ($assessmentModules as $module) {
            $title = str_ireplace(['Knowledge Check', 'Quiz'], 'Self Assessment', $module->title);
            $updates = [
                'title' => $title,
                'updated_at' => now(),
            ];

            if (in_array($module->slug, ['final-knowledge-check', 'final-self-assessment'], true)) {
                $updates['title'] = 'Final Self Assessment';
                $updates['slug'] = 'final-self-assessment';
                $updates['description'] = 'Complete this cumulative self assessment after reviewing every module in the course.';
            }

            DB::table('academy_modules')->where('id', $module->id)->update($updates);
        }

        foreach (DB::table('academy_quizzes')->get() as $assessment) {
            $title = trim(str_ireplace(['Knowledge Check', 'Quiz'], 'Self Assessment', $assessment->title ?: 'Self Assessment'));
            DB::table('academy_quizzes')->where('id', $assessment->id)->update([
                'title' => $title ?: 'Self Assessment',
                'updated_at' => now(),
            ]);
        }
    }

    private function seedCourse(string $courseSlug, array $modules, array $questions): void
    {
        $course = DB::table('academy_courses')->where('slug', $courseSlug)->first();
        if (! $course) return;

        $sort = 1;
        foreach ($modules as $slug => [$count, $description]) {
            DB::table('academy_modules')->where('course_id', $course->id)->where('slug', $slug)->update([
                'description' => $description,
                'google_slides_url' => null,
                'slide_count' => $count,
                'slide_asset_path' => 'academy-assets/slides/'.$courseSlug.'/'.$slug,
                'audio_url' => null,
                'sort_order' => $sort++,
                'published' => true,
                'updated_at' => now(),
            ]);
        }

        $this->seedAssessment($course, $sort, $questions);
    }

    private function seedAssessment(object $course, int $sort, array $questions): void
    {
        $module = DB::table('academy_modules')->where('course_id', $course->id)
            ->whereIn('slug', ['final-self-assessment', 'final-knowledge-check'])->first();

        $moduleId = $module?->id;
        $quizId = $moduleId ? DB::table('academy_quizzes')->where('module_id', $moduleId)->value('id') : null;
        $hasSubmissions = $quizId ? DB::table('academy_quiz_submissions')->where('quiz_id', $quizId)->exists() : false;

        if ($module && $hasSubmissions) {
            DB::table('academy_modules')->where('id', $module->id)->update([
                'slug' => 'archived-self-assessment-'.$module->id,
                'title' => 'Archived Self Assessment',
                'published' => false,
                'updated_at' => now(),
            ]);
            $moduleId = null;
            $quizId = null;
        }

        if (! $moduleId) {
            $moduleId = DB::table('academy_modules')->insertGetId([
                'course_id' => $course->id,
                'title' => 'Final Self Assessment',
                'slug' => 'final-self-assessment',
                'description' => 'Complete this cumulative self assessment after reviewing every module in the course.',
                'google_slides_url' => null,
                'slide_count' => 0,
                'slide_asset_path' => null,
                'audio_url' => null,
                'sort_order' => $sort,
                'published' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } else {
            DB::table('academy_modules')->where('id', $moduleId)->update([
                'title' => 'Final Self Assessment', 'slug' => 'final-self-assessment',
                'description' => 'Complete this cumulative self assessment after reviewing every module in the course.',
                'sort_order' => $sort, 'published' => true, 'updated_at' => now(),
            ]);
        }

        if (! $quizId) {
            $quizId = DB::table('academy_quizzes')->insertGetId([
                'module_id' => $moduleId,
                'title' => $course->title.' Self Assessment',
                'passing_score' => 80, 'published' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } else {
            DB::table('academy_quizzes')->where('id', $quizId)->update([
                'title' => $course->title.' Self Assessment', 'passing_score' => 80, 'published' => true, 'updated_at' => now(),
            ]);
            DB::table('academy_questions')->where('quiz_id', $quizId)->delete();
        }

        foreach ($questions as $i => $q) {
            $id = DB::table('academy_questions')->insertGetId([
                'quiz_id' => $quizId, 'question' => $q['question'], 'type' => $q['type'], 'points' => $q['points'],
                'explanation' => $q['explanation'] ?? null, 'rubric' => $q['rubric'] ?? null,
                'sort_order' => $i + 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($q['answers'] ?? [] as $j => $answer) {
                DB::table('academy_answers')->insert([
                    'question_id' => $id, 'answer' => $answer, 'is_correct' => $j === $q['correct'],
                    'sort_order' => $j + 1, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function mc(string $question, array $answers, int $correct, string $explanation): array
    { return compact('question','answers','correct','explanation') + ['type'=>'multiple_choice','points'=>1]; }

    private function wr(string $question, array $rubric, string $explanation): array
    { return ['question'=>$question,'type'=>'written','points'=>4,'rubric'=>implode("\n", array_map(fn($x)=>'• '.$x,$rubric)),'explanation'=>$explanation]; }

    private function clearanceQuestions(): array
    {
        return [
            $this->mc('A jet is departing CYWG from Runway 36 and is RNAV capable. Which SID from the course is an appropriate normal selection?', ['WLLYE1','KARIS1','YWG2','MUSIB1'], 1, 'KARIS1 is a Runway 36 jet SID. WLLYE1 is for turboprops, YWG2 is a turboprop SID, and MUSIB1 is for Runway 13.'),
            $this->mc('Which statement correctly describes the Winnipeg 2 (YWG2) departure shown in the course?', ['It is an RNAV SID built entirely from GPS waypoints.','It is a vector SID using an assigned departure heading and an initial climb to 3000 feet.','It is a hybrid SID restricted to jets on Runway 36.','It is a VFR departure route.'], 1, 'The course identifies YWG2 as a vector SID and describes the heading plus initial climb to 3000 feet.'),
            $this->mc('A westbound IFR flight from Winnipeg is filed at FL330. What should Clearance Delivery do under the course altitude rule?', ['Leave FL330 unchanged because all flight levels are valid westbound.','Offer an even flight level such as FL320 or FL340.','Change it to an odd flight level below FL180.','Change the flight rules to VFR.'], 1, 'The course applies even cruising altitudes westbound and odd cruising altitudes eastbound.'),
            $this->mc('An RNAV-capable flight to Regina requires LIVBI DUKPO FAREN before rejoining its original route at YDR. How much of the amended route should be read in the clearance?', ['Only LIVBI.','Only the points that were newly added, stopping before YDR.','The new route through and including YDR, the first unmodified point.','The entire route all the way to Regina every time.'], 2, 'For an amended clearance, the course says to read the changed routing up to and including the first unmodified point.'),
            $this->mc('Which Departure List field contains the assigned transponder code?', ['SID','ALT','ASSR','STS'], 2, 'ASSR is the assigned squawk/transponder-code field.'),
            $this->mc('A pilot reads back the correct route and runway but says squawk 4372 instead of the assigned 4732. What is the best response?', ['Accept it because only the route must be read back correctly.','Correct only the erroneous squawk and require the pilot to acknowledge it.','Issue a completely new clearance with a new SID.','Tell the pilot to contact Ground immediately.'], 1, 'The course specifically stresses correcting readback errors such as an incorrect squawk code.'),
            $this->mc('Ground is online and a pilot has correctly read back an IFR clearance. Which next instruction matches the course?', ['Readback correct; push and start at your discretion; contact Ground when ready to taxi.','Readback correct; taxi immediately to the runway.','Readback correct; monitor Unicom for taxi.','Readback correct; contact Tower for pushback.'], 0, 'When Ground is online, Delivery confirms the readback, allows push/start at discretion, and sends the aircraft to Ground when ready to taxi.'),
            $this->mc('Which statement about a CYWG VFR clearance is correct?', ['It includes a SID and an IFR clearance limit.','It can include a local altitude restriction and a squawk code without an instrument departure.','It always clears the aircraft to 5000 feet.','It requires mandatory IFR routing.'], 1, 'The course example gives a VFR altitude restriction and squawk; VFR aircraft do not use SIDs.'),
            $this->mc('A PDC is being sent to WestJet 271 with squawk 4712. What authenticator does the course method produce?', ['471W','712W','712J','271W'], 1, 'The PDC identifier is the last three digits of the squawk plus the first letter of the callsign: 712W.'),
            $this->mc('Why must a controller be careful when editing a flight plan in EuroScope?', ['The changes follow the aircraft and can affect what other controllers see.','The changes are visible only on the local computer.','Editing automatically cancels the flight plan.','Only the pilot can see the edits.'], 0, 'The course warns that flight-plan edits follow the aircraft and can affect other controllers.'),
            $this->wr('A pilot files an RNAV IFR route that does not comply with the applicable mandatory routing and also files an unsuitable cruising altitude. Describe how you would correct the flight plan and then issue the amended clearance.', ['Identify both the routing and altitude problems before transmitting.','Amend the route using the applicable mandatory points and choose a directionally appropriate altitude.','Advise the pilot that the airways are amended and read the changed routing through the first unmodified point.','Include the remaining clearance elements: destination/limit, SID, runway and squawk, then verify the readback.'], 'Award credit for a logical correction and clearance process using the course material; exact callsigns or route points are not required unless supplied in the scenario.'),
            $this->wr('A pilot gives a readback containing two errors, and Ground is online. Explain how you would handle the readback through to the handoff for taxi.', ['Identify and correct each incorrect clearance element.','Require/confirm a correct readback rather than ignoring an error.','Once correct, advise push and start at the pilot’s discretion.','Direct the aircraft to contact Ground on the appropriate frequency when ready to taxi.'], 'The response should show active readback monitoring, correction, confirmation, and the proper next-controller handoff.'),
        ];
    }

    private function groundQuestions(): array
    {
        return [
            $this->mc('At Winnipeg on VATSIM, an aircraft is moving on an uncontrolled apron. Which statement best describes Ground’s role?', ['Ground provides separation between all aircraft on the apron.','The pilot is responsible for apron separation, although Ground may delay push/start to improve traffic flow.','Ground must issue a taxi clearance for every movement on the apron.','Apron movement is prohibited unless Tower approves it.'], 1, 'The course treats Winnipeg FIR aprons as uncontrolled on VATSIM while allowing Ground to delay push/start when useful.'),
            $this->mc('You need to taxi an aircraft across Runway 31 while Tower is online. What must happen first?', ['Assume the runway is inactive if it is not listed on the ATIS.','Coordinate off-frequency and obtain approval from the overlying controller.','Issue the crossing and advise Tower afterward.','Send the aircraft to Unicom.'], 1, 'Ground must obtain runway-crossing approval from the overlying controller and must not assume a runway is inactive.'),
            $this->mc('Which combination is expected in a normal departure taxi instruction from the course?', ['Runway, altimeter, taxi route, and a hold-short limit.','Only the taxiway letters.','SID, squawk, and destination.','Parking gate and ATIS letter only.'], 0, 'The course examples include the runway, altimeter, taxi route, and end with a hold-short instruction.'),
            $this->mc('A cargo aircraft has landed at Winnipeg and its parking destination is not known. What should Ground do before issuing the full taxi route?', ['Assume Apron 1 because all commercial aircraft park there.','Assume Apron 6 because it is closest to the runway.','Confirm the aircraft’s exact parking destination.','Taxi it to the nearest uncontrolled taxiway and terminate service.'], 2, 'The course notes that general aviation, cargo, and private destinations vary and should be confirmed when unknown.'),
            $this->mc('A pilot requests a run-up on a suitable taxiway before departure. Which response matches the course?', ['Run-up denied; all run-ups must occur on the apron.','Run-up approved; advise when the run-up is complete.','Cleared for takeoff during the run-up.','Contact Delivery for run-up approval.'], 1, 'Ground may approve a suitable run-up and ask the pilot to advise when complete.'),
            $this->mc('On the A-SMGCS simulation, when will a normal tagged aircraft target be available?', ['Whenever the aircraft is connected, regardless of transponder state.','Only when the transponder is on, Mode C is selected, and the assigned code is being squawked.','Only after the aircraft reaches the runway.','Only for arrivals.'], 1, 'The course says tags depend on the transponder being on, in Mode C, and using the assigned squawk.'),
            $this->mc('What does a Primary Radar Return on the ground scope indicate in the course?', ['A fully correlated aircraft with all flight-plan data.','A target without usable secondary-transponder data, often shown as a T with a number.','A closed runway.','A Stage Two RIMCAS warning.'], 1, 'PRRs represent surface targets that are not returning the normal transponder information.'),
            $this->mc('What is the operational difference between RIMCAS Stage One and Stage Two described in the course?', ['Stage One is a caution; Stage Two is a warning requiring timely controller intervention.','Stage One is used only in daylight; Stage Two only at night.','Stage One closes the airport; Stage Two reopens it.','There is no difference.'], 0, 'Stage One is the caution alert; Stage Two is the warning alert where timely intervention is required.'),
            $this->mc('Which statement correctly compares RVOP and LVOP in the Ground course?', ['RVOP allows unrestricted traffic while LVOP only affects aprons.','RVOP restricts the aerodrome to essential movements with positive control; LVOP is more restrictive and limits controlled surfaces to one movement at a time.','They apply only when the ceiling is below 500 feet.','They remove the need to protect ILS critical areas.'], 1, 'The course describes LVOP as the more restrictive regime and requires positive control in both.'),
            $this->mc('During RVOP/LVOP, when should a runway crossing normally be included in a taxi clearance?', ['At the start of the taxi route, before the aircraft reaches the runway.','Only after the aircraft has reached the hold-short line and the crossing can be issued as a separate controlled movement.','Whenever the pilot reports the runway in sight.','Only when the runway is inactive on the ATIS.'], 1, 'The reduced/low visibility procedure in the course specifically avoids including a runway crossing until the aircraft reaches the hold-short point.'),
            $this->wr('During reduced visibility, an aircraft must taxi from an apron, cross a runway, and pass near an ILS critical area. Describe how you would manage the movement from initial taxi to the runway crossing.', ['Use positive/progressive control and the nearest logical route rather than an unnecessarily complex taxi.','Issue a hold-short instruction before the runway instead of including the crossing in the initial clearance.','Obtain/confirm runway-crossing authority before issuing the crossing.','Protect the applicable ILS critical/VFR-IFR holding line and continue monitoring A-SMGCS/RIMCAS information.'], 'Award credit for a safe sequence consistent with the course’s RVOP/LVOP and runway-protection procedures.'),
            $this->wr('Two taxiing aircraft are converging near a runway while an arrival is approaching. Explain how Ground should resolve the surface conflict and coordinate with Tower.', ['Recognize the conflict early and stop or reroute one aircraft using hold-position/hold-short instructions.','Protect the runway and never assume it is inactive.','Coordinate the runway crossing or other runway-related movement with Tower off-frequency.','Only resume movement once the conflict and runway risk are resolved, using clear unambiguous instructions.'], 'The answer should demonstrate proactive ground control, runway protection, and explicit coordination.'),
        ];
    }

    private function towerQuestions(): array
    {
        return [
            $this->mc('Which Winnipeg-area airspace is specifically shown in the course as controlled by Winnipeg Tower above the CYWG Control Zone?', ['Class C TCA from 2000 to 3000 feet.','Class E from 700 AGL to 1999 feet.','Class C TCA from 4000 to 12500 feet.','All Terminal airspace to 12500 feet.'], 0, 'The course diagram identifies the 2000–3000 foot Class C TCA portion as controlled by Tower.'),
            $this->mc('An aircraft flies from CYWG to CYAV through the St. Andrews corridor. What altitude does the course assign in that direction?', ['1800 feet ASL','2000 feet ASL','2500 feet ASL','3000 feet ASL'], 0, 'CYWG to CYAV traffic uses 1800 feet ASL; the reverse direction uses 2000 feet ASL.'),
            $this->mc('Terminal is online and an IFR departure is ready. What must Tower have before issuing takeoff clearance?', ['A radar release/clearance validation from the overlying radar controller.','Only a correct ATIS letter.','A VFR circuit clearance.','A PDC authenticator.'], 0, 'With an overlying radar controller online, an IFR departure requires a radar release before departure.'),
            $this->mc('A release says “valid runway 36 at 1405Z, clearance cancelled if not airborne before 1410Z.” What does this mean?', ['The aircraft may depart any time after 1410Z.','The aircraft may depart no earlier than 1405Z and the release expires if it is not airborne before 1410Z.','The aircraft must land before 1410Z.','The runway is closed from 1405Z to 1410Z.'], 1, 'The release has both a not-before validation time and a cancellation time.'),
            $this->mc('For a departure followed by an arrival, which basic IFR separation condition is presented in the Tower course?', ['The arrival must be at least 2 NM from the threshold and spacing must increase to at least 3 NM within one minute of departure.','The arrival must always be 10 NM from the threshold.','The departure must remain on the runway until the arrival lands.','No separation is required if both aircraft are medium category.'], 0, 'That is the basic departure-then-arrival condition taught in the course.'),
            $this->mc('A Heavy aircraft leads a Light IFR aircraft. What distance separation is shown in the wake-turbulence table?', ['3 NM','4 NM','5 NM','6 NM'], 3, 'The course table shows 6 NM for a Light aircraft following a Heavy when distance separation is used.'),
            $this->mc('Tower offers an intersection departure to a pilot. What additional information must Tower provide?', ['The shortened takeoff distance available.','The aircraft’s fuel endurance.','The destination weather.','The next ATIS identifier.'], 0, 'The course requires the controller to provide the reduced takeoff distance when the controller offers the intersection departure.'),
            $this->mc('A VFR arrival to CYWG is entering Class C and has just been identified. What should Tower provide next as applicable?', ['Winds/altimeter and a control instruction such as a Class C clearance or circuit entry.','An IFR SID.','A radar release from Centre before speaking to the aircraft.','A mandatory airway route.'], 0, 'The VFR arrival sequence in the course includes identification, weather information, and a control instruction/circuit entry.'),
            $this->mc('Several VFR aircraft are in the circuit. Which factor should influence sequencing rather than simply using order of first call?', ['Aircraft ground speed and how the sequence will develop.','The colour of the aircraft.','Whether the pilot is using text or voice.','The aircraft’s destination after landing.'], 0, 'The course explicitly reminds Tower to consider differing groundspeeds when deciding the sequence.'),
            $this->mc('When issuing weather at St. Andrews using Winnipeg weather data, what should the controller make clear?', ['That the winds and altimeter being passed are from Winnipeg.','That St. Andrews has its own CAT II observation.','That weather information is unavailable.','That only the wind may be passed, never the altimeter.'], 0, 'The St. Andrews module says to identify the weather as coming from Winnipeg.'),
            $this->wr('You have one IFR departure awaiting release, an IFR arrival approaching the runway, and two VFR circuit aircraft. Describe a safe Tower plan for sequencing them.', ['Account for the IFR arrival/departure separation requirements and obtain/observe the radar release conditions.','Protect wake turbulence and runway occupancy before clearing the IFR departure or arrival.','Sequence the VFR traffic using a suitable technique such as extending a leg, speed control, tight base, or a 360 when appropriate.','Use clear coordination/communications and avoid issuing conflicting runway clearances.'], 'There is more than one safe sequence. Grade the student on whether the plan is workable, coordinated, and consistent with the course separation concepts.'),
            $this->wr('You are closing Winnipeg Tower while an overlying controller remains online. What information should be included in the handover and how should control be transferred?', ['Pass ATIS identifier and active runway(s), plus relevant NOTAM/critical information.','Include pertinent ground status such as taxiing/cleared aircraft.','Include airborne traffic such as circuits, arrivals/departures, and conflicts.','Ask for questions and explicitly finish the transfer with “Winnipeg Tower is your control” (or equivalent position wording).'], 'Award credit for a complete operational briefing and an explicit transfer of control.'),
        ];
    }

    private function advancedTowerQuestions(): array
    {
        return [
            $this->mc('A VFR aircraft already airborne asks Tower for a pop-up IFR clearance. What is Tower’s normal role in the course?', ['Issue a full enroute IFR clearance without coordination.','Send/coordinate the aircraft with the appropriate overlying IFR/radar controller.','Tell the aircraft IFR is impossible once airborne.','Clear the aircraft into Class A immediately.'], 1, 'Pop-up IFR is normally handled by the appropriate overlying radar controller.'),
            $this->mc('An aircraft cancels IFR while remaining VFR. What additional question must the controller ask?', ['Whether the pilot wants to close the flight plan and terminate alerting services.','Whether the aircraft wants a new SID.','Whether the aircraft wants to enter Class A.','Whether the aircraft can change its registration.'], 0, 'The course requires confirmation of whether the flight plan will be closed, because that also affects alerting services.'),
            $this->mc('Which statement correctly distinguishes ZFR and YFR composite flight plans?', ['ZFR begins VFR then changes to IFR; YFR begins IFR then changes to VFR.','ZFR is IFR only; YFR is VFR only.','Both begin IFR and end IFR.','Both are only for helicopters.'], 0, 'The module defines ZFR as VFR-to-IFR and YFR as IFR-to-VFR.'),
            $this->mc('Before allowing an IFR aircraft to depart VFR and pick up its IFR clearance later, what must Tower do?', ['Confirm VFR weather permits it and coordinate the plan with the overlying radar controller.','Cancel the flight plan permanently.','Issue a contact approach.','Require the pilot to use a published missed approach.'], 0, 'The course requires suitable VFR weather and coordination with the radar controller, who may specify when/where to call.'),
            $this->mc('Which condition is unique to the contact approach description in the course?', ['The pilot must request it and must reasonably expect to continue visually by reference to the ground.','The airport must report at least 3 SM and VFR conditions.','ATC may assign it without a pilot request.','The aircraft must be VFR, not IFR.'], 0, 'A contact approach is IFR, must be pilot-requested, and relies on ground reference with at least 1 SM flight visibility.'),
            $this->mc('A VFR aircraft asks to fly the ILS as a simulated approach. Which phraseology concept is correct?', ['Approve the simulated approach and require the aircraft to remain VFR.','Clear the aircraft for the ILS as IFR.','Convert the flight to IFR automatically.','Deny all simulated approaches.'], 0, 'The course says simulated approaches for VFR aircraft are approved, not “cleared,” and the aircraft must remain VFR.'),
            $this->mc('Which Special VFR request is permitted by the course?', ['ATC suggests SVFR to a pilot when weather drops.','A pilot requests SVFR with at least 1 SM visibility and can remain clear of cloud.','A night departure requests SVFR below 1 SM visibility.','An aircraft uses SVFR to cruise through Class A airspace.'], 1, 'SVFR must be requested by the pilot, requires at least 1 SM and clear of cloud; the course limits night use to arrivals.'),
            $this->mc('At Saskatoon, Dundurn CYR is active above 3000 feet and Runway 15 is in use. Which SID does the specialty module say should be used?', ['YXE8 only','MOVOT1 only','YQR2','YQT9'], 1, 'The Saskatoon module specifies MOVOT1 for Runway 15 when the Dundurn CYR is active above 3000 feet.'),
            $this->mc('What is the special hotspot concern at Regina described in the course?', ['Taxiway K intersects the Runway 26/31 area, so the controller should verify the aircraft is lined up facing the correct runway before departure clearance.','Taxiway C is always controlled by Tower.','Runway 08 has an ILS that must be protected by CAT II procedures.','LAHSO is mandatory on every runway.'], 0, 'The Regina module highlights the Taxiway K/Runway 26-31 intersection and the need to verify correct runway orientation.'),
            $this->mc('Which Thunder Bay restriction is correctly stated?', ['Runway 30 has no authorized IFR approaches, and IFR departures from Runway 12 use the YQT9.','Runway 30 has the only ILS at the airport.','All IFR departures from Runway 12 use MOVOT1.','Runway 07 has no IFR approaches.'], 0, 'The specialty module states no IFR approaches for Runway 30 and YQT9 for IFR departures from Runway 12.'),
            $this->wr('A radar controller proposes a visual or contact approach that may conflict with your Tower traffic. Explain how you would evaluate and coordinate the request before the approach is issued.', ['Identify whether the approach is visual or contact and verify the relevant course requirements.','Assess runway/circuit traffic and whether the approach can be accommodated safely; deny or restrict it if necessary.','Coordinate with the radar controller before the approach clearance is issued, using clear controller-to-controller communication.','If accepted, manage/sequencing the IFR aircraft with suitable restrictions or delay tactics while maintaining required separation/runway safety.'], 'The response should show that Tower has discretion to deny or condition the approach and that coordination occurs before the radar controller issues it.'),
            $this->wr('A pilot requests an unusual operation during a busy period (for example a training manoeuvre, simulated approach, helicopter non-runway departure, or an emergency). Describe the decision-making process you would use.', ['Assess traffic, runway/circuit conflicts, and whether the operation can be conducted safely; delay or deny optional training if necessary.','Apply the operation-specific rule: remain VFR for simulated approaches, use “at your discretion” for helicopter non-runway surfaces, or prioritize an emergency as applicable.','Coordinate with affected controllers when the activity extends outside or affects their airspace/traffic.','Issue clear restrictions/instructions and continue monitoring the operation until the conflict or special condition is resolved.'], 'Accept any one of the course special-operation examples if the student applies the correct rule and demonstrates safe judgment.'),
        ];
    }

    public function down(): void {}
};
