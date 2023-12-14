@extends('layouts.master')
@section('title', 'A Letter from the Chief - 2022')
@section('content')

<div class="container py-4">
    <a href="{{route('index')}}" class="blue-text" style="font-size: 1.2em"> <i class="fas fa-arrow-left"></i> Back to the Winnipeg FIR</a>
    <br></br>
    <div class="card card-body">
    <h1 class="font-weight-bold blue-text">Nate's Letter to the FIR</h1>
        <p>It’s a bit wild to think that we’re soon approaching the end of 2022. This year seemed to fly by - but as with the past couple years, I’d still like to share some thoughts as we wrap up another year.</p>
        <hr>
        <h5 class="blue-text font-weight-bold">To My Staff;</h5>
        <br>
            <p>When I wrote my letter to the FIR in December 2021, I wrote about how excited I was to have a fully staffed leadership team. And while some of the names on Winnipeg’s staff have changed over the past year, our core remains the same.</p>
            
            <p>I continue to be lucky enough to have Kolby Dunning as my Deputy Chief - who also recently stepped in to help fill some webmaster duties as James White decided to take a step back to work on other projects. We also continue to be helped by Ryan Miltenberger, savior of sector files and noted ERAM fan - oh, and our Facility Engineer, too. As well, Tavis Harrison continues to hold down the fort when it comes to the FIR’s events - he’s got a lot on his plate in 2023, from Winnipeg’s first ever Friday Night Ops, as well as another possible Cross The Pond run.</p>
            
            <p>As I hinted to, we’ve also welcomed some changes over the span of the last year - a new Chief Instructor - Liam Shaw, someone who brings a plethora of real-world experience alongside his work on the VATSIM network. He’s been working hard behind the scenes on projects that we’re excited to share more details about soon, as well as helping our training team continue to develop, in my humble opinion, the best controllers on the network. We also welcomed a brand new staff position for the first time since I took over the reins - the Assistant Chief Instructor, Ethan Mason. Ethan, like Liam, brings plenty of experience to the table and is already hard at work getting our students the help they need to reach their potential.</p>
           
            <p>I feel that our FIR’s staff remains extremely strong - and I’m so grateful to have the members we have who stepped up to help our corner of the network continue to grow.</p>
              
        <hr>
        <h5 class="blue-text font-weight-bold">To Winnipeg's Controllers;</h5>
        <br>
            <p>Over the past few years, Winnipeg has turned from an FIR that really only had a few members, mostly locals who knew their stuff, and worked on their own - to a vast, diverse group of controllers from across the world. Why would they choose Winnipeg, then, over their home? I can’t speak for everyone, but I’d like to think our great leadership, dedicated pilots and training team have something to do with it.</p>
            
            <p>Thanks to the amazing controllers here in the FIR, we’ve been lucky enough to hold a Cross the Pond event, and have more major events on the horizon as we move into 2023. Experience on this network is key, and the traffic we’re seeing here in Winnipeg continues to grow.</p>

            <p>This past year saw a change in training leadership, as mentioned previously in my letter. Of course, that also means that there was a brief pause on training as our new staff members got settled in their new roles. If you’re a student who has been, or had to wait for their training, you have my sincere thanks. We might just be a small community, but running an FIR’s training can sometimes be a daunting task. Your patience is always appreciated and does not go unnoticed.</p>
        
        <hr>
        <h5 class="blue-text font-weight-bold">To the VATCAN Staff;</h5>
        <br>
            <p>For the first time since starting this tradition a couple years ago, there will be staff members seeing this for the first time! So, a very warm hello to Mark and Cody. You both bring tremendous leadership to Phil and his already well-solidified team in the division, and I’m hopeful you can help continue driving our division in the right direction.</p>
            
            <p>VATCAN’s relationship with Winnipeg has always been a bit strange, given that I stepped from VATCAN1 right to WPG1 - but I remain extremely grateful that our relationship has stayed as strong as ever before, and look forward to that continuing into the new year.</p>
        <hr>    
        <h5 class="blue-text font-weight-bold">On a Personal Note;</h5>
        <br>
            <p>In my 2021 letter, I spoke of getting my job back following a world-altering pandemic, visiting Winnipeg for the first time, and hosting Real Ops for the first time. That seems like a hundred years ago after this past year.</p>
            
            <p>For those of you who aren’t aware, this past summer, I packed up my home in Halifax and moved three time zones away, to my new city in Edmonton. I’m still in the aviation industry - because of course I am - just with a new airline now, and a new place to call “home”, something I’m still getting used to doing. Ethan Mason, our Assistant Chief Instructor, deserves a special thank you here - not just any old friend would be willing to drive with you across the country, for seventy-ish hours just to move jobs. So, thanks for being a great friend, and helping me get settled in this new adventure. At least we’re in the same city and can drink about it together now.</p>
            
            <p>Is it hard to tell the past year has been filled with change? Here’s to hoping 2023 is a little less hectic - if I try and move again, someone hold me back, please. There is, however, one thing that hasn’t changed - this network. I’ve been here for over ten years now, somehow, and my love for this shared passion of ours still is as steady as ever. As we move into the new year together, I can’t wait to see what’s in store for Winnipeg, and for VATSIM.</p>
            
    <hr>
        <div>
            <p>Wishing you a safe, fun and cheerful holiday season, and a very happy new year as we look to 2023 together.</p>
            <h5 class="font-weight-bold mb-1">Nate Power</h5>
            <p class="mb-1">Winnipeg FIR Chief</p>
            <img src="https://i.imgur.com/7xuoHQ9.gif" style="width:10%">
        </div>
    </div>
    <br>
    <div class="row pl-2">
        <a class="btn btn-primary" href="/yearend2021"><i class="fas fa-arrow-left"></i> Read Nate's 2021 Letter</a>
        <a class="btn btn-primary" href="/yearend">Read Nate's 2023 Letter <i class="fas fa-arrow-right"></i></a>
    </div>
</div>
@endsection
