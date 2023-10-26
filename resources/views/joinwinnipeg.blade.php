@extends('layouts.master')
@section('title', 'Join - Winnipeg FIR')
@section('description', 'Join the Winnipeg FIR!')

@section('content')

    <div class="container" style="margin-top: 20px;">
        <h1 class="blue-text font-weight-bold">We’re Not Just Another FIR.</h1>
        <p>From those who choose to fly Winnipeg as a pilot, to those who dedicate their time to controlling our skies and providing the welcoming, kind and knowledgeable service that the VATSIM network knows Canada for - the Winnipeg FIR isn’t just a place on a map. We’re a community.</p>
		<p>We're home to a constantly-evolving, modern training program, some of the best instructors around and more. If you’re looking to start your controlling career on VATSIM (or, are looking to expand your horizons from your current home FIR, ARTCC or vACC), we’d love to have you join our team on the scopes.</p>
		<hr>
		<h1 class="blue-text font-weight-bold">Joining Us</h1>
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active font-weight-bold" id="new-tab" data-toggle="tab" href="#new" role="tab" aria-controls="new" aria-selected="false">If You're New to VATSIM</a>
			</li>
			<li class="nav-item">
				<a class="nav-link font-weight-bold" id="visit-tab" data-toggle="tab" href="#visit" role="tab" aria-controls="visit" aria-selected="false">If You Have a Home FIR/ARTCC/vACC</a>
			</li>
		</ul>
		
	<div class="card">
		<div class="card-body pt-0 pb-2 ">	
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="new" role="tabpanel" aria-labelledby="new">
					<br>
					<h3 class="font-weight-bold blue-text">Step 1 – Join VATSIM</h3>
					<ol>
						<li>If you are currently not a member of VATSIM then you can sign up for an account at the<span> </span><a href="http://cert.vatsim.net/vatsimnet/signup.html" target="_blank" rel="noopener noreferrer">VATSIM Registration Page</a>. Ensure that you read all of the pages and instructions carefully.</li>
						<li>Under the preferred region field select the<span> </span><strong>Americas</strong><span> </span>region and the <strong>Canada</strong> division.</li>
						<li>After you complete your registration you receive an ID and password from VATSIM via email. When you have these two items, continue to step two.</li>
					</ol>
					<hr>
					<h3 class="font-weight-bold blue-text">Step 2 – Join VATCAN &amp; Complete your S1 Exam</h3>
					<ol>
						<li>You will need to join the VATCAN division. If you have not already joined, or skipped some steps from Step 1, click the following link to set your region:<span> </span><a href="https://cert.vatsim.net/vatsimnet/regch.php">https://cert.vatsim.net/vatsimnet/regch.php</a>. You may proceed to step 5 if you are already a part of VATCAN.</li>
						<li>You will now get your choice to select a region and division to join. To join VATCAN select<span> </span><strong>Americas</strong><span> </span>as your region.</li>
						<li>In the select a division drop down box, select<span> </span><strong>Canada</strong>.</li>
						<li>Press<span> </span><strong>Continue</strong><span> </span>to set your selection.</li>
						<li>Follow the steps on <a href="https://vatcan.ca/How-to-Become-a-Controller" target="_blank" rel="noopener noreferrer">https://vatcan.ca/How-to-Become-a-Controller</a> (if you already have your S1 (or higher) rating, you may skip step 2 on this page.)</li>
					</ol>
					<hr>
					<h3 class="font-weight-bold blue-text">Step 3 – Join the Winnipeg FIR</h3>
					<ol>
						<li><a href="http://vatcan.ca/login">Login into VATCAN</a> using your VATSIM CID and password.</li>
						<li>Click on <strong>My</strong> <strong>VATCAN</strong>, then on <strong>Transfer Request</strong>.</li>
						<li>In the <strong>New FIR</strong> drop-down, select <strong>Winnipeg FIR</strong>.</li>
						<li>Fill in your reasoning, and hit <strong>Submit</strong>.</li>
						<li>Your transfer will be reviewed by our FIR staff within 1-2 weeks.</li>
					</ol>
				</div>

				<div class="tab-pane fade" id="visit" role="tabpanel" aria-labelledby="visit"><br>
					<div class="card">	
						<div class="card-body btn-primary">
							<text>Winnipeg is currently welcoming visiting applications for Canadian-based, active controllers holding a Student 2 (S2) rating or higher, as well as all international active controllers who hold a rating of Controller (C1) or above! See below for information on how to apply.</text>
						</div>
					</div>
					<br>
					<p>Winnipeg is always looking for controllers to visit our FIR. Whether it's for a change of scenery, to learn a new way of controlling, or just for fun, come visit Winnipeg!</p>
					<p>Members who are interested in visiting are now asked to visit the VATCAN.ca website to apply for visiting status. Click below to be re-directed to the website to continue the application process.</p>
					<a class="ml-0 btn btn-success" href="https://vatcan.ca/my/visit">Apply Now!</a>
					</div>
				</div>
			</div>
		</div>
<br>
	<a href="/training"><h2 class="font-weight-bold blue-text"><i class="fas fa-arrow-right"></i> View Wait Times, How Our Training Works & More!</h2></a>
	<h5>Questions? <a href="{{route('staff')}}">Contact our Chief Instructor!</a></h5>
<br>

</div>
@endsection
