/* =========================================================
   Header scrolled state
   ========================================================= */
(function(){
  const navEl = document.getElementById('nav');
  function onScroll(){
    navEl.classList.toggle('scrolled', window.scrollY > 40);
  }
  onScroll();
  addEventListener('scroll', onScroll, {passive:true});
})();

/* =========================================================
   Mobile burger menu
   ========================================================= */
(function(){
  const navEl = document.getElementById('nav');
  const burgerBtn = document.getElementById('burger');
  const scrim = document.getElementById('navScrim');
  const links = document.querySelectorAll('.nav-links a.navlink, .nav-links .nav-actions a');

  function openMenu(){
    navEl.classList.add('open');
    burgerBtn.setAttribute('aria-expanded','true');
    burgerBtn.setAttribute('aria-label','Close menu');
    document.body.classList.add('no-scroll');
  }
  function closeMenu(){
    navEl.classList.remove('open');
    burgerBtn.setAttribute('aria-expanded','false');
    burgerBtn.setAttribute('aria-label','Open menu');
    document.body.classList.remove('no-scroll');
  }
  burgerBtn.addEventListener('click', ()=>{
    navEl.classList.contains('open') ? closeMenu() : openMenu();
  });
  scrim.addEventListener('click', closeMenu);
  links.forEach(a=>a.addEventListener('click', closeMenu));
  addEventListener('keydown', e=>{ if(e.key==='Escape' && navEl.classList.contains('open')) closeMenu(); });
  addEventListener('resize', ()=>{ if(window.innerWidth > 992 && navEl.classList.contains('open')) closeMenu(); });
})();
