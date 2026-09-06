<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $courses = [
            ['Introduction to Air Traffic Services','introduction-to-air-traffic-services',1,true,true,['Air Traffic Services','The ATIS','VATSIMisms']],
            ['Introduction to Aviation','introduction-to-aviation',2,true,true,[]],
            ['Software Setup','software-setup',3,true,true,['Initial Program Installation','EuroScope Basic Functions','External Client Walkthrough','Communication Platforms']],
            ['Clearance Delivery','clearance-delivery',4,true,false,['Flight Plans','Issuing Airways']],
            ['Ground','ground',5,true,false,['Basic Ground Operations','Advanced Ground Operations']],
            ['Tower','tower',6,true,false,['Winnipeg Airspace Familiarization','IFR Tower Control','VFR Tower Control','St. Andrews Tower']],
            ['Advanced Tower','advanced-tower',7,true,false,['Changing Flight Rules','Visual & Contact Approaches','Special Operations','Specialty Towers']],
            ['Introduction to Radar','introduction-to-radar',8,true,false,[]],
            ['Departure','departure',9,true,false,[]],
            ['Arrival','arrival',10,true,false,[]],
            ['Terminal','terminal',11,true,false,[]],
            ['Center','center',12,true,false,[]],
        ];

        foreach ($courses as [$title,$slug,$order,$published,$automatic,$modules]) {
            $development = $order > 7;
            DB::table('academy_courses')->updateOrInsert(['slug'=>$slug],[
                'title'=>$title,
                'description'=>$development ? 'In Development — this course will become available later.' : $this->description($slug),
                'icon'=>$development ? 'fa-tools' : 'fa-graduation-cap',
                'sort_order'=>$order,'published'=>$published,'default_enrollment'=>$automatic,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            if ($slug === 'introduction-to-aviation') continue;
            $courseId=DB::table('academy_courses')->where('slug',$slug)->value('id');
            foreach ($modules as $i=>$moduleTitle) {
                $moduleSlug=\Illuminate\Support\Str::slug($moduleTitle);
                DB::table('academy_modules')->updateOrInsert(['course_id'=>$courseId,'slug'=>$moduleSlug],[
                    'title'=>$moduleTitle,'description'=>'Module '.($i+1).' of '.$title.'.',
                    'google_slides_url'=>null,'sort_order'=>$i+1,'published'=>true,
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
            if (!empty($modules)) $this->seedAssessment($courseId,$title,$slug,count($modules)+1);
        }
    }

    private function seedAssessment(int $courseId,string $title,string $slug,int $order): void
    {
        DB::table('academy_modules')->updateOrInsert(['course_id'=>$courseId,'slug'=>'final-knowledge-check'],[
            'title'=>'Final Knowledge Check','description'=>'Complete this cumulative assessment after reviewing every course module.',
            'google_slides_url'=>null,'sort_order'=>$order,'published'=>true,'created_at'=>now(),'updated_at'=>now(),
        ]);
        $moduleId=DB::table('academy_modules')->where('course_id',$courseId)->where('slug','final-knowledge-check')->value('id');
        DB::table('academy_quizzes')->updateOrInsert(['module_id'=>$moduleId],[
            'title'=>$title.' Knowledge Check','passing_score'=>80,'published'=>true,'created_at'=>now(),'updated_at'=>now(),
        ]);
        $quizId=DB::table('academy_quizzes')->where('module_id',$moduleId)->value('id');
        DB::table('academy_questions')->where('quiz_id',$quizId)->delete();
        foreach ($this->questions($slug) as $i=>$q) {
            $questionId=DB::table('academy_questions')->insertGetId([
                'quiz_id'=>$quizId,'question'=>$q['q'],'type'=>$q['type'],'points'=>$q['type']==='written'?4:1,
                'explanation'=>$q['explanation'],'rubric'=>$q['rubric']??null,'sort_order'=>$i+1,'created_at'=>now(),'updated_at'=>now(),
            ]);
            foreach ($q['answers']??[] as $j=>$answer) DB::table('academy_answers')->insert([
                'question_id'=>$questionId,'answer'=>$answer,'is_correct'=>$j===$q['correct'],'sort_order'=>$j+1,'created_at'=>now(),'updated_at'=>now(),
            ]);
        }
    }

    private function questions(string $slug): array
    {
        $mc=fn($q,$a,$c,$e)=>['q'=>$q,'answers'=>$a,'correct'=>$c,'type'=>'multiple_choice','explanation'=>$e];
        $wr=fn($q,$r,$e)=>['q'=>$q,'type'=>'written','rubric'=>implode("\n",array_map(fn($x)=>'• '.$x,$r)),'explanation'=>$e];
        return match($slug) {
            'introduction-to-air-traffic-services'=>[
                $mc('Which description best matches the purpose of air traffic services?',['Selling airline tickets','Supporting the safe, orderly and efficient movement of traffic','Maintaining aircraft engines','Planning airport construction'],1,'ATS supports safe, orderly and efficient traffic flow.'),
                $mc('What is the primary function of an ATIS broadcast?',['Provide routinely updated aerodrome information','Issue pilot licences','Replace all controller instructions','Display flight-plan routes'],0,'ATIS reduces frequency congestion by broadcasting routine information.'),
                $mc('When information on an ATIS changes significantly, what should normally happen?',['Nothing until the next day','The identifying letter should advance and the broadcast be updated','All aircraft disconnect','The airport closes'],1,'A new ATIS version is identified by the next letter.'),
                $wr('Explain how a pilot and controller use the ATIS identifier during initial contact.',['Pilot states the received identifier','Controller confirms whether it is current','Controller provides important changes','Uses concise standard communication'],'Accept equivalent wording showing confirmation and correction of outdated information.'),
                $wr('Describe two ways VATSIM operations differ from real-world ATC and how a controller should adapt.',['One valid network difference','A second valid network difference','One suitable controller adaptation','A second suitable controller adaptation'],'Examples may include top-down coverage, varied pilot experience, text pilots, and simulation limitations.'),
            ],
            'software-setup'=>[
                $mc('Which file is normally opened to start the Winnipeg EuroScope profile?',['A .prf profile file','A video file','A spreadsheet','A web cookie'],0,'The Winnipeg package includes a EuroScope profile file.'),
                $mc('What is an ASR file used for in EuroScope?',['A saved radar or ground display setup','An audio recording','A pilot certificate','A weather forecast'],0,'ASR files load configured EuroScope displays.'),
                $mc('Which client is used to create and transmit the ATIS in this course?',['vATIS','A spreadsheet','Discord only','The web browser only'],0,'vATIS is the FIR ATIS client described in the course.'),
                $wr('Outline the steps required to install the Winnipeg sector files and open the correct profile.',['Downloads current starter release','Extracts it to a known folder','Opens the Winnipeg .prf with EuroScope','Confirms the profile/display loads'],'Equivalent safe installation steps are acceptable.'),
                $wr('Explain the roles of EuroScope, Audio for VATSIM, vATIS, Discord, and TeamSpeak in a controlling session.',['EuroScope controlling display','AFV radio audio','vATIS information broadcast','Discord/TeamSpeak communication and coordination'],'Award credit for each correctly matched purpose.'),
            ],
            'clearance-delivery'=>[
                $mc('What is a SID?',['A predetermined instrument departure procedure','A weather observation','A taxiway inspection','A VFR circuit direction'],0,'A SID is a Standard Instrument Departure.'),
                $mc('Why is mandatory IFR routing checked before issuing a clearance?',['To ensure the cleared route follows applicable published requirements','To choose a passenger gate','To calculate baggage weight','To replace the callsign'],0,'The filed route must be checked against applicable routing requirements.'),
                $mc('Which item belongs in a complete IFR clearance?',['A clearance limit and route','The pilot’s home address','Passenger count for every flight','Aircraft paint colour'],0,'The clearance limit and cleared route are core clearance elements.'),
                $wr('Construct a complete IFR clearance from a supplied flight plan, including the major elements in their proper sequence.',['Clearance limit','SID or route','Altitude instructions','Departure frequency and transponder code'],'Phraseology may vary if every operational element is correct.'),
                $wr('Explain how you would detect and correct an unsuitable filed route before reading the clearance.',['Reviews destination and route','Checks mandatory or preferred routing','Identifies the conflict','Amends and clearly issues the corrected route'],'Look for a logical verification and correction process.'),
            ],
            'ground'=>[
                $mc('What must be included when a taxi route requires an aircraft to cross a runway?',['An explicit runway-crossing instruction','Only the gate number','A weather forecast','A new callsign'],0,'Runway crossings require explicit authorization.'),
                $mc('What does “hold short” require the aircraft to do?',['Stop before the specified holding point','Enter the runway immediately','Change frequency without instruction','Return to the gate'],0,'The aircraft must stop before the specified limit.'),
                $mc('Why are airport hotspots important?',['They identify locations with increased risk of confusion or conflict','They show passenger lounges','They set ticket prices','They replace taxi instructions'],0,'Hotspots require extra attention and clear instructions.'),
                $wr('Issue an unambiguous taxi instruction involving two taxiways and a hold-short restriction.',['Correct callsign','Logical taxi route','Explicit hold-short limit','Concise standard phraseology'],'Use the airport layout supplied in the scenario.'),
                $wr('Describe how Ground should manage a developing taxi conflict and coordinate with Tower when a runway is involved.',['Recognizes the conflict','Stops or reroutes traffic','Protects the runway','Coordinates clearly with Tower'],'Safety and explicit runway protection are essential.'),
            ],
            'tower'=>[
                $mc('Who is responsible for issuing takeoff and landing clearances at a controlled airport?',['Tower','Clearance Delivery','The airline dispatcher','Ground after handoff'],0,'Tower controls runway operations.'),
                $mc('What should Tower do before issuing a takeoff clearance?',['Confirm the runway and required separation are available','Close the flight plan','Delete the ATIS','Ask the aircraft to taxi backward'],0,'The runway environment and applicable separation must be protected.'),
                $mc('A VFR aircraft joining the circuit should receive:',['Clear circuit-joining and sequencing instructions','An oceanic clearance','No information','A baggage assignment'],0,'Tower sequences VFR aircraft with clear circuit instructions.'),
                $wr('Explain how you would sequence one IFR arrival, one IFR departure, and VFR circuit traffic.',['Identifies runway priorities','Applies appropriate separation','Uses delay or sequencing instructions','Coordinates when required'],'Award credit for a safe, workable plan.'),
                $wr('Describe the key local differences a controller must consider when working Winnipeg Tower versus St. Andrews Tower.',['Identifies airspace/aerodrome difference','Identifies traffic or procedure difference','Uses correct local frequency/procedure concepts','Explains how control decisions change'],'Grade using the two local modules and current FIR procedures.'),
            ],
            'advanced-tower'=>[
                $mc('Who must request a contact approach?',['The pilot','The tower controller on the pilot’s behalf','Airport operations','Any nearby aircraft'],0,'A contact approach must be requested by the pilot.'),
                $mc('After an IFR cancellation, what should the controller clarify about the flight plan?',['Whether it will be closed and alerting services terminated','The aircraft paint scheme','The passenger fare','The gate agent’s name'],0,'The controller confirms whether the flight plan and alerting services are being closed.'),
                $mc('How should a simulated instrument approach by a VFR aircraft be described?',['Approved while the aircraft remains VFR','Cleared as an IFR approach','Automatically denied','Converted to Class A operations'],0,'A VFR simulated approach is approved, not issued as an IFR clearance.'),
                $wr('Compare visual and contact approaches, including who may initiate each and the required visual conditions.',['Visual approach conditions','Contact approach conditions','Pilot-request rule for contact approach','Operational difference clearly explained'],'Use the standards presented in the course.'),
                $wr('Describe how you would evaluate and coordinate an unusual tower operation without compromising other traffic.',['Assesses traffic and separation','May delay or deny the request','Coordinates with affected controller','Issues clear restrictions or instructions'],'Examples include training manoeuvres, helicopters, or non-standard operations.'),
            ],
            default=>[],
        };
    }

    private function description(string $slug): string
    {
        return match($slug) {
            'introduction-to-air-traffic-services'=>'An introduction to air traffic services, ATIS information, and VATSIM operations.',
            'introduction-to-aviation'=>'An introduction to aviation fundamentals for new Winnipeg FIR students.',
            'software-setup'=>'Install and configure the software and communication tools used by Winnipeg FIR controllers.',
            'clearance-delivery'=>'Learn flight-plan review, departure procedures, routing, and IFR clearance delivery.',
            'ground'=>'Learn safe airport surface operations, taxi instructions, and runway coordination.',
            'tower'=>'Learn IFR and VFR tower control, sequencing, and Winnipeg-area procedures.',
            'advanced-tower'=>'Develop advanced tower judgment for special approaches, operations, and airports.',
            default=>'Winnipeg FIR Academy course.',
        };
    }

    public function down(): void
    {
        DB::table('academy_courses')->whereIn('slug',[
            'introduction-to-air-traffic-services','software-setup','clearance-delivery','ground','tower','advanced-tower',
            'introduction-to-radar','departure','arrival','terminal','center',
        ])->delete();
    }
};
