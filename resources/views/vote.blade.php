@extends('layouts.vote')
@section('title', 'PROPOSAL: FIR Chief Electoral System - Winnipeg FIR')
@section('description', 'Time to go big. The Winnipeg FIR is proposing a new way to choose our chief.')
@section('content')

@if(Auth::user()->permissions >= 4)
<style>
.accordion {
  background-color: #122b44;
  color: white;
  cursor: pointer;
  padding: 1%;
  width: 100%;
  border: none;
  text-align: left;
  outline: none !important;
  font-size: 12px;
  transition: 0.4s;
}

.accordion:hover {
  background-color: #272727;
  color: #fff;
}

.active {
  background-color: #202020;
  color: white;
}

.accordion:after {
  font-family: "Font Awesome 5 Free";
  content: '\f104';
  float: right;
  font-weight: 900;
}

.active:after {
  font-family: "Font Awesome 5 Free";
  content: "\f107";
  font-weight: 900;
}

.panel {
  shadow: 5px;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.1s ease-out;
}
</style>

<div class="container py-4">
    <h1 class="font-weight-bold blue-text">PROPOSAL: FIR Chief Electoral System</h1>
    <p>For too long, VATSIM’s staff positions have been chosen based entirely without the opinions of the public being welcomed. Members of FIRs, divisions, and more haven’t had the 
      ability to truly choose among themselves who leads them. Here’s how we plan to change that.</p>
  <br>
    <div class="card card-body">
        <h3 class="font-weight-bold blue-text">The Rules, Simplified</h3>
            <p class="pb-2">The whole process starts with a simple yes/no confidence vote, held yearly. If the Chief does not have the confidence of the FIR, the election process is triggered.</p>

        <h5 class="font-weight-bold blue-text">The Electioral Process</h5>
            <li>Any active Winnipeg FIR <b>S2+</b> home controller is eligible to participate in the voting process</li>
            <li>Standards have been created to allow <b>select visiting controllers</b> to also participate in voting</li>
            <li><b>C1+</b> home controllers who meet the VATCAN minimum requirements for FIR Chief can run as a candidate</li>
            <li>Dates for candidate declarations, elections, vote submissions and more are all specifically outlined, ensuring <b>a clear, fair system</b></li>
            <li>The winner will be chosen using the 50% +1 vote system - <b>majority rules</b>. If there's more than one candidate, then a rundown occurs.</li>
    </div>
    <br></br>
    <h3 class="font-weight-bold blue-text pb-0">Frequenty Asked Questions</h3>
<!--FAQ-->    
    <ul class="list-unstyled">
        <li class="mb-1">
            <button class="accordion">Why a new system?</button>
            <div class="panel">
                <div class="card-body pb-0">
                <p>There's a few reasons we believe this system makes a lot of sense, and it's hard to summarize in a tiny card. But to put it simple - a voting system allows the people of the 
                 FIR to choose their leader, not just executives who could have very little knowledge about people being interviewed. It also will push FIR Chiefs to achieve at their highest potential 
                  - after all - knowing an election is held every year means proving yourself will be more key than ever before.</p>
                </div>
            </div>
        </li>
        <li class="mb-1">
            <button class="accordion">What if there's a tie?</button>
            <div class="panel">
                <div class="card-body pb-0">
                <p>The proposal includes a detailed plan for ties - as unlikely as a tie would be in a voting system that would allow more than two possible options to vote for, as well as a 
                  relatively large voter base, it could happen. Section 1.5 of the proposal notes a system for how a winner will be determined should there be a tie after voting with more than two candidates, and also details 
                a "worst case scenario" option for if the FIR is unable to determine a winner with just two candidates, wherein the VATCAN executive team becomes involved.</p>
                </div>
            </div>
        </li>
        <li class="mb-1">
            <button class="accordion">When will this be implemented?</button>
            <div class="panel">
                <div class="card-body pb-0">
                <p>The Winnipeg FIR wants to make sure this system is as close to perfect as can be prior to implementing something so major - hence why this page exists. The FIR is looking 
                  for comments, concerns, and everything in between (including suggestions!) to make sure what we believe is a revolutionary idea can be run to its full potential. Optimally,
                   this system can be implemented immediately, triggering a confidence vote as soon as June 1st.
                </p>
                </div>
            </div>
        </li>
        <li class="mb-1">
            <button class="accordion">Why yearly confidence votes?</button>
            <div class="panel">
                <div class="card-body pb-0">
                <p>As mentioned in "Why a new system" as well as in the actual proposal - a yearly vote ensures that the FIR constantly has the best available person in charge. It also pushes 
                  the incumbent FIR Chief to be performing at their most productive level. The current system has no term limits, no real need for a Chief to push themselves to get things done,
                   besides their love for their FIR. This would push that to a new limit.
                </p>
                <p>The confidence vote is also a very simple way to decide if a full election is needed - a simple yes/no vote being held a simple, relatively quick process. The key to the confidence vote 
                  is that if it passes, there's no election required. This system, should a well-liked, qualified chief be at the helm, should hold a very minimal impact on the FIR's operations.
                </p>
                </div>
            </div>
        </li>
        <li class="mb-1">
            <button class="accordion">Why can't S1s vote?</button>
            <div class="panel">
                <div class="card-body pb-0">
                <p>The deliberation as to who should be considered eligible to participate in voting was a very detailed process - however, the FIR team and others came to the conclusion
                  that the minimum of an S2 rating being the requirement to vote based on the simple fact that opening the voting eligibility to any lower rating would include members 
                  who have only been a member of the FIR for a short period of time, as well as controllers who have not completed significant training with the FIR's instructing team.
                </p>
                <p>In a system that's built to ensure the FIR's active and dedicated members are the ones who choose their leader, a system that could allow loopholes or students who 
                  have yet to spend a significant portion of time working within the FIR to skew a vote could be problematic. While it does shorten the voter list, we believe it also 
                  enhances the list to include just members who have spent their time to earn their right to participate in the process.
                </p>
                </div>
            </div>
        </li>
        <li class="mb-1">
            <button class="accordion">Can visiting controllers participate?</button>
            <div class="panel">
                <div class="card-body pb-0">
                <p>Short answer: yes. However, the details to this are key - visiting controllers have to meet activity requirements much more strict than home controllers to be considered 
                  eligible to participate in voting. Visiting controllers need to hold an active controller status for six months, as well as holding a minimum S2 rating (the same as 
                  home controllers.) Further details to this are available in section 2.0 of the proposal document.
                </p>
                </div>
            </div>
        </li>
    </ul>
    <p>We'll be adding more FAQs as we get them - keep checking back for answers to more of your questions about this important proposal.</p>
    <br>
    <h3 class="font-weight-bold blue-text pb-0">Ready for the details? Take a look at the complete proposal.</h3>
    <a class="ml-0 btn btn-success" href="google.com">Read The Proposal</a>
</div>
@else

<div class="container py-4">
    <h1 class="font-weight-bold blue-text">Sorry, this page isn't quite ready yet - check back soon!</h1>
    <a class="ml-0 btn btn-success" href="/">Back to WinnipegFIR.ca</a>
</div>

@endif

<!--Script for accordions-->
<script>

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}

</script>

@endsection