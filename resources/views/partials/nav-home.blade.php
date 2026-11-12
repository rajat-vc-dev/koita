@php($sectionPrefix = request()->routeIs('home') ? '' : rtrim(route('home'), '/').'/')

<div class="top-marquee">
    <div class="top-marquee-track">
        <span>Registrations are now closed. Audience Choice Awards open on 19 November 2026.</span>
    </div>
</div>
<header class="nav" id="nav">
  <div class="wrap nav-inner">
    <a href="{{ $sectionPrefix }}#top" class="brand" aria-label="The ILSS KCADT AI Awards 2026 home">
      <img src="https://aiawards.indialeadersforsocialsector.com/assets/koita/kcadt-ai-awards-logo.webp" alt="The ILSS - KCADT AI Awards">
	</a>
    <nav class="nav-links" id="navlinks">
      <!-- <a href="#about" class="navlink">About</a> -->

      <a href="{{ $sectionPrefix }}#categories" class="navlink">Award Categories</a>
      <a href="{{ $sectionPrefix }}#how" class="navlink">Process & Timelines</a>
      <a href="{{ $sectionPrefix }}#eligibility" class="navlink">Eligibility & Evaluation</a>
      <!-- <a href="#jury" class="navlink">Jury</a> -->
      <a href="{{ $sectionPrefix }}#winners" class="navlink">KF Tech Awards 2025</a>
      <a href="{{ $sectionPrefix }}#faq" class="navlink">FAQs</a>


      <div class="nav-actions">
        <a href="javascript:void(0);" class="btn btn-outline btn-sm" id="navRegister">Voting Opens Nov 19 <span class="arw">
  <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
  </svg>
</span></a>
      </div>
    </nav>
    <button class="burger" id="burger" aria-label="Toggle menu" aria-expanded="false"><span></span><span></span><span></span></button>
  </div>
  <div class="nav-scrim" id="navScrim"></div>
</header>

@pushOnce('scripts')
    <script src="{{ asset('js/site-navigation.js') }}" defer></script>
@endPushOnce
