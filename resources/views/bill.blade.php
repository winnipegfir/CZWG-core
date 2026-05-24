@extends('layouts.master')
@section('title', 'You found an Easter Egg!')
@section('content')

<style>
#whonk-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,0.85);
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 1.5rem;
    cursor: pointer;
}
#whonk-overlay.active {
    display: flex;
    animation: whonk-in 0.15s ease-out;
}
@keyframes whonk-in {
    from { opacity: 0; transform: scale(1.04); }
    to   { opacity: 1; transform: scale(1); }
}
#whonk-text {
    font-size: clamp(5rem, 20vw, 12rem);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1;
    text-shadow: 0 0 60px rgba(255,220,0,0.6), 0 0 120px rgba(255,180,0,0.3);
    animation: whonk-pop 0.2s cubic-bezier(0.36, 0.07, 0.19, 0.97);
    transform-origin: center;
    user-select: none;
}
@keyframes whonk-pop {
    0%   { transform: scale(0.4) rotate(-8deg); opacity: 0; }
    60%  { transform: scale(1.12) rotate(3deg); }
    80%  { transform: scale(0.95) rotate(-1deg); }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
}
#whonk-sub {
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    letter-spacing: 0.05em;
    margin-top: -0.5rem;
}

#bill-img {
    cursor: pointer;
    transition: transform 0.15s ease;
    max-width: 220px;
}
#bill-img:hover {
    transform: scale(1.05) rotate(-2deg);
}
</style>

<div class="container py-5">
    <h1><strong>Congrats! You've found an Easter Egg.</strong></h1>
    <p>We don't really have anything to give you as a gift, but please feel free to click on good ol' Bill below for a free whonking.</p>
    <hr>
    <img id="bill-img" src="https://static.wikia.nocookie.net/animalcrossing/images/c/c1/Bill_NH.png" onclick="clickBill()" alt="Bill">
    <br><br>
    <a href="{{ route('index') }}" style="font-size:1.1rem; color:#122b44;"><i class="fas fa-arrow-left mr-1"></i> Take Me Home (Country Roads)</a>
</div>

<div id="whonk-overlay" onclick="dismissWhonk()">
    <div id="whonk-text">WHONK!</div>
    <div id="whonk-sub">click anywhere to dismiss</div>
</div>

<script>
var whonkCount = 0;
var extras = ['WHONK!', 'WHONK!!', 'W H O N K', 'ok ok ok', 'WHONK?!', 'again?!', 'BILL APPROVES', '...whonk', 'HONK SHOO', 'WHAM'];

function clickBill() {
    var overlay = document.getElementById('whonk-overlay');
    var txt = document.getElementById('whonk-text');

    txt.textContent = whonkCount === 0 ? 'WHONK!' : extras[Math.floor(Math.random() * extras.length)];
    whonkCount++;

    overlay.classList.remove('active');
    void overlay.offsetWidth;
    overlay.classList.add('active');

    txt.style.animation = 'none';
    void txt.offsetWidth;
    txt.style.animation = '';
}

function dismissWhonk() {
    document.getElementById('whonk-overlay').classList.remove('active');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') dismissWhonk();
});
</script>

@endsection
