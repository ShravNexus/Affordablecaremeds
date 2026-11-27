/* NoRx Dose - shared behaviour (marquee, sticky shell, mobile menu)
   Loaded on every page via resources/views/components/baselayout.blade.php */
(function () {
  'use strict';

  /* ---- marquee loop: clone the group so the scroll never shows a gap ---- */
  /* ---- Christmas strip: countdown ---- */
  var xmas = document.getElementById('xmasStrip');
  if (xmas) {
    var xmasClock = xmas.querySelector('[data-countdown-to]');
    var xmasEnd = xmasClock ? Date.parse(xmasClock.dataset.countdownTo || '') : NaN;
    if (xmasClock && !Number.isNaN(xmasEnd)) {
      var two = function (n) { return String(n).padStart(2, '0'); };
      var xmasTimer;
      var xmasTick = function () {
        var left = xmasEnd - Date.now();
        if (left <= 0) {
          xmasClock.hidden = true;
          clearInterval(xmasTimer);
          return;
        }
        var s = Math.floor(left / 1000);
        xmasClock.querySelector('[data-unit="d"]').textContent = Math.floor(s / 86400);
        xmasClock.querySelector('[data-unit="h"]').textContent = two(Math.floor(s % 86400 / 3600));
        xmasClock.querySelector('[data-unit="m"]').textContent = two(Math.floor(s % 3600 / 60));
        xmasClock.querySelector('[data-unit="s"]').textContent = two(s % 60);
      };
      xmasTick();
      xmasTimer = setInterval(xmasTick, 1000);
    }
  }

  ['ticker', 'xmasTrack'].forEach(function (id) {
    var track = document.getElementById(id);
    if (track && track.firstElementChild) {
      var copy = track.firstElementChild.cloneNode(true);
      copy.setAttribute('aria-hidden', 'true');
      track.appendChild(copy);
    }
  });

  /* ---- sticky shell: publish its height so in-page sticky sidebars can sit
         clear of it without hard-coding the ticker + nav heights ---- */
  var shellTop = document.getElementById('shellTop');
  if (shellTop) {
    var syncHeight = function () {
      document.documentElement.style.setProperty(
        '--shell-top-h', shellTop.offsetHeight + 'px'
      );
    };
    if (window.ResizeObserver) {
      new ResizeObserver(syncHeight).observe(shellTop);
    } else {
      window.addEventListener('resize', syncHeight, { passive: true });
    }
    syncHeight();
  }

  /* ---- sticky nav ---- */
  var nav = document.getElementById('nav');
  if (nav) {
    var onScroll = function () {
      nav.classList.toggle('is-stuck', window.scrollY > 12);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---- mobile search ----
     The desktop form stays the only search implementation. On narrow screens
     this button simply reveals that same form as a small dropdown. */
  var mobileSearchToggle = document.getElementById('mobileSearchToggle');
  var searchBox = document.getElementById('searchBox');
  var searchInput = document.getElementById('q');
  var setSearchOpen = null;

  if (nav && mobileSearchToggle && searchBox) {
    setSearchOpen = function (open) {
      nav.classList.toggle('is-search-open', open);
      mobileSearchToggle.setAttribute('aria-expanded', open);
      mobileSearchToggle.setAttribute('aria-label', open ? 'Close product search' : 'Open product search');

      if (open && searchInput) {
        window.requestAnimationFrame(function () { searchInput.focus(); });
      }
    };

    mobileSearchToggle.addEventListener('click', function () {
      setSearchOpen(!nav.classList.contains('is-search-open'));
    });

    document.addEventListener('click', function (event) {
      if (!nav.classList.contains('is-search-open')) return;
      if (searchBox.contains(event.target) || mobileSearchToggle.contains(event.target)) return;

      setSearchOpen(false);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && nav.classList.contains('is-search-open')) {
        setSearchOpen(false);
        mobileSearchToggle.focus();
      }
    });
  }

  /* ---- mobile menu ---- */
  var burger = document.getElementById('burger');
  var menu = document.getElementById('menu');
  if (burger && menu) {
    /* The nav carries the state as well as the menu itself: the search box
       lives outside the menu in the markup but is shown alongside it at
       this width, and a class on their shared parent is what lets one rule
       reach both. */
    var setOpen = function (open) {
      if (open && setSearchOpen) setSearchOpen(false);
      menu.classList.toggle('is-open', open);
      if (nav) nav.classList.toggle('is-menu-open', open);
      burger.setAttribute('aria-expanded', open);
    };

    burger.addEventListener('click', function () {
      setOpen(!menu.classList.contains('is-open'));
    });

    menu.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') setOpen(false);
    });
  }

  /* ---- countdown ----
     The manager's coupon end time is rendered onto the side panel offer.
     Once it passes, the now-invalid promotion disappears immediately. */
  var promotion = document.getElementById('promotion');
  var clock = document.getElementById('clock');
  var saleEnd = promotion ? Date.parse(promotion.dataset.promotionEndsAt || '') : NaN;
  var timer;
  function tick(){
    if(!promotion || !clock || Number.isNaN(saleEnd)) return;
    var left = saleEnd - Date.now();
    if(left <= 0){
      promotion.hidden = true;
      clearInterval(timer);
      return;
    }
    var s = Math.floor(left/1000);
    var pad = function(n){ return String(n).padStart(2,'0'); };
    clock.querySelector('[data-unit="h"]').textContent = pad(Math.floor(s/3600));
    clock.querySelector('[data-unit="m"]').textContent = pad(Math.floor(s%3600/60));
    clock.querySelector('[data-unit="s"]').textContent = pad(s%60);
  }
  if(clock && !Number.isNaN(saleEnd)){ tick(); timer = setInterval(tick, 1000); }

})();
