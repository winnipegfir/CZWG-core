<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->seedIntroToAts();
        $this->seedSoftwareSetup();
    }

    private function seedIntroToAts(): void
    {
        $courseId = DB::table('academy_courses')->where('slug', 'introduction-to-air-traffic-services')->value('id');
        if (! $courseId) {
            return;
        }

        DB::table('academy_courses')->where('id', $courseId)->update([
            'description' => 'Learn how Canadian air traffic services are organized, how ATIS information is used, and how VATSIM operations differ from the real world.',
            'updated_at' => now(),
        ]);

        $modules = [
            'air-traffic-services' => [
                'title' => 'Air Traffic Services',
                'description' => 'ATS facilities, controller responsibilities, radar theory, transponders, and squawk codes.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Define Air Traffic Services (ATS).</li>
            <li>Describe the responsibilities of Clearance Delivery, Ground, Tower, Departure, Arrival/Terminal, and Centre.</li>
            <li>Explain the basics of primary and secondary radar, transponders, and squawk codes.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>What are Air Traffic Services?</h2>
        <p><strong>Air Traffic Services (ATS)</strong> is the broad term for the services provided by air traffic controllers and flight service specialists. In Canada, these services are provided through towers, Area Control Centres (ACCs), Flight Service Stations (FSS), and Flight Information Centres (FIC).</p>
        <div class="academy-info-grid">
            <div class="academy-info-card"><h4>Towers</h4><p>Provide airport control services, including clearance, taxi, runway, and local traffic functions.</p></div>
            <div class="academy-info-card"><h4>Area Control Centres</h4><p>Provide radar-based services through Departure, Arrival/Terminal, and Centre positions.</p></div>
            <div class="academy-info-card"><h4>Flight Service Stations</h4><p>Provide advisory services at smaller airports. They do not issue ATC clearances; they give traffic and weather information so pilots can make informed decisions.</p></div>
            <div class="academy-info-card"><h4>Flight Information Centres</h4><p>Provide enroute flight information, pre-flight briefings, and flight-planning services.</p></div>
        </div>
        <div class="academy-callout"><strong>Winnipeg FIR note:</strong> FSS and FIC services are useful background knowledge, but they are not simulated as controller positions in the Winnipeg FIR Academy.</div>
    </section>

    <section class="academy-content-section">
        <h2>Controller duties</h2>
        <p>For most commercial flights in Canada, pilots communicate with ATC through several positions. Controllers work together to maintain a <strong>safe, orderly, and expeditious</strong> flow of traffic.</p>
        <div class="academy-flow">
            <span>Clearance Delivery</span><i class="fas fa-arrow-right"></i><span>Ground</span><i class="fas fa-arrow-right"></i><span>Tower</span><i class="fas fa-arrow-right"></i><span>Departure</span><i class="fas fa-arrow-right"></i><span>Centre</span><i class="fas fa-arrow-right"></i><span>Arrival</span><i class="fas fa-arrow-right"></i><span>Tower</span><i class="fas fa-arrow-right"></i><span>Ground</span>
        </div>
        <p class="academy-muted">On VATSIM, new Winnipeg controllers generally progress from Clearance Delivery upward through the control positions. Real-world controller careers do not necessarily follow that sequence.</p>

        <h3>Clearance Delivery</h3>
        <p>Clearance Delivery is normally the first ATC point of contact for an IFR aircraft. The controller issues the aircraft's IFR route clearance before taxi, checks the flight plan for correctness, selects the appropriate Standard Instrument Departure (SID) and departure runway, and performs any needed flight-plan coordination.</p>
        <p>Clearances may be issued verbally or through an ACARS-based <strong>Pre-Departure Clearance (PDC)</strong>. Winnipeg primarily uses verbal clearances, although pilots may request a PDC. Clearance Delivery is physically located in the tower in real operations.</p>

        <h3>Ground</h3>
        <p>Ground safely routes aircraft on taxiways between aprons and runways. When an aircraft must cross an active runway, Ground coordinates with Tower. Arriving aircraft normally contact Ground after vacating the runway, while departing aircraft are transferred to Tower as they approach their departure runway.</p>
        <p>At smaller airports, Ground may also perform the Clearance Delivery function.</p>

        <h3>Tower</h3>
        <p>Tower is responsible for runway operations. Tower sequences arrivals and departures, protects separation, and selects the active runway using current and forecast weather. Tower primarily controls by visual reference, although a radar feed may also be available.</p>
        <p>At smaller airports, Tower may also perform Ground and Clearance Delivery functions.</p>

        <h3>Departure, Arrival, and Terminal</h3>
        <p>Departure and Arrival controllers work within an ACC and use radar to separate and sequence traffic. When Departure and Arrival are combined on one position, the position is called <strong>Terminal</strong>. These services are primarily provided to IFR aircraft, although VFR pilots may receive advisory services.</p>

        <h3>Centre</h3>
        <p>Centre controllers provide IFR services to enroute aircraft, including high-level cruise traffic between origin and destination airports. After Departure or Terminal establishes an aircraft enroute, it is normally handed to Centre. Near destination, Centre transfers the aircraft to Arrival or Terminal for descent and approach.</p>
    </section>

    <section class="academy-content-section">
        <h2>Radar theory</h2>
        <p>Without radar, controllers rely more heavily on procedural separation and pilot position reports. This increases controller workload and reduces how closely traffic can safely be spaced. Radar surveillance allows controllers to determine aircraft position more directly and can support greater traffic capacity.</p>
        <p>An <strong>Area Surveillance Radar (ASR)</strong> combines two systems: <strong>Primary Surveillance Radar (PSR)</strong> and <strong>Secondary Surveillance Radar (SSR)</strong>.</p>
        <div class="academy-callout"><strong>Radar is line-of-sight.</strong> Its coverage propagates outward and upward, so terrain, buildings, radar-site spacing, and low aircraft altitude can create coverage gaps. This is particularly important in remote and northern parts of Canada.</div>

        <h3>Primary Surveillance Radar (PSR)</h3>
        <p>PSR transmits radio energy. When that energy strikes a hard surface such as an aircraft, part of it is reflected back to the radar receiver. The radar system can use those returns to determine a target's position, direction, and speed.</p>
        <p><strong>PSR does not tell the controller who the aircraft is.</strong> It detects the target, but identification requires additional information.</p>

        <h3>Transponders and squawk codes</h3>
        <p>Most aircraft carry a transponder. Controllers assign a four-digit <strong>squawk code</strong> using digits 0 through 7. The pilot enters that code into the transponder, allowing the SSR return to be associated with the aircraft's flight plan.</p>
        <p>Some codes are reserved for emergency situations, including <strong>7500, 7600, and 7700</strong>.</p>

        <h3>Secondary Surveillance Radar (SSR)</h3>
        <p>SSR works differently from PSR. A ground interrogator sends an <strong>interrogator signal</strong>; an aircraft transponder receives it and replies with an <strong>identifying signal</strong>. Because SSR receives a transmission from the aircraft rather than relying on reflected energy, it generally has greater range than primary radar.</p>
        <div class="academy-info-grid academy-info-grid-3">
            <div class="academy-info-card"><h4>Mode A</h4><p>Transponder code.</p></div>
            <div class="academy-info-card"><h4>Mode C</h4><p>Transponder code and aircraft altitude.</p></div>
            <div class="academy-info-card"><h4>Mode S</h4><p>Transponder code, aircraft altitude, registration, and speed.</p></div>
        </div>
        <p>Most aircraft in Canada operate with Mode C capability.</p>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Module complete</strong><span>You should now understand the ATS structure and the radar concepts that support later controller training.</span></div></div>
</div>
HTML,
            ],
            'the-atis' => [
                'title' => 'The ATIS',
                'description' => 'How ATIS broadcasts work, what they contain, runway selection, and who maintains the ATIS on VATSIM.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Explain the purpose of the Automatic Terminal Information Service (ATIS).</li>
            <li>Interpret the major information contained in an ATIS.</li>
            <li>Select an appropriate active runway from the wind.</li>
            <li>Understand who may maintain the ATIS on VATSIM.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>Automatic Terminal Information Service</h2>
        <p>The <strong>Automatic Terminal Information Service (ATIS)</strong> is a continuous broadcast of airport information on a dedicated frequency. Pilots normally listen to the ATIS before departing or arriving so that routine weather and airport information does not need to be repeated on the main control frequency.</p>
        <p>On VATSIM, the Winnipeg FIR uses <strong>vATIS</strong> to create and publish ATIS information. vATIS installation and configuration are covered in the Software Setup course.</p>
    </section>

    <section class="academy-content-section">
        <h2>What information is included?</h2>
        <p>ATIS information is built from airport and weather information, including <strong>METARs</strong>. METARs are normally issued hourly and may also be updated when the weather changes significantly. The ATIS should likewise be updated when relevant information changes. Because the broadcast requires controller-side setup and maintenance in this training context, it is associated with controlled airports.</p>
        <p>Each ATIS has a letter called its <strong>identifier</strong>. On initial contact, pilots should advise ATC which identifier they have received so the controller knows whether they have the current information.</p>
        <ul>
            <li>Current weather.</li>
            <li>Available approaches.</li>
            <li>Active landing and departing runways.</li>
            <li>Runway surface conditions.</li>
            <li>Bird activity.</li>
            <li>Important NOTAM information and other operational items.</li>
        </ul>

        <div class="academy-audio-card">
            <div><div class="academy-kicker">CYWG example</div><h4>Winnipeg ATIS — Information MIKE</h4><p>Listen to the source example from the course slides. Then compare what you hear with the transcript below.</p></div>
            <audio controls preload="metadata"><source src="/academy-assets/media/intro-to-ats/cywg-atis.mp3" type="audio/mpeg">Your browser does not support the audio player.</audio>
        </div>
        <details class="academy-details"><summary>Show ATIS transcript</summary><p>Winnipeg information MIKE. Weather at 1500Z, wind 020 at 7, visibility 15 miles, 200 feet few, 400 feet few, 1,500 feet scattered, 2,500 feet scattered, 7,200 feet scattered, temperature 10 degrees, dewpoint 10 degrees, altimeter 29.74. Approach RNAV ZULU or YANKEE runway 36. Inform arrival of requested approach on initial contact. Landing and departing runway 36. Runway surface condition runway 36, runway condition code 5/5/5, 75% wet/75% wet/75% wet, valid from October 18 at 1453Z to October 18 at 2253Z. Runway 18 threshold displaced 1,000 feet, declared distance 18/36 reduced. No approach light for runway 18. Consult NOTAM for raised approach minimums. Bird activity in vicinity of airport. Inform ATC that you have information MIKE.</p></details>
    </section>

    <section class="academy-content-section">
        <h2>Selecting the active runway</h2>
        <p>The active runway is selected primarily from <strong>wind direction and speed</strong>. Tower may change the active runway when current or forecast conditions make another runway more suitable.</p>
        <p>Remember that reported wind direction tells you <strong>where the wind is coming from</strong>. For example, wind <strong>360 at 10 knots</strong> favours Runway 36 because the aircraft would receive a direct headwind.</p>
        <p>When the wind is calm, airports may use a preferred configuration based on runway length, taxi distance, noise abatement, or other local considerations. In a METAR, calm wind is shown as <strong>00000KT</strong>.</p>
        <div class="academy-callout"><strong>Winnipeg reference:</strong> the CYWG preferred runway configuration is described in the Winnipeg FIR SOPs, section 1.4.9. <a href="https://winnipegfir.ca/policies" target="_blank" rel="noopener">Open Winnipeg FIR policies</a>.</div>
    </section>

    <section class="academy-content-section">
        <h2>Who maintains the ATIS on VATSIM?</h2>
        <p>Tower often maintains the ATIS because Tower selects the active runway. During early Ground training, however, you may be the only controller online and therefore need to select the runway and transmit the ATIS yourself.</p>
        <p>There is no VATSIM requirement that a particular position must own the ATIS. If Ground is maintaining it and Tower, Terminal, or Centre logs on, the ATIS does not have to be transferred. If controllers do decide to transfer responsibility, the outgoing controller should tell the incoming controller which identifier is currently in use.</p>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Module complete</strong><span>You should now be able to explain the ATIS, choose a wind-favoured runway, and coordinate ATIS responsibility.</span></div></div>
</div>
HTML,
            ],
            'vatsimisms' => [
                'title' => 'VATSIMisms',
                'description' => 'Top-down control, text pilots, virtual airlines, new pilots, supervisor assistance, and simulated emergencies.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Describe important ways VATSIM differs from real-world operations.</li>
            <li>Work with voice, receive-only, and text-only pilots.</li>
            <li>Respond appropriately to new pilots, accessibility needs, supervisor situations, and simulated emergencies.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>What is a VATSIMism?</h2>
        <p>A <strong>VATSIMism</strong> is something that happens on the VATSIM network that would not normally occur in real-world operations. No course can list every possible example, so controllers need judgment and flexibility.</p>
        <div class="academy-callout"><strong>Keep the purpose of the network in mind:</strong> VATSIM is a hobby and a learning environment. When a pilot makes a genuine mistake, treat it as an opportunity to help rather than to scold. A controller is not expected to predict every pilot error.</div>
    </section>

    <section class="academy-content-section">
        <h2>Top-down structure</h2>
        <p>VATSIM uses a <strong>top-down</strong> controller structure. A controller normally covers every unstaffed position below their own area of responsibility.</p>
        <div class="academy-info-grid academy-info-grid-2">
            <div class="academy-info-card"><h4>Example: Ground online</h4><p>Ground also covers Clearance Delivery if Clearance Delivery is not separately staffed.</p></div>
            <div class="academy-info-card"><h4>Example: Departure online</h4><p>Departure covers Departure, Tower, Ground, and Clearance Delivery when those lower positions are unstaffed.</p></div>
        </div>
        <p>When one controller is covering several positions, the same frequency is used for those combined responsibilities. If a lower controller connects, give them time to build situational awareness, conduct a quick briefing, and then hand the appropriate aircraft to the new frequency.</p>
        <div class="academy-phraseology"><strong>Example:</strong> “Dauntless 515, Winnipeg Ground has just logged on, contact them on 121.900.”</div>
        <div class="academy-phraseology"><strong>Broadcast example:</strong> “Attention all aircraft, Winnipeg Ground is now online, all taxiing and parked aircraft monitor their frequency on 121.900.”</div>
    </section>

    <section class="academy-content-section">
        <h2>Virtual airlines and unfamiliar callsigns</h2>
        <p>VATSIM includes both real-world and fictional virtual airlines. If a callsign is unfamiliar, inspect the aircraft's flight-plan remarks; virtual-airline pilots often include the spoken callsign there.</p>
        <p>Examples mentioned in the source material include Walker Air Transport (WAT / WALKER), Canadian Xpress (CXA / CANADIAN EXPRESS), Canada Air (CAN / CANADA AIR), and Canadian Airlines (CDN / CANADIAN).</p>
        <p><a href="https://my.vatsim.net/virtual-airlines" target="_blank" rel="noopener">VATSIM-recognized virtual airlines</a></p>
    </section>

    <section class="academy-content-section">
        <h2>Voice, receive-only, and text-only pilots</h2>
        <p>You may encounter three communication capabilities:</p>
        <ul>
            <li><strong>Voice:</strong> normal two-way voice communication.</li>
            <li><strong>Receive-only:</strong> the pilot can hear your voice transmissions but replies using text.</li>
            <li><strong>Text-only:</strong> all controller-pilot communication must be conducted by text.</li>
        </ul>
        <p>For text communication in EuroScope, select the aircraft and use the text box at the bottom of the scope. The display will indicate that your message is being directed to that callsign on your frequency.</p>
        <p>Winnipeg's EuroScope alias file can speed up repetitive text. For example, <code>.clrd</code> can populate a departure-clearance message. The alias file is stored as <code>alias.txt</code> in the Winnipeg FIR sector-file folder.</p>
    </section>

    <section class="academy-content-section">
        <h2>Pilots with disabilities</h2>
        <p>Some pilots may need accommodations. They will often explain what assistance they require, and additional information may be included in their flight-plan remarks.</p>
        <p>One example from the source material is a visually impaired pilot using specialized software that cannot taxi the aircraft. With controller permission, the pilot may need to reposition to the runway instead. If Ground is working with a separate Tower controller, coordinate with Tower before issuing modified taxi instructions that affect runway operations.</p>
        <p><a href="https://www.bvipilots.net/vatsimmessage" target="_blank" rel="noopener">Additional information for controllers working with blind or visually impaired pilots</a></p>
    </section>

    <section class="academy-content-section">
        <h2>New pilots</h2>
        <p>VATSIM pilots have a wide range of experience. The Code of Conduct expects pilots to be familiar with their aircraft and to comply with accepted ATC clearances and instructions, but you will still encounter pilots who struggle.</p>
        <p>If workload permits, help them rather than turning the frequency into a confrontation. Sometimes the best option is to let a manageable mistake play out safely and then send useful resources privately.</p>
        <p>Use EuroScope's <code>.chat</code> function and select the aircraft to open a private conversation. The course recommends <a href="https://www.fltplan.com/" target="_blank" rel="noopener">FltPlan.com</a> as one source for free aeronautical charts.</p>
        <div class="academy-callout"><strong>Core principle:</strong> be patient and kind with pilots who are trying to learn.</div>
    </section>

    <section class="academy-content-section">
        <h2>Supervisor assistance</h2>
        <p>If a pilot appears to be deliberately ignoring instructions or refuses to contact you after a <code>.contactme</code> message, you can request supervisor assistance with <code>.wallop</code>. Include the callsign and a short description of the issue.</p>
        <p>A supervisor may ask questions, contact the pilot, and decide whether the pilot can remain connected. Remember that pilots can also use <code>.wallop</code> to request help involving a controller.</p>
    </section>

    <section class="academy-content-section">
        <h2>Simulated emergencies</h2>
        <p>Simulated emergencies can result from pilot mistakes, simulator failures, or add-ons that generate random faults. Before accepting an emergency, consider whether you can handle the extra workload while maintaining service to other traffic.</p>
        <p>Emergencies may require priority handling and coordination with the pilot and adjacent controllers. If accommodating the emergency would create excessive disruption or unsafe workload, a VATSIM controller may ask the pilot to disconnect or revert the simulated failure.</p>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Course modules complete</strong><span>When you are ready, return to the course page and complete the final self-assessment.</span></div></div>
</div>
HTML,
            ],
        ];

        $this->updateModules($courseId, $modules);
        $this->seedFinalAssessment($courseId, 'Introduction to Air Traffic Services Self-Assessment', $this->introQuestions());
    }

    private function seedSoftwareSetup(): void
    {
        $courseId = DB::table('academy_courses')->where('slug', 'software-setup')->value('id');
        if (! $courseId) {
            return;
        }

        DB::table('academy_courses')->where('id', $courseId)->update([
            'description' => 'Install and configure EuroScope, Winnipeg sector files, vATIS, Audio for VATSIM, Discord, and TeamSpeak for controlling in the Winnipeg FIR.',
            'updated_at' => now(),
        ]);

        $modules = [
            'initial-program-installation' => [
                'title' => 'Initial Program Installation',
                'description' => 'Install EuroScope, download the Winnipeg sector files, and open the CZWG profile.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Describe EuroScope and its role in Winnipeg FIR controlling.</li>
            <li>Install EuroScope and the Winnipeg FIR sector files.</li>
            <li>Open EuroScope with the Winnipeg <code>.prf</code> profile.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>Welcome to EuroScope</h2>
        <p><strong>EuroScope</strong> is a Windows-based ATC client used widely on VATSIM. Winnipeg has used EuroScope for many years and also uses plugins and external clients to make the simulation closer to the real controlling environment.</p>
        <p>This module gets the core program and Winnipeg files installed. External clients such as vATIS and Audio for VATSIM are covered later in the course.</p>
    </section>

    <section class="academy-content-section">
        <h2>1. Download EuroScope</h2>
        <ol>
            <li>Open <a href="https://www.euroscope.hu/wp" target="_blank" rel="noopener">euroscope.hu</a>.</li>
            <li>Select <strong>Installation</strong> from the navigation menu.</li>
            <li>Under Download, choose the latest stable release.</li>
            <li>Save the <code>.msi</code> installer somewhere convenient. The installer can be deleted after EuroScope is installed.</li>
        </ol>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-1/download-link.png" alt="EuroScope download link shown in the source training material"><figcaption>EuroScope download link.</figcaption></figure>
    </section>

    <section class="academy-content-section">
        <h2>2. Run the installer</h2>
        <p>Open the downloaded <code>.msi</code> file and follow the setup wizard. The default installation is normally sufficient.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-1/installer-welcome.png" alt="EuroScope Setup Wizard"><figcaption>Start the setup wizard.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-1/installer-folder.png" alt="EuroScope installation folder screen"><figcaption>Choose the installation folder.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-1/installer-confirm.png" alt="EuroScope confirm installation screen"><figcaption>Confirm installation.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-1/installer-complete.png" alt="EuroScope installation complete screen"><figcaption>Close the installer once complete.</figcaption></figure>
        </div>
    </section>

    <section class="academy-content-section">
        <h2>3. Download Winnipeg sector files</h2>
        <p>The Winnipeg FIR publishes a starter package for its enhanced sector file. Open the <a href="https://github.com/winnipegfir/ZWG-Enhanced-Sector-File/releases" target="_blank" rel="noopener">ZWG Enhanced Sector File releases</a> page. The same link is available from the Winnipeg FIR Dashboard under ATC Resources.</p>
        <p>Use the newest release at the top of the page and, under <strong>Assets</strong>, download the <strong>Starter</strong> package in either <code>.zip</code> or <code>.7z</code> format.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-1/sector-file-assets.png" alt="Starter sector file assets on GitHub"><figcaption>Look for the Starter package in the release assets.</figcaption></figure>
    </section>

    <section class="academy-content-section">
        <h2>4. Extract and open the CZWG profile</h2>
        <p>Extract the starter package into a folder you can keep long term. A common location is a EuroScope folder in Documents, but the exact location is your choice.</p>
        <p>The extracted package contains the Winnipeg profile and supporting sector-file folders. Open the <code>CZWG.prf</code> file to launch the Winnipeg configuration.</p>
        <p>If Windows does not know which program should open the file, right-click the <code>.prf</code>, choose <strong>Open with</strong>, and locate <code>EuroScope.exe</code>. A typical installation path is under <code>Program Files (x86)\EuroScope</code>.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-1/sector-file-folder.png" alt="Example Winnipeg sector file folder"><figcaption>Example extracted Winnipeg sector-file folder.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-1/euroscope-open.png" alt="Winnipeg profile open in EuroScope"><figcaption>The Winnipeg profile open in EuroScope.</figcaption></figure>
        </div>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Module complete</strong><span>EuroScope and the Winnipeg sector files should now be installed and ready for the basic-functions module.</span></div></div>
</div>
HTML,
            ],
            'euroscope-basic-functions' => [
                'title' => 'EuroScope Basic Functions',
                'description' => 'Open ASR displays, use controller lists and aliases, load METARs, and connect as an observer.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Open Winnipeg-specific <code>.asr</code> display files.</li>
            <li>Use the basic controller lists and text-command area.</li>
            <li>Understand Winnipeg aliases and their fillable fields.</li>
            <li>Connect to VATSIM as an observer.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>Opening an ASR</h2>
        <p>EuroScope can be customized heavily. Winnipeg therefore supplies preset display files ending in <code>.asr</code>. These files are stored with the sector files and provide ready-made Centre, Terminal, Tower, and ground-map layouts.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/asr-files.png" alt="Winnipeg ASR files"><figcaption>Examples of Winnipeg ASR files.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/open-asr-menu.png" alt="Open SCT menu showing ASR files"><figcaption>Use the <strong>Open SCT</strong> menu and choose <strong>Open</strong>.</figcaption></figure>
        </div>
        <p>Select the desired <code>.asr</code> from your sector-file install location. EuroScope will load the display and settings associated with that file.</p>
        <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/ground-screen.png" alt="CYWG ground display in EuroScope"><figcaption>Example CYWG ground display.</figcaption></figure>
    </section>

    <section class="academy-content-section">
        <h2>Controller lists</h2>
        <p>Winnipeg's preset displays load several lists. Some are immediately useful on the ground, while others become more important in radar training.</p>
        <ul>
            <li><strong>Departure List:</strong> manages aircraft on the ground at an airport and is one of the first lists you will use while controlling.</li>
            <li><strong>Sector Inbound/Outbound List:</strong> shows aircraft entering your airspace and aircraft you have ownership over; especially useful in radar positions.</li>
            <li><strong>METARs:</strong> shows weather for selected airports.</li>
            <li><strong>Controllers:</strong> displays online ATC and observers within your visibility range.</li>
            <li><strong>Flight Plan (FP) List:</strong> shows aircraft with flight plans in your visibility range and is useful when you need to inspect an aircraft's plan.</li>
        </ul>
        <p>Most list actions are opened by clicking the relevant field. Some options differ depending on whether you left-click or right-click.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/departure-list.png" alt="EuroScope departure list"><figcaption>Departure List example.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/squawk-auto-assign.png" alt="Auto assign squawk menu"><figcaption>Clicking the ASSR field can open the squawk assignment menu. Auto assign chooses a code from the position's range.</figcaption></figure>
        </div>
    </section>

    <section class="academy-content-section">
        <h2>Text commands and METARs</h2>
        <p>EuroScope's text line is used for built-in commands as well as Winnipeg-specific aliases. To load a METAR, click the text-entry area and press <strong>F2</strong>. EuroScope fills in <code>.QD</code>; add the airport ICAO code and press Enter.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-2/metar-command.png" alt="QD METAR command"><figcaption>Example: <code>.QD CYWG</code>.</figcaption></figure>
            <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-2/metar-list.png" alt="METAR list loading"><figcaption>The selected station then loads in the METAR list.</figcaption></figure>
        </div>
    </section>

    <section class="academy-content-section">
        <h2>Winnipeg alias file</h2>
        <p>The Winnipeg FIR supplies an alias file to speed up common text transmissions, especially for pilots who cannot use voice. It is a normal text file stored in the sector package's <strong>Alias</strong> folder, so controllers can inspect the available commands directly.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-2/alias-examples.png" alt="Example Winnipeg aliases"><figcaption>Examples from the Winnipeg alias file.</figcaption></figure>
        <p>Alias commands can include placeholders. A dollar sign followed by a number, such as <code>$1</code>, is a field you fill manually. Press <strong>Tab</strong> to move through fillable fields. A dollar sign followed by an abbreviation or word can be filled automatically by EuroScope when the required flight-plan or display data are available.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-2/alias-fillable.png" alt="Alias command with fillable fields"><figcaption>Example of a command containing manual and automatically populated fields.</figcaption></figure>
    </section>

    <section class="academy-content-section">
        <h2>Connecting as an observer</h2>
        <p>Click <strong>Connect</strong> in the upper-left area of EuroScope to open the connection window.</p>
        <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/connect-window.png" alt="EuroScope connection window"><figcaption>EuroScope connection window.</figcaption></figure>
        <div class="academy-callout academy-callout-warning"><strong>Do not connect to a controlling position until the Winnipeg FIR training team has certified you for that position.</strong> For practice and familiarization, connect as an observer instead.</div>
        <p>Select <strong>WPG_OBS</strong> (or another observer login permitted by Winnipeg FIR policy), confirm that the facility/rating is set for observer use, and connect.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-2/observer-login.png" alt="Observer login configuration"><figcaption>Observer login example.</figcaption></figure>
        <p>When EuroScope asks which frequency to observe, use the <strong>Prim</strong> button to prime the frequency. EuroScope then establishes the relevant visibility points. If you also want to hear the frequency, add it in Audio for VATSIM using the <strong>+</strong> button.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-2/voice-communication-setup.png" alt="EuroScope voice communication setup"><figcaption>Prime the desired frequency in EuroScope.</figcaption></figure>
            <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-2/afv-add-frequency.png" alt="Adding a frequency in Audio for VATSIM"><figcaption>Add the observed frequency in AFV if you want to listen.</figcaption></figure>
        </div>
        <p>Observer logon names and other requirements are described in the <a href="https://winnipegfir.ca/policies" target="_blank" rel="noopener">Winnipeg FIR General Policy</a>.</p>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Module complete</strong><span>You should now be comfortable opening Winnipeg displays, using basic lists and commands, and connecting as an observer.</span></div></div>
</div>
HTML,
            ],
            'external-client-walkthrough' => [
                'title' => 'External Client Walkthrough',
                'description' => 'Install and configure vATIS and Audio for VATSIM, then connect them to your controlling session.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Install vATIS and Audio for VATSIM (AFV).</li>
            <li>Import and configure the Winnipeg FIR vATIS profile.</li>
            <li>Use the basic vATIS interface and connect it to VATSIM.</li>
            <li>Configure AFV audio/PTT settings and connect it to your EuroScope session.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>vATIS: download and install</h2>
        <p>Winnipeg uses <strong>vATIS</strong> to generate and transmit ATIS information on VATSIM. Visit <a href="https://vatis.clowd.io/" target="_blank" rel="noopener">vatis.clowd.io</a>, choose Download, and use the latest release offered by the project.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/vatis-download-page.png" alt="vATIS download page"><figcaption>vATIS download page.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/vatis-release-page.png" alt="vATIS release page"><figcaption>Download the current release.</figcaption></figure>
        </div>
        <p>Run the downloaded installer and follow the prompts. vATIS may be installed wherever you prefer; a Start Menu or desktop shortcut is useful because you will use it during most controlling sessions.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-3/vatis-install-complete.png" alt="vATIS installation complete"><figcaption>Finish the installer and launch the program to begin setup.</figcaption></figure>
    </section>

    <section class="academy-content-section">
        <h2>Import the Winnipeg vATIS profile</h2>
        <p>The Winnipeg FIR provides a pre-built vATIS profile so that controlled ATIS stations do not need to be created manually. Download it from the Winnipeg FIR Controller Dashboard under <strong>ATC Resources</strong>. The source slides instruct users to right-click the resource and choose <strong>Save Link As</strong>. The downloaded profile is a <code>.JSON</code> file.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-3/vatis-dashboard-resource.png" alt="vATIS profile in Winnipeg ATC Resources"><figcaption>Winnipeg ATC Resources contains the vATIS facility file.</figcaption></figure>
        <p>On the vATIS Profiles screen, click <strong>Import</strong>, select the JSON file, and then double-click the imported Winnipeg profile to open it.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/vatis-profiles.png" alt="vATIS profiles screen"><figcaption>Import the Winnipeg profile.</figcaption></figure>
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/vatis-config-prompt.png" alt="vATIS configuration prompt"><figcaption>On first use, vATIS may prompt you to configure the program.</figcaption></figure>
        </div>
    </section>

    <section class="academy-content-section">
        <h2>Configure vATIS</h2>
        <p>Open <strong>Settings</strong> and enter the requested VATSIM account information. Save the settings when finished.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-3/vatis-settings.png" alt="vATIS settings"><figcaption>vATIS settings screen.</figcaption></figure>
        <p>In the main vATIS window, select an airport tab and then choose the appropriate configuration from the dropdown. This populates the operational information fields.</p>
        <ul>
            <li><strong>APRT COND:</strong> normally contains runway configuration and approach-specific information.</li>
            <li><strong>NOTAMs:</strong> normally contains pilot information such as closures, bird activity, and other notices.</li>
        </ul>
        <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/vatis-main-screen.png" alt="vATIS main screen"><figcaption>Select the airport and configuration before connecting.</figcaption></figure>
    </section>

    <section class="academy-content-section">
        <h2>Connect vATIS to VATSIM</h2>
        <p>Once you are logged into the appropriate controlling position in EuroScope and have selected the correct ATIS configuration, click <strong>Connect</strong> in vATIS. The client should connect and populate the current weather information.</p>
        <p>To make changes, edit the APRT COND or NOTAMs fields and click the Save icon. The large ATIS identifier letter can also be selected when you need to change the identifier.</p>
        <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/vatis-connected.png" alt="Connected vATIS display"><figcaption>Connected vATIS display with weather and airport information populated.</figcaption></figure>
        <p>For deeper functionality, see the <a href="https://docs.vatis.clowd.io/#/?id=what-is-vatis" target="_blank" rel="noopener">vATIS documentation</a>.</p>
    </section>

    <section class="academy-content-section">
        <h2>Audio for VATSIM</h2>
        <p><strong>Audio for VATSIM (AFV)</strong> is the voice client used with EuroScope. Download the standalone client from VATSIM's <a href="https://audio.vatsim.net/docs/atc/euroscope" target="_blank" rel="noopener">EuroScope audio documentation</a>, run the installer, and allow the client to apply any available updates.</p>
        <div class="academy-figure-grid academy-figure-grid-2">
            <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/afv-docs.png" alt="Audio for VATSIM EuroScope documentation"><figcaption>AFV download information for EuroScope users.</figcaption></figure>
            <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-3/afv-client.png" alt="Audio for VATSIM client"><figcaption>The AFV standalone client.</figcaption></figure>
        </div>
    </section>

    <section class="academy-content-section">
        <h2>Configure and connect AFV</h2>
        <p>Open AFV Settings and enter your VATSIM details, choose the correct microphone and output devices, and assign a <strong>push-to-talk (PTT)</strong> key. Apply the settings before closing the window.</p>
        <figure class="academy-content-figure academy-content-figure-small"><img src="/academy-assets/media/software-setup/module-3/afv-settings.png" alt="Audio for VATSIM settings"><figcaption>AFV account, audio, and PTT configuration.</figcaption></figure>
        <p>Connect to the correct position in EuroScope first, then click <strong>Connect</strong> in AFV. AFV should automatically follow your EuroScope position. You can manually add a position/frequency with the <strong>+</strong> button if required.</p>
        <p>If AFV displays an unexpected frequency such as <strong>199.998</strong>, first verify that the EuroScope connection is correct and that the selected position exists in the connection settings. Disconnecting and reconnecting AFV may also resolve the issue.</p>
        <ul>
            <li><strong>TX:</strong> transmit on a frequency.</li>
            <li><strong>RX:</strong> receive a frequency.</li>
            <li><strong>XC:</strong> cross-couple frequencies; this is covered in more detail later in training.</li>
        </ul>
        <figure class="academy-content-figure"><img src="/academy-assets/media/software-setup/module-3/afv-connected.png" alt="Connected AFV client"><figcaption>Example AFV session connected to a controller position.</figcaption></figure>
        <p>Additional reference: <a href="https://audio.vatsim.net/downloads/manual.pdf" target="_blank" rel="noopener">Audio for VATSIM manual</a>.</p>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Module complete</strong><span>vATIS and AFV should now be installed, configured, and ready to use with EuroScope.</span></div></div>
</div>
HTML,
            ],
            'communication-platforms' => [
                'title' => 'Communication Platforms',
                'description' => 'Set up Discord and TeamSpeak for Winnipeg FIR, VATCAN, and VATSIM communication and coordination.',
                'content' => <<<'HTML'
<div class="academy-content">
    <div class="academy-objectives">
        <div class="academy-kicker">Learning objectives</div>
        <h3>By the end of this module, you should be able to:</h3>
        <ul>
            <li>Install or access Discord and TeamSpeak 3.</li>
            <li>Join the relevant Winnipeg FIR, VATCAN, and VATSIM communication services.</li>
            <li>Understand why these platforms are used alongside EuroScope.</li>
        </ul>
    </div>

    <section class="academy-content-section">
        <h2>Why controllers need communication platforms</h2>
        <p>Controlling requires more than a scope and radio client. Communication platforms keep controllers informed, provide staff and training coordination, and support communication outside the live ATC frequency.</p>
        <p>The source course identifies <strong>TeamSpeak 3</strong>, <strong>Discord</strong>, and EuroScope's controller-calling system as Winnipeg FIR controller communication tools. The Winnipeg FIR General Policy contains the current requirements.</p>
        <p><a href="https://winnipegfir.ca/policies" target="_blank" rel="noopener">Winnipeg FIR General Policy</a></p>
    </section>

    <section class="academy-content-section">
        <h2>Discord</h2>
        <p>VATSIM, VATCAN, and the Winnipeg FIR each use Discord communities. Create an account at <a href="https://discord.com/" target="_blank" rel="noopener">discord.com</a>. Installing the desktop client is recommended because features such as screen sharing can be useful during training.</p>
        <ul>
            <li><strong>VATSIM:</strong> use the <a href="https://community.vatsim.net/servers" target="_blank" rel="noopener">VATSIM community servers directory</a>.</li>
            <li><strong>VATCAN:</strong> use your <a href="https://vatcan.ca/my/integrations" target="_blank" rel="noopener">VATCAN Integrations</a> page. Linking through this page allows rating information to update automatically.</li>
            <li><strong>Winnipeg FIR:</strong> you can join from the Winnipeg FIR website, but the course recommends using the <a href="https://winnipegfir.ca/dashboard" target="_blank" rel="noopener">Winnipeg FIR Dashboard</a> so your Discord account is linked to the FIR website.</li>
        </ul>
    </section>

    <section class="academy-content-section">
        <h2>TeamSpeak 3</h2>
        <p>VATCAN operates a unified TeamSpeak 3 server that is commonly used for controller coordination. Download TeamSpeak from <a href="https://www.teamspeak.com/en/downloads/" target="_blank" rel="noopener">teamspeak.com</a> and choose the installer that matches your system, typically 64-bit Windows.</p>
        <p>After installing TeamSpeak, return to <a href="https://vatcan.ca/my/integrations" target="_blank" rel="noopener">VATCAN Integrations</a>. In the TeamSpeak section, generate an <strong>access token</strong>. The token links your VATSIM information, including rating and FIR, so the server can give you the correct access.</p>
        <p>The generated token provides a temporary nickname similar to <code>TK000000001</code>. You may use the Connect button to open TeamSpeak automatically, or connect manually to <code>ts.vatcan.ca</code> while using the generated token as your nickname.</p>
    </section>

    <section class="academy-content-section">
        <h2>You're ready</h2>
        <p>At this point you should have the core communication platforms needed for Winnipeg training and controller coordination. If you need membership help, the source course points students to <a href="https://vatcan.ca/my/membership" target="_blank" rel="noopener">VATCAN Membership</a>.</p>
    </section>

    <div class="academy-module-complete"><i class="fas fa-check-circle"></i><div><strong>Course modules complete</strong><span>Return to the Software Setup course page and complete the final self-assessment.</span></div></div>
</div>
HTML,
            ],
        ];

        $this->updateModules($courseId, $modules);
        $this->seedFinalAssessment($courseId, 'Software Setup Self-Assessment', $this->softwareQuestions());
    }

    private function updateModules(int $courseId, array $modules): void
    {
        foreach ($modules as $slug => $data) {
            DB::table('academy_modules')
                ->where('course_id', $courseId)
                ->where('slug', $slug)
                ->update([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'content' => $data['content'],
                    'google_slides_url' => null,
                    'published' => true,
                    'updated_at' => now(),
                ]);
        }
    }

    private function seedFinalAssessment(int $courseId, string $title, array $questions): void
    {
        // These two courses are still pre-release. Keep assessment content in one final module
        // and remove any old module-level quizzes or local test attempts from earlier builds.
        $nonFinalModuleIds = DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->where('slug', '!=', 'final-knowledge-check')
            ->pluck('id');
        if ($nonFinalModuleIds->isNotEmpty()) {
            DB::table('academy_quizzes')->whereIn('module_id', $nonFinalModuleIds)->delete();
        }

        $lastOrder = (int) DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->where('slug', '!=', 'final-knowledge-check')
            ->max('sort_order');

        DB::table('academy_modules')->updateOrInsert(
            ['course_id' => $courseId, 'slug' => 'final-knowledge-check'],
            [
                'title' => 'Final Self-Assessment',
                'description' => 'Complete this cumulative self-assessment after reviewing every module in the course.',
                'google_slides_url' => null,
                'content' => null,
                'sort_order' => $lastOrder + 1,
                'published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $moduleId = DB::table('academy_modules')
            ->where('course_id', $courseId)
            ->where('slug', 'final-knowledge-check')
            ->value('id');

        DB::table('academy_quizzes')->updateOrInsert(
            ['module_id' => $moduleId],
            [
                'title' => $title,
                'passing_score' => 80,
                'published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $quizId = DB::table('academy_quizzes')->where('module_id', $moduleId)->value('id');

        // Reset only pre-release attempts for these two rewritten assessments so old responses
        // cannot point at a different question/answer set.
        DB::table('academy_quiz_submissions')->where('quiz_id', $quizId)->delete();
        DB::table('academy_questions')->where('quiz_id', $quizId)->delete();

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
                'A pilot at a smaller Canadian airport needs traffic and weather advisory information, but the service is not issuing ATC clearances. Which ATS facility best matches that role?',
                ['Flight Information Centre (FIC)', 'Flight Service Station (FSS)', 'Area Control Centre (ACC)', 'Clearance Delivery'],
                1,
                'FSS provides advisory information at smaller airports rather than issuing ATC clearances.'
            ),
            $this->mc(
                'An IFR aircraft at Winnipeg has not begun taxiing. Its route, SID, departure runway, and flight-plan correctness still need to be checked. Which controller is primarily responsible?',
                ['Ground', 'Tower', 'Clearance Delivery', 'Centre'],
                2,
                'Clearance Delivery issues the IFR route clearance and verifies the flight plan, SID, and departure runway before taxi.'
            ),
            $this->mc(
                'An IFR departure has climbed out, is established enroute, and is leaving the Terminal/Departure environment. Which controller would normally receive it next?',
                ['Ground', 'Centre', 'Arrival', 'Clearance Delivery'],
                1,
                'Centre provides enroute IFR service after Departure or Terminal establishes the flight enroute.'
            ),
            $this->mc(
                'Why can a low-altitude aircraft lose radar coverage even when it is between two populated cities?',
                ['Radar only works at night', 'Radar coverage is line-of-sight and can be limited by terrain, distance, and low altitude', 'SSR cannot receive transponder replies below 18,000 feet', 'Controllers disable primary radar outside terminal areas'],
                1,
                'Radar propagates outward and upward and is limited by line-of-sight, terrain, site spacing, and aircraft altitude.'
            ),
            $this->mc(
                'A primary radar return shows a target moving northeast, but no secondary information is available. Which statement is most accurate?',
                ['PSR can determine target movement but cannot identify who the aircraft is', 'PSR always displays the aircraft registration', 'PSR requires the pilot to enter a squawk before it can detect the aircraft', 'PSR receives a digital reply directly from the transponder'],
                0,
                'Primary radar uses reflected energy to locate a target but does not inherently identify the aircraft.'
            ),
            $this->mc(
                'Which pairing correctly describes the information returned by the transponder modes covered in the course?',
                ['Mode A: code + altitude; Mode C: registration only', 'Mode C: code + altitude; Mode S: code + altitude + registration + speed', 'Mode C: code only; Mode S: weather + route', 'Mode S: code only; Mode A: code + altitude + speed'],
                1,
                'The course lists Mode C as code and altitude, while Mode S adds registration and speed.'
            ),
            $this->mc(
                'The METAR reports wind 360 degrees at 10 knots. Ignoring other local restrictions, which runway orientation is the best match and why?',
                ['Runway 18, because the wind blows toward 180', 'Runway 36, because wind direction reports where the wind is coming from', 'Either runway, because wind direction is not used for runway selection', 'Runway 09, because 360 degrees is a crosswind'],
                1,
                'Wind direction states where the wind originates, so Runway 36 would provide a direct headwind.'
            ),
            $this->mc(
                'A pilot checks in saying they have ATIS information MIKE. What does that identifier mainly allow the controller to determine?',
                ['Which aircraft type the pilot is flying', 'Whether the pilot received the current version of routine airport information', 'Which transponder mode the aircraft is using', 'Which controller issued the pilot\'s IFR clearance'],
                1,
                'The ATIS letter identifies the version of the broadcast the pilot received.'
            ),
            $this->mc(
                'On VATSIM, Departure is online at an airport while Tower, Ground, and Clearance Delivery are unstaffed. Under the top-down structure, what should the Departure controller expect to cover?',
                ['Departure only', 'Departure and Tower only', 'Departure, Tower, Ground, and Clearance Delivery', 'Every controller position in the entire FIR including Centre'],
                2,
                'The course specifically uses Departure as an example of covering all lower unstaffed positions down through Clearance Delivery.'
            ),
            $this->mc(
                'A pilot declares a simulated emergency during a busy period. The extra coordination would overwhelm the controller and significantly disrupt other traffic. What does the course say the controller may do?',
                ['The controller must accept every simulated emergency', 'Immediately issue a landing clearance regardless of separation', 'Ask the pilot to disconnect or revert the simulated fault if the emergency cannot reasonably be accommodated', 'Ignore the emergency until another controller logs in'],
                2,
                'Controllers may decline a simulated emergency when the workload or disruption is too great.'
            ),
            $this->written(
                'A new pilot is struggling to follow instructions but appears to be trying in good faith. Describe how you would handle the situation if your workload allows.',
                ['Keep the frequency safe and manageable', 'Help rather than scold the pilot', 'Use private chat or off-frequency resources when useful', 'Show patience and give the pilot a reasonable learning opportunity'],
                'The course emphasizes helping new pilots when workload permits, using private messages/resources when appropriate, and maintaining a patient learning-focused approach.'
            ),
            $this->written(
                'Compare Primary Surveillance Radar and Secondary Surveillance Radar. Include how each system receives information and how a squawk code helps identify an aircraft.',
                ['PSR uses reflected radio energy from the target', 'SSR interrogates the aircraft transponder and receives a transmitted reply', 'PSR can show target position/movement but not identity by itself', 'A controller-assigned four-digit squawk links the SSR return with the aircraft/flight plan', 'Mentions the Mode C or Mode S information from the course'],
                'A strong answer distinguishes reflected primary returns from transponder-based secondary returns and explains the role of the assigned squawk.'
            ),
        ];
    }

    private function softwareQuestions(): array
    {
        return [
            $this->mc(
                'You have installed EuroScope and extracted the Winnipeg Starter package. Which file should you open to start the Winnipeg profile?',
                ['CZWG.prf', 'CYWG_GND.asr', 'alias.txt', 'WinnipegProfile.json'],
                0,
                'The source material instructs students to open the CZWG .prf file after extracting the sector package.'
            ),
            $this->mc(
                'You want to switch from your current EuroScope display to the preset CYWG ground layout. What is the correct approach?',
                ['Open the desired .asr through the Open SCT menu', 'Import a vATIS JSON profile', 'Press F2 and type the airport ICAO', 'Generate a TeamSpeak token'],
                0,
                'ASR files are preset EuroScope displays and are opened through the Open SCT menu.'
            ),
            $this->mc(
                'While looking at the Departure List, you click the ASSR field for an aircraft. What function from the course can this expose?',
                ['Automatic squawk-code assignment', 'Automatic runway selection', 'A Discord invitation', 'ATIS letter generation'],
                0,
                'The ASSR field can open the squawk assignment options, including Auto assign.'
            ),
            $this->mc(
                'You need CYWG weather in the EuroScope METAR list. Which sequence matches the course?',
                ['Press F2, let EuroScope insert .QD, type CYWG, then press Enter', 'Press F1, type .ASR CYWG, then reconnect', 'Open the alias file and choose METAR from a menu', 'Use .wallop CYWG'],
                0,
                'F2 inserts .QD; adding the station ICAO and pressing Enter loads the METAR.'
            ),
            $this->mc(
                'An alias command contains $1 and another field such as $arrrwy. What is the key difference?',
                ['$1 is a manual fillable field; a named field such as $arrrwy can be filled automatically when EuroScope has the needed data', '$1 is always the aircraft callsign; $arrrwy is always typed manually', 'Both fields are ignored by EuroScope', 'Both fields can only be filled by right-clicking the radar target'],
                0,
                'Numbered dollar fields are manually fillable and can be tabbed through; named fields may be automatically populated from available data.'
            ),
            $this->mc(
                'You are not yet certified on a controlling position but want to watch traffic. Which setup best follows the course?',
                ['Connect as WPG_OBS/Observer, then prime the frequency you want to observe', 'Connect as CYWG_TWR with your student rating and transmit only when needed', 'Use vATIS as the controller connection and leave EuroScope offline', 'Connect AFV first and let it create the EuroScope position'],
                0,
                'Students should use an approved observer login rather than an uncertified controlling position, then prime the desired frequency.'
            ),
            $this->mc(
                'What file type does the Winnipeg pre-built vATIS profile use, and where does the course tell you to obtain it?',
                ['.JSON from the Winnipeg FIR Dashboard under ATC Resources', '.ASR from the EuroScope Open SCT menu', '.PRF from the VATCAN TeamSpeak server', '.TXT from the VATSIM supervisor client'],
                0,
                'The Winnipeg vATIS facility profile is downloaded as JSON from the Controller Dashboard ATC Resources area.'
            ),
            $this->mc(
                'In vATIS, which pairing best matches the source material?',
                ['APRT COND: runway/approach information; NOTAMs: closures, bird activity, and other pilot notices', 'APRT COND: pilot passwords; NOTAMs: microphone settings', 'APRT COND: controller roster; NOTAMs: flight strips', 'APRT COND: Discord channels; NOTAMs: TeamSpeak tokens'],
                0,
                'The course separates runway/approach configuration information from pilot notices such as closures and bird activity.'
            ),
            $this->mc(
                'AFV shows an unexpected frequency such as 199.998 after you connect. Which troubleshooting sequence best follows the course?',
                ['Verify the EuroScope connection and selected position, then disconnect/reconnect AFV if needed', 'Delete the Winnipeg sector files and reinstall Windows', 'Change the ATIS identifier letter', 'Generate a new Discord integration token'],
                0,
                'The course recommends checking the EuroScope connection/position first and reconnecting AFV if necessary.'
            ),
            $this->mc(
                'Why does the course recommend generating the VATCAN TeamSpeak access token from the Integrations page instead of simply choosing any nickname?',
                ['The token links VATSIM information such as rating and FIR so the server can assign the correct access', 'The token installs EuroScope automatically', 'The token replaces the AFV push-to-talk key', 'The token publishes the airport ATIS'],
                0,
                'The TeamSpeak token links the member\'s VATSIM information and helps provide the correct server/channel access.'
            ),
            $this->written(
                'You are preparing for a controlling session. Explain the distinct roles of EuroScope, vATIS, Audio for VATSIM, Discord, and TeamSpeak in the workflow taught by this course.',
                ['EuroScope is the controlling/radar client', 'vATIS creates and publishes airport ATIS information', 'AFV provides controller voice communications', 'Discord supports community/training/staff communication', 'TeamSpeak is used for VATCAN/controller coordination'],
                'Award credit for correctly matching each program with its role rather than simply listing the program names.'
            ),
            $this->written(
                'A new student says: “EuroScope opens, but I cannot see the Winnipeg layout, I am not certified to control yet, and I want to listen to Winnipeg Ground.” Give a safe troubleshooting/setup sequence based on Modules 1 and 2.',
                ['Confirm the Winnipeg Starter package was extracted and CZWG.prf opened', 'Open the appropriate Winnipeg .asr display', 'Connect only as an approved observer such as WPG_OBS', 'Prime the Winnipeg Ground frequency in EuroScope', 'Add the same frequency in AFV if the student wants to hear it'],
                'The answer should combine installation/display steps with the restriction against logging onto an uncertified controlling position.'
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
            'points' => 2,
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

            DB::table('academy_modules')->where('course_id', $courseId)->update([
                'content' => null,
                'updated_at' => now(),
            ]);
        }
    }
};
