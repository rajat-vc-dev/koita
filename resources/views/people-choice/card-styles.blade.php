<style>
.top-marquee {
  display: none;
}
header.nav {
  top: 0px;
  border-bottom: 1px solid #f2f2f2;
}
.poll-heading h1 {
  margin-top: 40px;
}
.poll-tray {
  margin-bottom: 30px;
  border: 1px solid var(--poll-border);
  border-radius: 100px;
}
#poll-submit {
  cursor: pointer;
}
.poll-card-banner img {
  width: 100%;
  height: inherit;
  border-radius: 0px;
}

/* =====================================================================
   PREMIUM CARD LAYOUT — design styles (no JS changes)
   ===================================================================== */
.poll-shell {
  --pc-ink:#120a1c; --pc-plum:#2a1438;
  --pc-gold:#c9a24a; --pc-gold-hi:#f1d99a; --pc-gold-lo:#8a6a1c;
  --pc-ivory:#fffdf8; --pc-text:#221a2b; --pc-muted:#756c7f; --pc-hair:#ece5dc;
  --pc-gold-grad:linear-gradient(135deg,#f3dc9a 0%,#c9a24a 45%,#8a6a1c 100%);
  background:#fff;
}
.poll-shell *, .poll-shell *::before, .poll-shell *::after { box-sizing:border-box; }

/* heading */
.poll-shell .poll-heading { text-align:center; margin-bottom:44px; }
.poll-shell .poll-heading h1 { font-size:clamp(34px,5vw,56px); line-height:1.1; letter-spacing:-1px; color:var(--pc-ink); }
.poll-shell .poll-heading h1::after {
  content:""; display:block; width:64px; height:4px; margin:18px auto 0; border-radius:4px; background:var(--gold);
}
.poll-shell .poll-heading p { max-width:640px; margin:14px auto 0; font-size:15px; line-height:1.7; color:var(--pc-muted); }

/* intro points */
.poll-shell .poll-intro {
  list-style:none; margin:30px auto 0; padding:0; max-width:1080px;
  display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; text-align:left;
}
.poll-shell .poll-intro li {
  display:flex; align-items:center; gap:16px; margin:0;
  padding:18px 20px; border-radius:18px; background:#fff;
  border:1.5px solid #f0ebe3;
  box-shadow:0 2px 6px rgba(18,10,28,.04), 0 14px 30px -16px rgba(18,10,28,.22);
  font-size:15px; line-height:1.5; color:var(--pc-text);
  transition:transform .3s, box-shadow .3s, border-color .3s;
}
.poll-shell .poll-intro li:hover { transform:translateY(-3px); border-color:var(--gold); box-shadow:0 4px 10px rgba(18,10,28,.05), 0 20px 40px -18px rgba(18,10,28,.3); }
.poll-shell .poll-intro li strong { color:var(--deep-plum); font-weight:800; }
.poll-shell .poll-intro-icon {
  flex-shrink:0; width:48px; height:48px; border-radius:14px;
  display:flex; align-items:center; justify-content:center;
  background:color-mix(in srgb, var(--gold) 22%, #fff); color:var(--ink);
}
.poll-shell .poll-intro-icon svg { width:24px; height:24px; }
/* "List of Organisations" heading */
.poll-shell .poll-list-title {
  display:flex; align-items:center; gap:18px;
  margin:0 0 8px; padding:0;
  font-size:clamp(22px,2.6vw,28px); font-weight:700; letter-spacing:-.3px; color:var(--pc-ink);
}
.poll-shell .poll-list-title::before {
  content:""; flex-shrink:0; width:6px; height:28px; border-radius:6px; background:var(--gold);
}
.poll-shell .poll-list-title::after {
  content:""; flex:1; height:1px; background:#ece5dc;
}
@media (max-width:900px){
  .poll-shell .poll-intro { grid-template-columns:1fr; max-width:560px; gap:12px; }
  .poll-shell .poll-intro li { padding:14px 16px; font-size:14.5px; }
  .poll-shell .poll-intro-icon { width:42px; height:42px; border-radius:12px; }
}

/* grid: 4 in a row */
.poll-shell .poll-grid {
	display: grid;
  grid-template-columns: repeat(3,minmax(0,1fr));
  gap: 26px;
}
@media (max-width:1150px){ .poll-shell .poll-grid { grid-template-columns:repeat(3,minmax(0,1fr)); } }
@media (max-width:860px){ .poll-shell .poll-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media (max-width:560px){ .poll-shell .poll-grid { grid-template-columns:1fr; } }

/* =====================================================================
   CARD LAYOUT — round logo overlapping the top edge, pills, quote
   Colours: only your page variables (--gold, --ink, --deep-plum, --pc-*)
   ===================================================================== */
.poll-shell .poll-grid { row-gap: 52px; padding-top: 30px; }

/* card */
.poll-shell .poll-card {
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: visible;
  background: #fff;
  border: 1.5px solid #f0ebe3;
  border-radius: 22px;
  box-shadow: 0 2px 6px rgba(18,10,28,.04), 0 18px 40px -14px rgba(18,10,28,.22);
  transition: transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s, border-color .3s, opacity .3s;
}
.poll-shell .poll-card:hover { transform:translateY(-6px); box-shadow:0 4px 10px rgba(18,10,28,.05), 0 30px 56px -18px rgba(18,10,28,.32); }

/* round logo sitting over the top edge */
.poll-shell .poll-card-banner {
  position: absolute;
  top: -28px;
  left: 26px;
  z-index: 2;
  width: 114px;
  height: 114px;
  margin: 0;
  padding: 8px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: visible;
  box-shadow: 0 4px 28px -14px rgba(18,10,28,.45);
  transition: transform .5s cubic-bezier(.3,1.6,.5,1), box-shadow .3s;
}
.poll-shell .poll-card:hover .poll-card-banner { transform: scale(1.06); }
.poll-shell .poll-card-banner img {
  width: auto; height: auto; /* max-width: 76%; max-height: 62%; */ object-fit: contain; border-radius: 0; transition: none;
}
.poll-shell .poll-card:hover .poll-card-banner img { transform: none; }
.poll-shell .poll-card-banner > span:not(.poll-card-rank) {
  width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 28px; font-weight: 700; color: var(--ink); text-transform: uppercase;
  background: #faf5ea; box-shadow: none;
}

/* rank badge (your JS fills + un-hides it) — sits on the logo */
.poll-shell .poll-card-rank {
  position: absolute;
  top: auto;
  bottom: -6px;
  right: -10px;
  left: auto;
  z-index: 3;
  width: auto;
  min-width: 44px;
  height: 28px;
  padding: 0 10px;
  border-radius: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 800;
  background: var(--gold);
  color: var(--ink);
  border: 2px solid #fff;
  box-shadow: 0 6px 14px -6px rgba(18,10,28,.5);
  animation: pc-pop .45s cubic-bezier(.3,1.6,.5,1);
}
.poll-shell .poll-card-rank[hidden] { display:none; }
@keyframes pc-pop { from { transform:scale(0) rotate(-30deg); } }

/* body — order: name, AI solution, sector (each on its own line), tagline, buttons */
.poll-shell .poll-card-body {
  flex: 1;
  position: relative;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  align-content: flex-start;
  gap: 10px;
  padding: 26px 26px 96px; /* bottom space holds the buttons */
}
.poll-shell .poll-card-body > h2 {
  order: 1;
  flex: 0 0 100%;
  min-height: 56px;
  display: flex;
  align-items: center;
  margin: 0 0 4px;
  padding: 0 0 0 123px;
  border: 0;
  font-size: 19px;
  font-weight: 700;
  line-height: 1.25;
  letter-spacing: -.2px;
  color: var(--ink);
}

/* pills */
.poll-shell .poll-card .poll-solution,
.poll-shell .poll-card .poll-sector {
  display: inline-block;
  flex: 0 0 auto;
  max-width: 100%;
  margin: 0;
  padding: 6px 14px;
  border: 0;
  border-radius: 16px;
  font-size: 13px;
  font-weight: 500;
  line-height: 1.35;
  letter-spacing: 0;
  text-transform: none;
}
.poll-shell .poll-card .poll-solution {
  order: 2;
  color: var(--deep-plum);
  background: color-mix(in srgb, var(--deep-plum) 10%, #fff);
}
.poll-shell .poll-card .poll-sector {
  order: 3;
  color: var(--ink);
  background: color-mix(in srgb, var(--gold) 22%, #fff);
}
.poll-shell .poll-card .poll-solution > span,
.poll-shell .poll-card .poll-sector::before {
  display: inline;
  width: auto;
  margin: 0 4px 0 0;
  padding: 0;
  font-size: inherit;
  font-weight: 700;
  letter-spacing: 0;
  text-transform: none;
  color: inherit;
}
.poll-shell .poll-card .poll-solution > span::after { content: ":"; }
.poll-shell .poll-card .poll-sector::before { content: "Sector:"; }

/* tagline with quote icon (no heading) */
.poll-shell .poll-summary {
  order: 4;
  flex: 0 0 100%;
  display: flex;
  gap: 12px;
  margin: 4px 0 0;
  padding: 0;
  font-style: italic;
  font-size: 14.5px;
  line-height: 1.6;
  color: #5a5163;
}
.poll-shell .poll-summary::before {
  content: "";
  flex-shrink: 0;
  display: none;
  width: 26px;
  height: 21px;
  margin: 2px 0 0;
  vertical-align: 0;
  background: var(--gold);
  -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 24'%3E%3Cpath d='M0 24V14C0 6 4 1.5 12 0l1.4 3.2C9 4.6 7 7.4 6.8 11H12v13H0zm17 0V14c0-8 4-12.5 12-14l1 3.2c-4.4 1.4-6.4 4.2-6.6 7.8H29v13H17z'/%3E%3C/svg%3E") center/contain no-repeat;
          mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 24'%3E%3Cpath d='M0 24V14C0 6 4 1.5 12 0l1.4 3.2C9 4.6 7 7.4 6.8 11H12v13H0zm17 0V14c0-8 4-12.5 12-14l1 3.2c-4.4 1.4-6.4 4.2-6.6 7.8H29v13H17z'/%3E%3C/svg%3E") center/contain no-repeat;
}

/* buttons */
.poll-shell .poll-actions {
  order: 5;
  position: absolute;
  left: 26px;
  right: 26px;
  bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin: 0;
}
.poll-shell .poll-actions > .poll-secondary {
  white-space: nowrap; cursor: pointer; font-size: 14px; font-weight: 700; color: var(--deep-plum);
  background: none; border: 0; box-shadow: none; padding: 8px 0; min-width: 0;
}
.poll-shell .poll-actions > .poll-secondary::after { content:"→"; display:inline-block; transition:transform .2s; margin-left:8px; }
.poll-shell .poll-actions > .poll-secondary:hover::after { transform:translateX(4px); }
.poll-shell .poll-actions > .poll-secondary[hidden] { display:none; }

.poll-shell .poll-choice { position:relative; margin-left:auto; cursor:pointer; }
.poll-shell .poll-choice input { position:absolute; opacity:0; width:1px; height:1px; pointer-events:none; }
.poll-shell .poll-choice .poll-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  min-width: 130px;
  white-space: nowrap;
  padding: 13px 26px;
  border: 0;
  border-radius: 40px;
  font-size: 14.5px;
  font-weight: 700;
  letter-spacing: .3px;
  background: var(--gold);
  color: var(--ink);
  transition: transform .3s var(--ease), box-shadow .3s var(--ease), background .3s, color .3s;
}
.poll-shell .poll-choice:hover .poll-button { transform: translateY(-2px); }
.poll-shell .poll-choice input:focus-visible + .poll-button { outline:2px solid var(--gold); outline-offset:3px; }
.poll-shell .poll-choice input:disabled + .poll-button { background:#eee9f1; color:#a49bae; box-shadow:none; cursor:not-allowed; }

/* selected card */
.poll-shell .poll-card:has(input:checked) {
  border-color: var(--deep-plum);
  box-shadow: 0 0 0 1px var(--deep-plum), 0 26px 50px -18px rgba(18,10,28,.35);
}
.poll-shell .poll-choice input:checked + .poll-button,
.poll-shell .poll-choice:hover input:checked + .poll-button {
  background: #fff;
  color: var(--deep-plum);
  box-shadow: inset 0 0 0 1.5px var(--deep-plum);
}
.poll-shell .poll-card:has(input:disabled:not(:checked)) { opacity:.45; }
.poll-shell .poll-card:has(input:disabled:not(:checked)):hover { opacity:.85; }

/* layout lock — keeps this card layout even if other site CSS styles .poll-card-body */
main.poll-shell .poll-card .poll-card-body {
  display: flex !important;
  flex-direction: column !important;
  flex-wrap: nowrap !important;
  align-items: flex-start !important;
  align-content: flex-start !important;
  justify-content: flex-start !important;
  grid-template-columns: none !important;
  grid-template-rows: none !important;
  grid-auto-flow: row !important;
  width: 100% !important;
  text-align: left !important;
}
main.poll-shell .poll-card .poll-card-body > h2 {
  flex: none !important;
  width: 100% !important;
  max-width: 100% !important;
  justify-content: flex-start !important;
  text-align: left !important;
  margin-bottom: 15px;
}
main.poll-shell .poll-card .poll-card-body > .poll-summary {
  flex: none !important;
  width: 100% !important;
  max-width: 100% !important;
  text-align: left !important;
}
main.poll-shell .poll-card .poll-card-body > .poll-solution,
main.poll-shell .poll-card .poll-card-body > .poll-sector {
  flex: none !important;
  align-self: flex-start !important;
  width: auto !important;
  max-width: 100% !important;
  position: static !important;
  white-space: normal !important;
  text-align: left !important;
}

/* logo lock — every card gets the same white circle (overrides any other site CSS) */
main.poll-shell .poll-card .poll-card-banner,
main.poll-shell .poll-card:nth-child(n) .poll-card-banner,
main.poll-shell .poll-card:has(input:checked) .poll-card-banner {
  background: #fff !important;
  border: 0 !important;
  outline: 0 !important;
  padding: 8px !important;
}
main.poll-shell .poll-card .poll-card-banner > span:not(.poll-card-rank) {
  background: #faf5ea !important;
  box-shadow: none !important;
  border: 0 !important;
  color: var(--ink) !important;
}
main.poll-shell .poll-card .poll-card-banner img {
  background: transparent !important;
  border: 0 !important;
}

@media (max-width:560px){
  .poll-shell .poll-card-banner { width:88px; height:88px; left:20px; }
  .poll-shell .poll-card-body > h2 { padding-left:96px; font-size:17px; }
  .poll-shell .poll-card-body { padding:22px 20px 90px; }
  .poll-shell .poll-actions { left:20px; right:20px; bottom:20px; }
}

/* bottom tray — fixed to the bottom of the screen.
   Hidden at first; it slides up once the user scrolls about 8% down the page
   (class added by the small script at the end of this file) or as soon as any vote is selected. */
.poll-shell { padding-bottom: 150px; } /* space so the last cards are not hidden behind the tray */
.poll-shell .poll-tray {
  position: fixed;
  left: 50%;
  bottom: 20px;
  transform: translate(-50%, calc(100% + 40px));
  opacity: 0;
  visibility: hidden;
  transition: transform .45s cubic-bezier(.2,.8,.2,1), opacity .3s, visibility 0s linear .45s;
  width: calc(100% - 40px);
  max-width: 1200px;
  z-index: 50;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 22px;
  padding: 18px 20px 18px 28px;
  border-radius: 24px;
  color: #fff;
  backdrop-filter: blur(18px) saturate(1.4);
  -webkit-backdrop-filter: blur(18px) saturate(1.4);
  border: 1px solid rgba(201,162,74,.35);
  box-shadow: 0 30px 70px -10px rgba(18,10,28,.45);
  background: var(--deep-plum);
}
/* tray visible: after scrolling, or when at least one vote is selected */
.poll-shell.is-tray-visible .poll-tray,
.poll-shell #poll-ballot:has(input:checked) .poll-tray {
  transform: translate(-50%, 0);
  opacity: 1;
  visibility: visible;
  transition: transform .45s cubic-bezier(.2,.8,.2,1), opacity .3s, visibility 0s;
}
.poll-shell .poll-tray > div { flex:1; min-width:0; }
.poll-shell #poll-count { margin:0; font-size:15px; font-weight:800; color:#fff; }
.poll-shell .poll-tray-hint { margin:6px 0 0; font-size:12px; color:#b5a9c0; }
.poll-shell .poll-ranked-choices { list-style:none; display:flex; flex-wrap:wrap; gap:8px; margin:12px 0 2px; padding:0; }
.poll-shell .poll-ranked-choices[hidden] { display:none; }
.poll-shell .poll-ranked-choices li {
  display:inline-flex; align-items:center; gap:6px; padding:6px 8px 6px 6px; border-radius:30px;
  background:linear-gradient(135deg,rgba(201,162,74,.22),rgba(255,255,255,.04));
  border:1px solid rgba(201,162,74,.5); color:#fff; font-size:12.5px; font-weight:700;
}
.poll-shell .poll-ranked-choices li > :first-child:not(button) {
  min-width: 24px;
  height: 24px;
  padding: 0 6px;
  border-radius: 30px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 11.5px;
  font-weight: 800;
  background: var(--gold);
  color: var(--ink);
}
.poll-shell .poll-ranked-choices li button {
  width: 22px;
  height: 22px;
  padding: 0;
  border: 0;
  border-radius: 50%;
  background: transparent;
  color: #fff;
  font-size: 25px;
  line-height: 1;
  cursor: pointer;
}
.poll-shell .poll-ranked-choices li button:hover:not(:disabled) { background:rgba(255,255,255,.15); }
.poll-shell .poll-ranked-choices li button:disabled { opacity:.25; cursor:default; }
.poll-shell #poll-submit {
  flex-shrink: 0;
  padding: 17px 30px;
  border: 0;
  border-radius: 16px;
  white-space: nowrap;
  font-size: 14px;
  font-weight: 800;
  letter-spacing: .4px;
  box-shadow: 0 10px 30px -8px rgba(201,162,74,.6);
  transition: transform .25s, box-shadow .25s;
  background: var(--gold);
  color: var(--ink);
}
.poll-shell #poll-submit:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 16px 34px -8px rgba(201,162,74,.8); }
.poll-shell #poll-submit:disabled {background:rgba(255,255,255,.08); color:#7f7489; box-shadow:none; cursor:not-allowed;}
.poll-button {
  display: inherit;
}
.poll-shell .poll-solution span {
  width: 100%;
  display: inherit;
  margin-bottom: 5px;
}
@media (max-width:700px){
  .poll-shell { padding-bottom: 230px; }
  .poll-shell .poll-tray { flex-direction:column; align-items:stretch; padding:16px; bottom:12px; width:calc(100% - 24px); gap:12px; }
  .poll-shell #poll-submit { width:100%; }
}

/* error */
.poll-shell .poll-error { max-width:640px; margin:0 auto 26px; padding:12px 18px; border-radius:12px; text-align:center;
  background:#fdf0ee; border:1px solid #f3c9c2; color:#9c3a2e; font-weight:600; font-size:14px; }

/* =====================================================================
   READ MORE PROFILE POPUP — wide & short layout
   Header (logo + name + details) on top, Problem / Solution / Impact
   side by side as three cards, links at the bottom.
   Colours: only your page variables (--gold, --ink, --deep-plum)
   ===================================================================== */
.poll-profile {
  --pp-hair: #ece5dc;
  --pp-text: #3f3848;
  --pp-tint: color-mix(in srgb, var(--deep-plum) 5%, #fff);
  box-sizing: border-box;
  width: min(960px, calc(100% - 32px));
  max-width: calc(100% - 32px);
  max-height: min(640px, calc(100vh - 48px));
  margin: auto;
  padding: 34px 36px 30px;
  border: 0;
  border-radius: 26px;
  background: #fff;
  color: var(--pp-text);
  box-shadow: 0 40px 100px rgba(18,10,28,.45);
  overflow-y: auto;
  overscroll-behavior: contain;
  counter-reset: pp-sec;
  /* standard scrollbar props stay "auto" so Chrome/Edge use the slim custom bar below */
  scrollbar-width: auto;
  scrollbar-color: auto;
}
.poll-profile[open] {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr));
  column-gap: 16px;
  row-gap: 0;
  align-content: start;
  animation: pp-in .35s cubic-bezier(.2,.9,.3,1.2);
}
.poll-profile * { box-sizing: border-box; }
.poll-profile::backdrop { background: rgba(18,10,28,.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
@keyframes pp-in { from { opacity:0; transform:translateY(16px) scale(.97); } }

/* slim scrollbar — no arrow buttons */
.poll-profile::-webkit-scrollbar { width: 6px; }
.poll-profile::-webkit-scrollbar-button { display: none; width: 0; height: 0; }
.poll-profile::-webkit-scrollbar-track { background: transparent; margin: 26px 0; }
.poll-profile::-webkit-scrollbar-thumb { background: color-mix(in srgb, var(--deep-plum) 25%, #fff); border-radius: 10px; }
.poll-profile::-webkit-scrollbar-thumb:hover { background: var(--deep-plum); }
@supports (-moz-appearance: none) {
  .poll-profile { scrollbar-width: thin; scrollbar-color: color-mix(in srgb, var(--deep-plum) 25%, #fff) transparent; }
}

/* close button */
.poll-profile-close { position: absolute; top: 18px; right: 18px; z-index: 5; margin: 0; height: auto; display: block; }
.poll-profile-close button {
  width: 40px; height: 40px; padding: 0 0 3px; border-radius: 50%; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  border: 1px solid var(--pp-hair); background: #fff; color: var(--ink);
  font-size: 24px; line-height: 1;
  transition: transform .25s, background .25s, color .25s;
}
.poll-profile-close button:focus { outline: none; }
.poll-profile-close button:focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; }
.poll-profile-close button:hover { background: var(--ink); color: #fff; transform: rotate(90deg); }

/* header — logo on the left */
.poll-profile-logo {
  float: none;
  grid-column: 1;
  grid-row: 1 / 5;
  align-self: start;
  width: 100%;
  max-width: 128px;
  aspect-ratio: 1;
  height: auto;
  margin: 0;
  padding: 14px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 0 0 1px var(--pp-hair), 0 14px 28px -14px rgba(18,10,28,.4);
  display: flex; align-items: center; justify-content: center;
}
.poll-profile-logo img { max-width: 78%; max-height: 62%; width: auto; height: auto; object-fit: contain; border-radius: 0; }
.poll-profile-logo span {
  width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 30px; font-weight: 700; color: var(--ink); text-transform: uppercase;
  background: #faf5ea; box-shadow: none;
}

/* header — name, pills, tagline on the right */
.poll-profile > h2 {
  grid-column: 2 / -1;
  overflow: visible;
  margin: 4px 0 12px;
  padding-right: 50px;
  font-size: 24px; font-weight: 700; line-height: 1.2; letter-spacing: -.3px; color: var(--ink);
}
.poll-profile > p:not(.poll-profile-text):not(.poll-hint) {
  grid-column: 2 / -1;
  justify-self: start;
  overflow: visible;
  margin: 0 0 8px;
  padding: 6px 14px;
  border: 0;
  border-radius: 16px;
  font-size: 13px; font-weight: 500; line-height: 1.35; color: var(--deep-plum);
  background: color-mix(in srgb, var(--deep-plum) 10%, #fff);
}
.poll-profile > p:nth-of-type(2):not(.poll-profile-text):not(.poll-hint) {
  color: var(--ink);
  background: color-mix(in srgb, var(--gold) 22%, #fff);
}
.poll-profile > p:not(.poll-profile-text):not(.poll-hint) strong {
  display: inline; min-width: 0; margin-right: 4px;
  font-size: inherit; font-weight: 700; letter-spacing: 0; text-transform: none; color: inherit; vertical-align: 0;
}
.poll-profile > p:not(.poll-profile-text):not(.poll-hint) strong::after { content: ":"; }
.poll-profile > p.poll-hint {
  grid-column: 2 / -1;
  display: flex; gap: 10px;
  margin: 4px 0 0; padding: 0; border: 0;
  font-weight: 400; font-style: italic; font-size: 15px; line-height: 1.55; color: #5a5163;
}
.poll-profile > p.poll-hint::before {
  content: ""; flex-shrink: 0; width: 22px; height: 18px; margin-top: 2px; vertical-align: 0;
  background: var(--gold); display: none;
  -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 24'%3E%3Cpath d='M0 24V14C0 6 4 1.5 12 0l1.4 3.2C9 4.6 7 7.4 6.8 11H12v13H0zm17 0V14c0-8 4-12.5 12-14l1 3.2c-4.4 1.4-6.4 4.2-6.6 7.8H29v13H17z'/%3E%3C/svg%3E") center/contain no-repeat;
          mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 24'%3E%3Cpath d='M0 24V14C0 6 4 1.5 12 0l1.4 3.2C9 4.6 7 7.4 6.8 11H12v13H0zm17 0V14c0-8 4-12.5 12-14l1 3.2c-4.4 1.4-6.4 4.2-6.6 7.8H29v13H17z'/%3E%3C/svg%3E") center/contain no-repeat;
}

/* Problem / Solution / Impact — three cards side by side */
.poll-profile > h3 {
  counter-increment: pp-sec;
  grid-column: span 2;
  grid-row: 10;
  clear: none;
  display: flex; align-items: center; gap: 10px;
  margin: 26px 0 0;
  padding: 18px 18px 10px;
  border-radius: 18px 18px 0 0;
  background: var(--pp-tint);
  font-size: 13px; font-weight: 800; letter-spacing: .5px; color: var(--ink);
}
.poll-profile > h3::before {
  content: counter(pp-sec, decimal-leading-zero);
  width: 28px; height: 28px; flex-shrink: 0; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 800; letter-spacing: 0;
  background: var(--gold); color: var(--ink);
  box-shadow: none;
}
.poll-profile > h3::after { content: none; }
.poll-profile > p.poll-profile-text {
  grid-column: span 2;
  grid-row: 11;
  margin: 0;
  padding: 0 18px 18px;
  border-radius: 0 0 18px 18px;
  background: var(--pp-tint);
  font-size: 13.5px; line-height: 1.65; color: var(--pp-text); white-space: pre-line;
}

/* links (CTAs) — shown right after the tagline */
.poll-profile-links {
  grid-column: 2 / -1;
  grid-row: 5;
  clear: none;
  display: flex; flex-wrap: wrap; gap: 10px;
  margin-top: 16px; padding-top: 0; border-top: 0;
}
.poll-profile-links:empty { display: none; }
.poll-profile-links a {
  display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border-radius: 40px;
  font-size: 13.5px; font-weight: 700; text-decoration: none; color: var(--deep-plum);
  background: #fff; border: 1.5px solid color-mix(in srgb, var(--deep-plum) 25%, #fff);
  transition: background .2s, border-color .2s, transform .2s, color .2s;
}
.poll-profile-links a::after { content: "↗"; font-size: 14px; transition: transform .2s; }
.poll-profile-links a:hover { border-color: var(--deep-plum); transform: translateY(-2px); }
.poll-profile-links a:hover::after { transform: translate(2px,-2px); }
.poll-profile-links a:first-child { background: var(--gold); color: var(--ink); border-color: var(--gold); }
.poll-profile-links a:first-child:hover { border-color: var(--gold); }
.poll-profile-links a[href$=".pdf" i]::after { content: "PDF"; font-size: 9.5px; font-weight: 800; letter-spacing: 1px; padding: 3px 6px; border-radius: 5px; background: var(--deep-plum); color: #fff; }

/* tablet / mobile — stack everything */
@media (max-width: 760px) {
  .poll-profile { padding: 26px 20px 22px; max-height: calc(100vh - 32px); }
  .poll-profile[open] { display: flex; flex-direction: column; align-items: flex-start; }
  .poll-profile > h3, .poll-profile > p.poll-profile-text { order: 2; align-self: stretch; }
  .poll-profile-links { order: 1; align-self: stretch; margin-bottom: 4px; }
  .poll-profile-logo { width: 96px; max-width: 96px; margin: 0 0 14px; }
  .poll-profile > h2 { font-size: 21px; }
  .poll-profile > p:not(.poll-profile-text):not(.poll-hint) { display: table; }
  .poll-profile > h3 { margin-top: 14px; }
}
</style>
