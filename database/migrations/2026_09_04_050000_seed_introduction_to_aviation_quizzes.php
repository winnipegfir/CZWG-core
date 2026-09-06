<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $course = DB::table('academy_courses')->where('slug', 'introduction-to-aviation')->value('id');
        if (! $course) return;
        $sets = $this->sets();
        foreach ($sets as $slug => $questions) {
            $module = DB::table('academy_modules')->where('course_id', $course)->where('slug', $slug)->value('id');
            if (! $module) continue;
            DB::table('academy_quizzes')->updateOrInsert(['module_id' => $module], ['title' => 'Module Knowledge Check', 'passing_score' => 80, 'published' => true, 'created_at' => now(), 'updated_at' => now()]);
            $quiz = DB::table('academy_quizzes')->where('module_id', $module)->value('id');
            DB::table('academy_questions')->where('quiz_id', $quiz)->delete();
            foreach ($questions as $order => $q) {
                $id = DB::table('academy_questions')->insertGetId(['quiz_id'=>$quiz, 'question'=>$q['q'], 'type'=>$q['type'], 'points'=>$q['type']==='written'?4:1, 'explanation'=>$q['explanation'] ?? null, 'rubric'=>$q['rubric'] ?? null, 'sort_order'=>$order, 'created_at'=>now(), 'updated_at'=>now()]);
                foreach ($q['answers'] ?? [] as $i => $answer) DB::table('academy_answers')->insert(['question_id'=>$id, 'answer'=>$answer, 'is_correct'=>$i===$q['correct'], 'sort_order'=>$i, 'created_at'=>now(), 'updated_at'=>now()]);
            }
        }
    }

    private function sets(): array
    {
        $mc = fn ($q,$answers,$correct,$why) => compact('q','answers','correct') + ['type'=>'multiple_choice','explanation'=>$why];
        $wr = fn ($q,$rubric,$answer) => ['q'=>$q,'type'=>'written','rubric'=>implode("\n",array_map(fn($x)=>'• '.$x,$rubric)),'explanation'=>$answer];
        return [
            'the-basics' => [
                $mc('Which statement best distinguishes ICAO and IATA identifiers?',['ICAO is mainly for ticketing; IATA is used by ATC.','ICAO supports aviation operations and ATC; IATA is commonly used commercially.','ICAO applies only in Canada.','There is no difference.'],1,'ICAO identifiers are operational; IATA codes are commonly passenger-facing commercial identifiers.'),
                $mc('What is the primary purpose of air traffic control?',['Reduce airline fuel costs','Provide a safe, orderly, and efficient flow of air traffic','Repair aircraft','Issue pilot licences'],1,'ATC supports the safe, orderly, and efficient flow of air traffic.'),
                $mc('Which aircraft category normally produces lift using rotating blades?',['Fixed-wing','Rotary-wing','Gliders only','Seaplanes only'],1,'Rotary-wing aircraft produce lift using rotating blades.'),
                $wr('Explain how lift is produced and state one important difference between fixed-wing and rotary-wing aircraft.',['1 point: airflow around an airfoil or blade','1 point: pressure/force difference produces lift','1 point: fixed-wing lift uses airflow over fixed wings','1 point: rotary-wing lift uses rotating blades'],'Accept technically sound equivalent wording.'),
                $wr('A controller sees CYWG and B38M. Explain what each identifier represents and why standardized identifiers are useful in ATC.',['1 point: CYWG is an ICAO aerodrome identifier','1 point: CYWG identifies Winnipeg Richardson International','1 point: B38M is the aircraft-type designator for a Boeing 737 MAX 8','1 point: standards support clear and consistent communication'],'Minor formatting differences are acceptable when meaning is clear.'),
            ],
            'radio-telecommunication' => [
                $mc('Why should a controller listen before beginning a transmission?',['Confirm the frequency is not already in use','Increase transmitter range','Change the callsign','Remove the need for a readback'],0,'Listening first helps prevent transmissions from being stepped on.'),
                $mc('Which description of VHF aviation radio is most accurate?',['Line-of-sight and normally used for routine civil ATC','Only for oceanic communication','Only for military aircraft','It always receives while transmitting'],0,'VHF is the routine civil aviation band and is largely line-of-sight.'),
                $mc('What is the difference between SAY AGAIN and I SAY AGAIN?',['SAY AGAIN requests repetition; I SAY AGAIN introduces a repetition.','SAY AGAIN means wait; I SAY AGAIN means continue.','Both cancel a transmission.','There is no difference.'],0,'The phrases distinguish requesting and providing a repetition.'),
                $wr('Write a concise transmission instructing ACA270 to hold short of Runway 36 on Taxiway Kilo. Assume you are Winnipeg Ground.',['1 point: ACA270 callsign','1 point: clear hold-short instruction','1 point: Runway 36 and Taxiway Kilo','1 point: concise standard phraseology'],'Example: “ACA270, Winnipeg Ground, hold short Runway 36 on Kilo.”'),
                $wr('Compare HF, VHF, and UHF by giving one relevant use or limitation for each, then identify the normal band for routine civil ATC.',['1 point: HF long-range/beyond line of sight','1 point: VHF routine civil ATC/line of sight','1 point: UHF mainly military in this course','1 point: selects VHF for routine civil ATC'],'Equivalent descriptions are acceptable.'),
            ],
            'flight-planning' => [
                $mc('Using the course cruising-altitude rule, which altitude is appropriate for a westbound VFR flight?',['5,000 feet','5,500 feet','6,000 feet','6,500 feet'],3,'The course uses even thousands westbound, plus 500 feet for VFR.'),
                $mc('A VOR radial represents:',['Distance from the station','A magnetic bearing extending outward from the station','Destination runway heading','Satellite-computed groundspeed'],1,'A VOR radial is a magnetic bearing from the station.'),
                $mc('Which statement about a VFR flight plan is correct?',['It is never permitted.','It may not always be required, but opening and closing one provides important safety information.','It automatically changes the flight to IFR.','It removes pilot navigation responsibility.'],1,'A VFR flight plan provides valuable search-and-rescue information and must be managed correctly.'),
                $wr('Compare VFR and IFR in terms of navigation, weather conditions, and use of instruments or published procedures.',['1 point: VFR uses visual references','1 point: VFR requires applicable visual-weather minima','1 point: IFR relies on instruments/navigation aids','1 point: published IFR procedures or minima recognized'],'Accept a clear operational comparison.'),
                $wr('Explain what information a pilot can obtain from VOR/DME and GNSS, and why GNSS waypoints are useful.',['1 point: VOR supplies radial/direction','1 point: DME supplies distance','1 point: GNSS supplies satellite-derived position','1 point: waypoints define route fixes/navigation points'],'GNSS constellation names are optional.'),
            ],
            'airspace' => [
                $mc('Which operation is permitted in Canadian Class A airspace?',['VFR only','IFR only','VFR and IFR without clearance','Any operation without communication'],1,'Class A airspace is IFR only.'),
                $mc('Which Canadian airspace class is uncontrolled?',['Class B','Class C','Class E','Class G'],3,'Class G is uncontrolled airspace.'),
                $mc('Which Class F mapping is correct?',['CYA advisory; CYR restricted; CYD danger','CYA danger; CYR advisory; CYD restricted','CYA restricted; CYR danger; CYD advisory','All three are advisory'],0,'CYA, CYR, and CYD identify advisory, restricted, and danger areas.'),
                $wr('Compare the entry and communication expectations for VFR aircraft in Class C and Class D airspace.',['1 point: Class C requires two-way communication','1 point: Class C requires clearance before entry','1 point: Class D requires two-way communication','1 point: distinguishes Class D VFR clearance expectation/local requirements'],'Grade against the Canadian airspace table in the module.'),
                $wr('Explain what uncontrolled Class G means and identify three pilot responsibilities that still apply.',['1 point: ATC does not provide controlled separation as in controlled airspace','1 point: pilot remains responsible for safe separation/awareness','1 point: applicable rules and weather minima still apply','1 point: another valid duty such as navigation or terrain clearance'],'Do not award credit for claiming no rules apply.'),
            ],
            'aviation-weather' => [
                $mc('Which statement correctly compares a METAR and a TAF?',['A METAR is an observation; a TAF is a forecast.','A METAR is a forecast; a TAF is a pilot report.','Both report only upper winds.','Both are issued only after significant weather begins.'],0,'METAR reports observed aerodrome weather; TAF reports forecast conditions.'),
                $mc('What does 34010G15KT mean?',['Wind from 340° at 10 knots, gusting 15','Wind toward 340° at 15 km/h','Wind variable from 10° to 15°','Calm with a 340-foot ceiling'],0,'Direction is where the wind comes from, followed by speed and gust in knots.'),
                $mc('In a TAF, what is the main difference between TEMPO and FM?',['TEMPO is temporary; FM begins new prevailing conditions from a stated time.','TEMPO cancels the TAF; FM is a pilot report.','TEMPO is only wind; FM is only visibility.','They are interchangeable.'],0,'TEMPO covers temporary fluctuations; FM starts new prevailing conditions.'),
                $wr('Decode: CYWG 271600Z 34010KT 9SM -SHSN BKN013 M02/M03 A3009.',['1 point: Winnipeg, day 27 at 1600 UTC','1 point: wind 340°/10 kt and visibility 9 SM','1 point: light snow showers and broken 1,300 ft AGL','1 point: −2°C/−3°C and altimeter 30.09 inHg'],'Allow equivalent plain-language wording.'),
                $wr('A pilot reports unexpected icing and requests a weather deviation. What belongs in a useful pilot report, and how should the controller respond in the simulation?',['1 point: location','1 point: time, altitude, and aircraft type','1 point: useful weather details such as icing type/intensity','1 point: clear assistance/deviation when traffic permits and proper relay procedure'],'Focus on accurate reporting and safe controller judgment.'),
            ],
        ];
    }

    public function down(): void
    {
        $course = DB::table('academy_courses')->where('slug', 'introduction-to-aviation')->value('id');
        if ($course) {
            $modules = DB::table('academy_modules')->where('course_id', $course)->pluck('id');
            DB::table('academy_quizzes')->whereIn('module_id', $modules)->delete();
        }
    }
};
