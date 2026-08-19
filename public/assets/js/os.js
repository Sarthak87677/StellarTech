/**
 * StellarTech OS — shell motion.
 *
 * Motion here is ONE-SHOT and driven by state change. Nothing loops at
 * rest. The streak flame fires only when the streak actually went up
 * since the member last looked; the bell rings only when a notification
 * arrives. A reload on its own animates nothing.
 *
 * NOTE ON GLOBAL SCOPE: space.js declares `reduceSpace`, app.js declares
 * `reduce`. These files share global scope, so a repeated name kills the
 * later file silently. This one uses `reduceOs`. Keep all three distinct.
 */
const reduceOs = matchMedia('(prefers-reduced-motion: reduce)').matches;

(function () {
  'use strict';

  /* ---------- helpers ---------- */

  // Add a class for exactly as long as its animation runs, then strip it
  // so the same effect can fire again next time. Duration is explicit
  // rather than listening for animationend: a celebration fires several
  // animations at once (flame + five staggered embers) and the shortest
  // one finishing must not cut the others off.
  function oneShot(el, cls, ms) {
    if (!el || reduceOs) return;
    el.classList.remove(cls);
    void el.offsetWidth;                     // restart a re-triggered animation
    el.classList.add(cls);
    clearTimeout(el['_t_' + cls]);
    el['_t_' + cls] = setTimeout(function () { el.classList.remove(cls); }, ms);
  }

  const TIERS = [[21, 't4'], [7, 't3'], [3, 't2'], [1, 't1'], [0, 't0']];
  function tierFor(days) {
    for (let i = 0; i < TIERS.length; i++) if (days >= TIERS[i][0]) return TIERS[i][1];
    return 't0';
  }
  // A streak crossing a boundary changes tier, so the celebration has to
  // restyle the flame as well as roll the number.
  function applyTier(flame, days) {
    TIERS.forEach(function (t) { flame.classList.remove('flame--' + t[1]); });
    flame.classList.add('flame--' + tierFor(days));
  }

  /* ---------- streak flame ---------- */

  const FLAME_KEY = function (userId) { return 'st.streak.' + userId; };

  // Count from -> to over ~600ms. Under reduced motion the number still
  // has to end up correct, it just gets there immediately.
  function setLen(el, value) {
    el.setAttribute('data-len', String(value).length);
  }

  function rollNumber(el, from, to) {
    if (!el) return;
    setLen(el, to);                          // size the digits before they land
    if (reduceOs) { el.textContent = to; return; }
    const start = performance.now(), span = 600, delta = to - from;
    (function step(now) {
      const t = Math.min(1, (now - start) / span);
      const eased = 1 - Math.pow(1 - t, 3);
      el.textContent = Math.round(from + delta * eased);
      if (t < 1) requestAnimationFrame(step);
      else el.textContent = to;
    })(start);
  }

  function celebrate(flame, from, to) {
    applyTier(flame, to);
    rollNumber(flame.querySelector('[data-flame-n]'), from, to);
    // 1250ms covers the 700ms flame pop plus the last ember (280ms delay
    // + 900ms travel). Cutting it shorter kills embers mid-rise.
    oneShot(flame, 'is-celebrating', 1250);
  }

  function initFlame(flame) {
    const streak = parseInt(flame.dataset.streak, 10) || 0;
    const userId = flame.dataset.user || '0';
    const key = FLAME_KEY(userId);

    // At risk: nothing logged today and the MEMBER's clock is past 18:00.
    // The server cannot decide this - it does not know their timezone.
    const loggedToday = flame.dataset.loggedToday === '1';
    if (streak > 0 && !loggedToday && new Date().getHours() >= 18) {
      flame.classList.add('is-at-risk');
    }

    let previous = null;
    try { previous = localStorage.getItem(key); } catch (e) { /* private mode */ }

    // Celebrate only on a genuine increase. First ever visit records the
    // baseline silently - there is nothing to celebrate yet.
    if (previous !== null && streak > parseInt(previous, 10)) {
      celebrate(flame, parseInt(previous, 10), streak);
    }
    try { localStorage.setItem(key, streak); } catch (e) { /* ignore */ }
  }

  /* ---------- notification bell ---------- */

  function setBellCount(bell, count) {
    const badge = bell.querySelector('[data-bell-badge]');
    const previous = parseInt(bell.dataset.count, 10) || 0;
    count = Math.max(0, count | 0);

    bell.dataset.count = count;
    if (badge) {
      badge.textContent = count;
      badge.hidden = count === 0;            // no badge at zero, ever
    }
    bell.setAttribute('aria-label',
      'Notifications' + (count ? ', ' + count + ' unread' : ''));

    if (count > previous) {
      oneShot(bell, 'is-ringing', 750);      // three damping oscillations
      oneShot(bell, 'is-counting', 470);     // badge pop
    }
  }

  /* ---------- boot ---------- */

  document.querySelectorAll('[data-flame]').forEach(initFlame);

  /**
   * Small surface for the rest of the shell (and the /dev/motion demo)
   * to drive these components. The data layer lands in step 7; until
   * then nothing calls notify() in production, so the bell stays still.
   */
  function flames(target) {
    if (target) return [target];
    return Array.prototype.slice.call(document.querySelectorAll('[data-flame]'));
  }
  function bells(target) {
    if (target) return [target];
    return Array.prototype.slice.call(document.querySelectorAll('[data-bell]'));
  }

  window.OsMotion = {
    reduced: reduceOs,

    /** Roll to a new streak with the celebration. Omit target in the real
     *  top bar, where there is only one flame. */
    celebrateStreak: function (to, from, target) {
      flames(target).forEach(function (flame) {
        const current = parseInt(flame.dataset.streak, 10) || 0;
        const start = (from === undefined) ? current : from;
        flame.dataset.streak = to;
        flame.classList.remove('is-at-risk');
        celebrate(flame, start, to);
        try { localStorage.setItem(FLAME_KEY(flame.dataset.user || '0'), to); } catch (e) {}
      });
    },

    /** Set the streak with no animation at all. */
    setStreak: function (days, target) {
      flames(target).forEach(function (flame) {
        flame.dataset.streak = days;
        applyTier(flame, days);
        const n = flame.querySelector('[data-flame-n]');
        if (n) { n.textContent = days; setLen(n, days); }
        try { localStorage.setItem(FLAME_KEY(flame.dataset.user || '0'), days); } catch (e) {}
      });
    },

    /** A notification arrived. Rings and pops only because the count rose. */
    notify: function (count, target) {
      bells(target).forEach(function (bell) {
        const next = (count === undefined)
          ? (parseInt(bell.dataset.count, 10) || 0) + 1
          : count;
        setBellCount(bell, next);
      });
    },

    /** Set the count outright. Going down never animates. */
    setBell: function (count, target) {
      bells(target).forEach(function (bell) { setBellCount(bell, count); });
    }
  };
})();
