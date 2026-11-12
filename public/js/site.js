/* =============================================================
   AI for Impact Awards — shared interactions
   ============================================================= */
(function(){
  'use strict';

  /* ---- Nav: mobile toggle + scroll state ---- */
  var nav = document.querySelector('.nav');
  var burger = document.querySelector('.burger');
  if(burger && nav){
    burger.addEventListener('click', function(){
      var open = nav.classList.toggle('open');
      burger.setAttribute('aria-expanded', open);
      document.body.style.overflow = open ? 'hidden' : '';
    });
    nav.querySelectorAll('.nav-menu a').forEach(function(a){
      a.addEventListener('click', function(){ nav.classList.remove('open'); document.body.style.overflow=''; burger.setAttribute('aria-expanded','false'); });
    });
  }
  // solid nav on scroll (unless flagged always-solid)
  var alwaysSolid = nav && nav.classList.contains('solid');
  function onScroll(){ if(nav && !alwaysSolid) nav.classList.toggle('scrolled', window.scrollY > 24); }
  window.addEventListener('scroll', onScroll); onScroll();

  /* ---- Reveal on scroll ---- */
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } });
  }, {threshold:.12});
  document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });
  window.revealNew = function(root){ (root||document).querySelectorAll('.reveal:not(.in)').forEach(function(el){ el.classList.add('in'); }); };

  /* ---- Count-up ---- */
  var cio = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(!e.isIntersecting) return;
      var el = e.target, target = +el.dataset.count, pre = el.dataset.prefix||'', suf = el.dataset.suffix||'', dur = 1400, start = performance.now();
      function tick(now){ var p = Math.min((now-start)/dur,1), eased = 1-Math.pow(1-p,3); el.textContent = pre + Math.round(target*eased) + suf; if(p<1) requestAnimationFrame(tick); }
      requestAnimationFrame(tick); cio.unobserve(el);
    });
  }, {threshold:.5});
  document.querySelectorAll('[data-count]').forEach(function(c){ cio.observe(c); });

  /* ---- Accordions (one open per group) ---- */
  document.querySelectorAll('[data-accordion]').forEach(function(group){
    group.querySelectorAll('.acc-head').forEach(function(btn){
      btn.addEventListener('click', function(){
        var acc = btn.parentElement, body = acc.querySelector('.acc-body'), open = acc.classList.contains('open');
        group.querySelectorAll('.acc').forEach(function(a){ a.classList.remove('open'); a.querySelector('.acc-body').style.maxHeight=null; a.querySelector('.acc-head').setAttribute('aria-expanded','false'); });
        if(!open){ acc.classList.add('open'); body.style.maxHeight = body.scrollHeight+'px'; btn.setAttribute('aria-expanded','true'); }
      });
    });
  });

  /* ---- Subnav scroll-spy ---- */
  var spy = document.querySelectorAll('.subnav a[data-spy]');
  if(spy.length){
    var map = {}; spy.forEach(function(a){ map[a.dataset.spy]=a; });
    var sio = new IntersectionObserver(function(entries){
      entries.forEach(function(e){
        if(e.isIntersecting){ spy.forEach(function(a){ a.classList.remove('active'); }); var l = map[e.target.id]; if(l){ l.classList.add('active'); l.scrollIntoView({inline:'center',block:'nearest'}); } }
      });
    }, {rootMargin:'-45% 0px -50% 0px'});
    spy.forEach(function(a){ var el = document.getElementById(a.dataset.spy); if(el) sio.observe(el); });
  }

  /* ---- Newsletter ---- */
  var nlBtn = document.getElementById('nlBtn'), nlEmail = document.getElementById('nlEmail'), nlNote = document.getElementById('nlNote');
  function isEmail(v){ return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v); }
  if(nlBtn){
    function sub(){ if(isEmail(nlEmail.value)){ nlNote.style.display='block'; nlEmail.value=''; } else { nlEmail.focus(); nlEmail.style.borderColor='#c0392b'; } }
    nlBtn.addEventListener('click', sub);
    nlEmail.addEventListener('keydown', function(e){ if(e.key==='Enter') sub(); nlEmail.style.borderColor=''; });
  }

  /* ---- YouTube reel modal ---- */
  var reelBtn = document.getElementById('reelBtn'), reelModal = document.getElementById('reelModal');
  if(reelBtn && reelModal){
    var mount = reelModal.querySelector('.modal-video');
    function openR(){ var id = reelBtn.dataset.video; mount.innerHTML = '<iframe src="https://www.youtube.com/embed/'+id+'?autoplay=1&rel=0" title="Highlight reel" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe>'; reelModal.classList.add('open'); document.body.style.overflow='hidden'; }
    function closeR(){ reelModal.classList.remove('open'); mount.innerHTML=''; document.body.style.overflow=''; }
    reelBtn.addEventListener('click', openR);
    reelModal.querySelectorAll('[data-close]').forEach(function(el){ el.addEventListener('click', closeR); });
    window.addEventListener('keydown', function(e){ if(e.key==='Escape' && reelModal.classList.contains('open')) closeR(); });
  }

  /* ---- Generic form validation ---- */
  window.validateForm = function(scope){
    var ok = true;
    scope.querySelectorAll('[data-required]').forEach(function(f){
      var field = f.closest('.field') || f.closest('.check') || f;
      var bad = false;
      if(f.type==='checkbox'){ bad = !f.checked; }
      else if(f.type==='file'){ bad = !f.files || !f.files.length; }
      else { bad = !String(f.value||'').trim(); if(f.dataset.email!==undefined && f.value && !isEmail(f.value)) bad = true; }
      if(field.classList){ field.classList.toggle('bad', bad); }
      if(bad) ok = false;
    });
    // at least one checkbox in a [data-check-group]
    scope.querySelectorAll('[data-check-group]').forEach(function(g){
      var any = g.querySelectorAll('input[type=checkbox]:checked').length > 0;
      g.classList.toggle('bad', !any); if(!any) ok = false;
    });
    return ok;
  };
})();
