/**
 * StellarTech — motion choreography.
 * Word-split headlines, staged hero entrance, directional scroll reveals,
 * hero parallax. No build step: edit this file and refresh.
 */
const nav = document.getElementById('nav');
if (nav) addEventListener('scroll',
  () => nav.classList.toggle('scrolled', scrollY > 24), {passive:true});
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ============================================================
   MOTION CHOREOGRAPHY
   1. Headlines split into words that fly in from alternating sides
   2. Hero enters as a staged sequence on load
   3. Everything below reveals directionally on scroll, staggered by
      group so grids cascade instead of snapping in all at once
   ============================================================ */

const WORD_IN = [
  {dx:-72, dy: 26, rz:-4},
  {dx: 76, dy:-22, rz: 4},
  {dx:  0, dy: 60, rz: 0, sc:.86},
  {dx:-60, dy:-30, rz: 3},
  {dx: 64, dy: 34, rz:-3},
  {dx:  0, dy:-54, rz: 0, sc:1.14}
];

function splitWords(root){
  let n = 0;
  (function walk(node){
    [...node.childNodes].forEach(child => {
      if (child.nodeType === 3) {
        const parts = child.textContent.split(/(\s+)/);
        const frag = document.createDocumentFragment();
        parts.forEach(part => {
          if (!part) return;
          if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
          const w = document.createElement('span'); w.className = 'w';
          const it = document.createElement('i');
          it.textContent = part;
          const v = WORD_IN[n % WORD_IN.length];
          it.style.setProperty('--dx', v.dx + 'px');
          it.style.setProperty('--dy', v.dy + 'px');
          it.style.setProperty('--rz', (v.rz || 0) + 'deg');
          if (v.sc) it.style.setProperty('--sc', v.sc);
          it.style.setProperty('--d', (n * 0.055) + 's');
          n++;
          w.appendChild(it); frag.appendChild(w);
        });
        node.replaceChild(frag, child);
      } else if (child.nodeType === 1 && child.tagName !== 'BR') {
        walk(child);
      }
    });
  })(root);
}

function choreograph(){
  const plan = [
    ['.sec-head',                       'up',    0.00],
    ['.mission-grid > div:first-child', 'left',  0.00],
    ['.mission-note',                   'right', 0.10],
    ['.disc-card',                      'tilt',  0.09],
    ['.news-card',                      'pop',   0.075],
    ['.founder',                        'up',    0.00],
    ['.oc-card',                        'pop',   0.08],
    ['.rakh',                           'up',    0.00],
    ['.rakh-list li',                   'right', 0.07],
    ['.slot',                           'right', 0.09],
    ['.path',                           'pop',   0.08],
    ['.cta',                            'pop',   0.00],
    ['.news-foot',                      'up',    0.00],
    ['.foot-grid > div',                'up',    0.07]
  ];
  plan.forEach(([sel, dir, step]) => {
    document.querySelectorAll(sel).forEach((el, i) => {
      if (el.hasAttribute('data-hero')) return;
      el.classList.remove('rv');
      el.setAttribute('data-rv', dir);
      if (step) el.style.setProperty('--d', (i * step) + 's');
    });
  });
  document.querySelectorAll('.rv:not([data-rv])').forEach(el => el.setAttribute('data-rv','up'));
}

// Section headings get the same word treatment, at a calmer amplitude
document.querySelectorAll('.sec-head h2, .rakh h2, .cta h2').forEach(h => {
  h.setAttribute('data-split',''); h.classList.add('split');
});

const heads = document.querySelectorAll('[data-split]');

if (reduce) {
  document.querySelectorAll('.rv, [data-rv]').forEach(el => el.classList.add('in'));
  heads.forEach(h => h.classList.add('go'));
} else {
  heads.forEach(splitWords);
  choreograph();

  // Hero: staged entrance on load
  const hero = [...document.querySelectorAll('[data-hero]')];
  const start = () => {
    const h1 = document.querySelector('h1[data-split]');
    if (h1) h1.classList.add('go');
    hero.forEach((el, i) => {
      el.style.setProperty('--d', (0.5 + i * 0.13) + 's');
      requestAnimationFrame(() => el.classList.add('in'));
    });
  };
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(() => setTimeout(start, 140));
  else addEventListener('load', () => setTimeout(start, 220));
  setTimeout(start, 2200);   // safety net if fonts stall

  // Below the fold: reveal on scroll
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const t = e.target;
      if (t.hasAttribute('data-split')) t.classList.add('go');
      else t.classList.add('in');
      io.unobserve(t);
    });
  }, {threshold:.14, rootMargin:'0px 0px -9% 0px'});

  document.querySelectorAll('[data-rv]:not([data-hero])').forEach(el => io.observe(el));
  document.querySelectorAll('[data-split]:not(h1)').forEach(el => io.observe(el));

  // Hero parallax: content drifts and fades as you scroll away
  const heroWrap = document.querySelector('.hero .wrap');
  const mark = document.querySelector('.hero-mark');
  let ticking = false;
  addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      const y = scrollY;
      if (y < innerHeight * 1.15 && heroWrap) {
        const p = y / innerHeight;
        heroWrap.style.transform = 'translate3d(0,' + (y * 0.26) + 'px,0)';
        heroWrap.style.opacity = Math.max(0, 1 - p * 1.4);
        if (mark) mark.style.transform =
          'translate3d(0,' + (y * 0.10) + 'px,0) scale(' + (1 - p * 0.07) + ')';
      }
      ticking = false;
    });
  }, {passive:true});
}

