
/* =========================================================
   Data
   ========================================================= */
const FOCUS = ["All","Governance & Civic Tech","Justice & Legal Access","Healthcare & Wellbeing","Climate & Environment","Education & Learning"];

// Winner data + matching case-study photos from dt4si.com
const WINNERS = [
  {name:"CM Admin", area:"Governance & Civic Tech", tier:"1st Prize", tierClass:"r-champ",
   desc:"An AI-enabled assistant that streamlines citizen grievances and internal government workflows.",
   img:"https://dt4si.com/uploads/studies/1776681202_logo_Anudip%20Foundation_%20Bridging%20the%20Skills%20Gap%20through%20an%20Agentic%20AI%20Learning%20Ecosystem.png"},
  {name:"Akaai AI", area:"Justice & Legal Access", tier:"2nd Prize", tierClass:"r-first",
   desc:"AI tools that simplify legal research and courtroom documentation for faster access to justice.",
   img:"https://dt4si.com/uploads/studies/1777536793_logo_I-Stem_%20Dismantling%20the%20_Digital%20Apartheid_%20for%20People%20with%20Disabilities.webp"},
  {name:"Info-tek Pregnancy Chatbot", area:"Healthcare & Wellbeing", tier:"Popular Choice", tierClass:"r-pop",
   desc:"Conversational AI supporting expecting mothers with personalised guidance and timely care.",
   img:"https://dt4si.com/uploads/studies/1776681938_logo_Khan%20Academy.png"},
  {name:"Phonowal SNEHA Didi", area:"Climate & Environment", tier:"Popular Choice", tierClass:"r-pop",
   desc:"A community health-worker support platform powered by AI for better field outcomes.",
   img:"https://dt4si.com/uploads/studies/1776682170_logo_Magic%20Bus%20India%20Foundation_%20Democratizing%20Personalized%20Skilling%20through%20Magic%20AI.png"},
   {name:"Akaai AI", area:"Education & Learning", tier:"1st Prize", tierClass:"r-first",
   desc:"AI tools that simplify legal research and courtroom documentation for faster access to justice.",
   img:"https://dt4si.com/uploads/studies/1777536793_logo_I-Stem_%20Dismantling%20the%20_Digital%20Apartheid_%20for%20People%20with%20Disabilities.webp"},
   {name:"CM Admin", area:"Governance & Civic Tech", tier:"1st Prize", tierClass:"r-champ",
   desc:"An AI-enabled assistant that streamlines citizen grievances and internal government workflows.",
   img:"https://dt4si.com/uploads/studies/1776681202_logo_Anudip%20Foundation_%20Bridging%20the%20Skills%20Gap%20through%20an%20Agentic%20AI%20Learning%20Ecosystem.png"},
];

const FAQS = [
  {q:"Who can apply for The ILSS - KCADT AI AWARDS 2026?", a:"Indian non-profits (registered as a Trust, Society, or Section 8 Company) that have implemented AI-based solutions for social impact are eligible to apply."},
  {q:"What qualifies as an AI-based solution?", a:"A solution where machine learning, natural-language processing, computer vision or similar AI methods are central to how it delivers impact, not just a dashboard or basic automation."},
  {q:"Can early-stage ideas or prototypes apply?", a:"The awards prioritise solutions piloted with real users in the field. Very early prototypes with no deployment are usually not a fit yet."},
  {q:"Are for-profits eligible?", a:"Yes. For-profit social purpose organisations are eligible to apply. Service providers working with impact organisations are not eligible."},
  {q:"Is there a theme restriction?", a:"No single theme, solutions across health, education, livelihoods, governance, climate and beyond are all welcome."},
  {q:"What kind of impact are you looking for?", a:"We are looking for solutions that address meaningful challenges. Impact can be direct (e.g., improved outcomes) or systemic (e.g., improved policy design, data-driven decision-making)."},
  {
  q: "How does the evaluation process work?",
  a: [
    "Applications will be assessed against five criteria: Solution Design & Outcomes, Adoption & Community Validation, Responsible AI & Team Capability, Cost Efficiency, and Relevance & Alignment.",
    "A jury of experts will review submissions. Selected applicants may be invited for a solution demonstration and Q&A."
  ]
},
  {
    q: "How do you evaluate Responsible AI?",
    a: `Responsible AI refers to the design, development, and deployment of AI systems that prioritize fairness, transparency, accountability, privacy, and the minimization of bias or harm. In evaluation, we will assess whether your solution:
    <ul>
      <li>Ensures fairness and avoids discrimination.</li>
      <li>Protects data privacy and security.</li>
      <li>Is transparent about how AI decisions are made.</li>
      <li>Considers the broader social and environmental impact.</li>
    </ul>`
  },
  {
    q: "Is there a cash award or funding associated?",
    a: `Yes. Winners of The ILSS - KCADT AI AWARDS 2026 will receive the following cash prizes:
    <ul>
      <li>1st Prize – ₹20 Lakhs</li>
      <li>2nd Prize – ₹10 Lakhs</li>
      <li>3rd Prize – ₹5 Lakhs Each (3 category winners)</li>
      <li>People's Choice – ₹5 Lakhs</li>
    </ul>`
  },
  {q:"What recognition will winners receive?", a:"Beyond the cash awards, selected winners will also be profiled and featured at India Fundraising Conference (IFRC) and Digital Toolbook for Social Impact (DT4SI), the sector’s first digital knowledge platform for social impact. This visibility is designed to amplify their work, inspire others, and strengthen the overall AI-for-social-good ecosystem in India."},
 {q:"What if we miss the submission deadline?", a:'We hope you don’t! We may make some considerations. In case of personal or professional exigencies, do reach out to us at <a href="mailto:aiawards@indialeadersforsocialsector.com">aiawards@indialeadersforsocialsector.com</a>.'},
  {q:"Can I resubmit if I make an error or miss a document?", a:"All submissions are final. Please be careful and detail orientated when creating and submitting the application."},
  {q:"Unsure if you qualify?", a:"Register your interest anyway, our team will review your solution and guide you on eligibility before the form opens."},
  {q:"Can I apply again if I participated in the AI Awards last year?", a:"Yes. Previous-year applicants can apply again, provided they are submitting a new or substantially different AI solution from their previous submission."},
  {q:"Can a previous-year award winner apply again?", a:"Yes. Previous-year award winners can apply again, provided the new submission meets the eligibility criteria and is for a new or substantially different AI solution from the one previously awarded."},
];

/* =========================================================
   Winners
   ========================================================= */
const winTabs = document.getElementById('winTabs');
const winGrid = document.getElementById('winGrid');

function renderWinners(area){
  const list = area === "All" ? WINNERS : WINNERS.filter(w => w.area === area);
  if(!list.length){
    winGrid.innerHTML = `<div class="win-empty"><b>Case studies on the way</b>Winners in this focus area are being documented on DT4I — check back shortly or view the full list below.</div>`;
    return;
  }
  winGrid.innerHTML = list.map(w => `
    <article class="wcard reveal">
      <div class="thumb"><span class="wribbon ${w.tierClass}">${w.tier}</span>
        <img src="${w.img}" alt="${w.name}" loading="lazy" onerror="this.style.display='none'"></div>
      <div class="wbody">
        <h4>${w.name}</h4>
        <div class="worg">${w.area}</div>
        <p class="wdesc">${w.desc}</p>
        <div class="wactions"><a href="#" class="chip-btn chip-solid">Demo</a><a href="https://dt4si.com/digital-transformation-case-studies" target="_blank" rel="noopener" class="chip-btn chip-line">DT4I case study</a></div>
      </div>
    </article>`).join('');
  requestAnimationFrame(()=>winGrid.querySelectorAll('.reveal').forEach(el=>el.classList.add('in')));
}
FOCUS.forEach((f,i)=>{
  const b = document.createElement('button');
  b.className = 'tab' + (i===0 ? ' active':'');
  b.textContent = f;
  b.onclick = () => { winTabs.querySelectorAll('.tab').forEach(t=>t.classList.remove('active')); b.classList.add('active'); renderWinners(f); };
  winTabs.appendChild(b);
});
renderWinners("All");

/* =========================================================
   FAQ accordion
   ========================================================= */
const faqList = document.getElementById('faqList');

const BOLD_PHRASES = [
  "new or substantially different AI solution",
];

function boldPhrases(text, phrases){
  let result = text;
  phrases.forEach(phrase => {
    const escaped = phrase.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    result = result.replace(new RegExp(escaped, 'g'), `<strong>${phrase}</strong>`);
  });
  return result;
}

function renderAnswer(a){
  const paragraphs = Array.isArray(a) ? a : [a];
  return paragraphs.map(p => `<p>${boldPhrases(p, BOLD_PHRASES)}</p>`).join('');
}

faqList.innerHTML = FAQS.map((f,i)=>`
  <div class="faq-item reveal ${'d'+((i%3)+1)}">
    <button class="faq-q" aria-expanded="false">${f.q}<span class="fplus">+</span></button>
    <div class="faq-a">
      ${renderAnswer(f.a)}
      ${f.list ? `<ul>${f.list.map(item => `<li>${boldPhrases(item, BOLD_PHRASES)}</li>`).join('')}</ul>` : ''}
    </div>
  </div>`).join('');

faqList.querySelectorAll('.faq-q').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const item = btn.parentElement, ans = item.querySelector('.faq-a'), open = item.classList.contains('open');
    faqList.querySelectorAll('.faq-item').forEach(it=>{it.classList.remove('open');it.querySelector('.faq-a').style.maxHeight=null;it.querySelector('.faq-q').setAttribute('aria-expanded','false')});
    if(!open){item.classList.add('open');ans.style.maxHeight = ans.scrollHeight+'px';btn.setAttribute('aria-expanded','true');}
  });
});



/* =========================================================
   Sub-nav scroll-spy
   ========================================================= */
const spyLinks = [...document.querySelectorAll('.subnav a')];
const spyMap = {}; spyLinks.forEach(a=>spyMap[a.dataset.spy]=a);
const spyIO = new IntersectionObserver((entries)=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      spyLinks.forEach(a=>a.classList.remove('active'));
      const link = spyMap[e.target.id]; if(link){ link.classList.add('active'); link.scrollIntoView({inline:'center',block:'nearest'}); }
    }
  });
},{rootMargin:'-45% 0px -50% 0px'});
['about','categories','winners','jury','how','eligibility','gets','register','faq'].forEach(id=>{ const el=document.getElementById(id); if(el) spyIO.observe(el); });

/* =========================================================
   Reveal on scroll
   ========================================================= */
const io = new IntersectionObserver((entries)=>{ entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } }); }, {threshold:.14});
document.querySelectorAll('.reveal').forEach(el=>io.observe(el));

/* =========================================================
   Count-up stats
   ========================================================= */
const cio = new IntersectionObserver((entries)=>{
  entries.forEach(e=>{
    if(!e.isIntersecting) return;
    const el = e.target, target = +el.dataset.count, pre = el.dataset.prefix||'', suf = el.dataset.suffix||'', dur = 1400, start = performance.now();
    function tick(now){ const p = Math.min((now-start)/dur,1), eased = 1-Math.pow(1-p,3); el.textContent = pre + Math.round(target*eased) + suf; if(p<1) requestAnimationFrame(tick); }
    requestAnimationFrame(tick); cio.unobserve(el);
  });
},{threshold:.5});
document.querySelectorAll('[data-count]').forEach(c=>cio.observe(c));

/* =========================================================
   Register / Submit tabbed form
   ========================================================= */
const tabRegister = document.getElementById('tabRegister'), tabSubmit = document.getElementById('tabSubmit');
const formRegister = document.getElementById('usopen') || document.getElementById('formRegister');
const formSubmit = document.getElementById('formSubmit');
function showForm(which){
  if (!tabRegister || !formRegister) return;
  const reg = which==='register';
  tabRegister.classList.toggle('active',reg); if(tabSubmit) tabSubmit.classList.toggle('active',!reg);
  tabRegister.setAttribute('aria-selected',reg); if(tabSubmit) tabSubmit.setAttribute('aria-selected',!reg);
  formRegister.style.display = reg?'block':'none'; if(formSubmit) formSubmit.style.display = reg?'none':'block';
}
if (tabRegister) tabRegister.addEventListener('click',()=>showForm('register'));
if(tabSubmit) tabSubmit.addEventListener('click',()=>showForm('submit'));
// nav "Submit application" opens the submit tab
const navSubmitBtn = document.getElementById('navSubmit');
if(navSubmitBtn) navSubmitBtn.addEventListener('click',()=>setTimeout(()=>showForm('submit'),300));

// file upload label
const fileInput = document.getElementById('fileInput'), fileLabel = document.getElementById('fileLabel');
if(fileInput){ fileInput.addEventListener('change',()=>{ fileLabel.textContent = fileInput.files[0] ? fileInput.files[0].name : 'Click to upload your PDF / deck — max 25MB'; }); }

// Legacy client-side success handler for optional submit tab only
if (formSubmit) {
  function validate(form){
    let ok = true;
    form.querySelectorAll('[data-required]').forEach(f=>{
      let bad = false;
      if(f.type==='checkbox'){ bad = !f.checked; }
      else if(f.type==='file'){ bad = !f.files || !f.files.length; }
      else { bad = !f.value.trim(); if(f.dataset.email!==undefined && f.value && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(f.value)) bad = true; }
      f.classList.toggle('err',bad); if(bad) ok = false;
    });
    return ok;
  }
  formSubmit.querySelectorAll('.form-submit').forEach(btn=>{
    btn.addEventListener('click',(e)=>{
      e.preventDefault();
      if(!validate(formSubmit)) return;
      formSubmit.querySelectorAll('.fgrid,.field,.consent,.form-submit,.file-drop').forEach(el=>el.style.display='none');
      const success = document.getElementById('successSubmit');
      if (success) success.classList.add('show');
    });
  });
}

/* =========================================================
   Newsletter
   ========================================================= */
const nlBtn = document.getElementById('nlBtn'), nlEmail = document.getElementById('nlEmail'), nlNote = document.getElementById('nlNote');
function subscribe(){ if(/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(nlEmail.value)){ nlNote.style.display='block'; nlEmail.value=''; } else { nlEmail.focus(); nlEmail.style.borderColor='#c0392b'; } }
if(nlBtn) nlBtn.addEventListener('click', subscribe);
if(nlEmail) nlEmail.addEventListener('keydown', e=>{ if(e.key==='Enter') subscribe(); nlEmail.style.borderColor=''; });

/* =========================================================
   YouTube reel modal
   ========================================================= */
const reelBtn = document.getElementById('reelBtn'), reelModal = document.getElementById('reelModal'), reelMount = document.getElementById('reelMount');
function openReel(){
  const id = reelBtn.dataset.video; // REPLACE with the client's real YouTube video ID
  reelMount.innerHTML = `<iframe src="https://www.youtube.com/embed/${id}?autoplay=1&rel=0" title="2025 highlight reel" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe>`;
  reelModal.classList.add('open'); reelModal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden';
}
function closeReel(){ reelModal.classList.remove('open'); reelModal.setAttribute('aria-hidden','true'); reelMount.innerHTML=''; document.body.style.overflow=''; }
if(reelBtn) reelBtn.addEventListener('click', openReel);
if(reelModal){
  reelModal.querySelectorAll('[data-close]').forEach(el=>el.addEventListener('click', closeReel));
  addEventListener('keydown', e=>{ if(e.key==='Escape' && reelModal.classList.contains('open')) closeReel(); });
}

// Finalists slider

(function(){
  const track = document.getElementById('finTrack');
  const slider = document.getElementById('finSlider');
  const dotsWrap = document.getElementById('finDots');
  const cards = track.children.length;

  function visibleCount(){
    if (window.innerWidth <= 600) return 1;
    if (window.innerWidth <= 900) return 2;
    return 3;
  }

  let index = 0;
  let maxIndex = 0;

  function buildDots(){
    dotsWrap.innerHTML = '';
    const visible = visibleCount();
    maxIndex = Math.max(0, cards - visible);
    for(let i = 0; i <= maxIndex; i++){
      const dot = document.createElement('button');
      dot.className = 'fin-dot' + (i === index ? ' active' : '');
      dot.addEventListener('click', () => { index = i; update(); });
      dotsWrap.appendChild(dot);
    }
  }

  function cardStep(){
    const cardWidth = track.children[0].getBoundingClientRect().width;
    const gap = 20;
    return cardWidth + gap;
  }

  function update(){
    const visible = visibleCount();
    maxIndex = Math.max(0, cards - visible);
    index = Math.min(Math.max(index, 0), maxIndex);

    const offset = index * cardStep();
    track.style.transform = `translateX(-${offset}px)`;

    [...dotsWrap.children].forEach((d, i) => d.classList.toggle('active', i === index));
  }

  window.addEventListener('resize', () => { buildDots(); update(); });

  let isDown = false;
  let startX = 0;
  let startOffset = 0;
  let currentOffset = 0;

  function getX(e){
    return e.touches ? e.touches[0].clientX : e.clientX;
  }

  function dragStart(e){
    isDown = true;
    slider.style.cursor = 'grabbing';
    startX = getX(e);
    startOffset = index * cardStep();
    currentOffset = startOffset;
    track.style.transition = 'none';
  }

  function dragMove(e){
    if(!isDown) return;
    const dx = getX(e) - startX;
    currentOffset = startOffset - dx;
    track.style.transform = `translateX(-${currentOffset}px)`;
  }

  function dragEnd(){
    if(!isDown) return;
    isDown = false;
    slider.style.cursor = 'grab';
    track.style.transition = 'transform 0.4s ease';

    const step = cardStep();
    const movedBy = currentOffset - startOffset;

    if(Math.abs(movedBy) > step / 4){
      index += movedBy > 0 ? 1 : -1;
    }
    update();
  }

  slider.style.cursor = 'grab';
  slider.addEventListener('mousedown', dragStart);
  window.addEventListener('mousemove', dragMove);
  window.addEventListener('mouseup', dragEnd);

  slider.addEventListener('touchstart', dragStart, {passive:true});
  slider.addEventListener('touchmove', dragMove, {passive:true});
  slider.addEventListener('touchend', dragEnd);

  slider.addEventListener('dragstart', e => e.preventDefault());

  buildDots();
  update();
})();

// active nav items on id
document.addEventListener('DOMContentLoaded', function () {
  const navLinks = document.querySelectorAll('.navlink[href^="#"]');
  const sections = [];

  navLinks.forEach(link => {
    const id = link.getAttribute('href').slice(1);
    const section = document.getElementById(id);
    if (section) sections.push(section);
  });

  const setActive = (id) => {
    navLinks.forEach(link => {
      link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
    });
  };

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) setActive(entry.target.id);
      });
    },
    {
      root: null,
      rootMargin: '-80px 0px -60% 0px', // 80px ~ apke fixed header ki height, adjust kar lein
      threshold: 0
    }
  );

  sections.forEach(section => observer.observe(section));
});