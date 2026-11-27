/* Page script: all-products - runs after common.js

   The catalogue itself is rendered server-side by Blade, so this file no longer
   builds any markup. It filters the cards that are already on the page, which
   keeps the search instant and means the grid is present for search engines and
   for anyone with JavaScript switched off. */
(function(){
  'use strict';
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var grid  = document.getElementById('grid');
  if(!grid) return;

  var cards = Array.prototype.slice.call(grid.querySelectorAll('.card'));
  var empty = document.getElementById('empty');
  var count = document.getElementById('catCount');
  var state = {q:''};

  /* Searchable text is read once per card rather than on every keystroke. */
  cards.forEach(function(card){
    var title = card.querySelector('.card__title');
    card.dataset.search = ((title ? title.textContent : '') + ' ' +
                           (card.dataset.cat || '')).toLowerCase();
  });

  var io = ('IntersectionObserver' in window) ? new IntersectionObserver(function(en){
    en.forEach(function(e){
      if(!e.isIntersecting) return;
      e.target.classList.add('is-in');
      io.unobserve(e.target);
    });
  }, {threshold:.1, rootMargin:'0px 0px -30px 0px'}) : null;

  function reveal(card){
    if(io && !reduce) io.observe(card);
    else card.classList.add('is-in');
  }
  cards.forEach(reveal);

  function render(){
    var t = state.q.toLowerCase();
    var shown = 0;

    cards.forEach(function(card){
      var match = !t || card.dataset.search.indexOf(t) > -1;

      /* display rather than the hidden attribute: .card is a flex container,
         and its display rule would win over [hidden]. */
      card.style.display = match ? '' : 'none';

      if(match){
        shown++;
        /* A card revealed while hidden never fired its observer, so anything
           filtered back into view is revealed now. */
        if(!card.classList.contains('is-in')) reveal(card);
      }
    });

    if(count) count.textContent = shown + (shown === 1 ? ' product' : ' products');
    if(empty) empty.style.display = shown ? 'none' : '';
  }
  render();

  /* ---- search ---- */
  var q = document.getElementById('q');
  if(q) q.addEventListener('input', function(){ state.q = q.value.trim(); render(); });

  /* ---- wishlist (delegated)
     "Select options" is a link to the product page, so it needs no handler. ---- */
  grid.addEventListener('click', function(e){
    var w = e.target.closest('.wish');
    if(!w) return;
    e.preventDefault();
    var on = w.classList.toggle('is-on');
    w.setAttribute('aria-pressed', on);
    w.querySelector('svg').setAttribute('fill', on ? 'currentColor' : 'none');
  });

  /* ---- back to top (sticky nav itself lives in common.js) ---- */
  var top = document.getElementById('top');
  if(top){
    var onScroll = function(){ top.classList.toggle('is-on', window.scrollY > 700); };
    window.addEventListener('scroll', onScroll, {passive:true});
    onScroll();
    top.addEventListener('click', function(){
      window.scrollTo({top:0, behavior: reduce ? 'auto' : 'smooth'});
    });
  }
})();
