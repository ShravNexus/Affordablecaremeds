{{-- The storefront side panel: the featured offer above every live category.
     Included by the layout on every page except home; fed by SidePanelComposer. --}}
<aside class="side-panel" aria-label="Offers and categories">
  @if ($sidePromotion !== null)
    <section
      class="side-panel__promo"
      id="promotion"
      @if ($sidePromotion['ends_at_iso'] !== null) data-promotion-ends-at="{{ $sidePromotion['ends_at_iso'] }}" @endif
    >
      <p class="side-panel__offer">
        <span class="side-panel__spark" aria-hidden="true">✦</span>
        {{ $sidePromotion['ends_at_iso'] !== null ? 'Offer ends in —' : 'Seasonal offer —' }}
        <strong>{{ $sidePromotion['promotion_label'] }}</strong>. Code
      </p>
      <span class="side-panel__code">{{ $sidePromotion['code'] }}</span>
      @if ($sidePromotion['ends_at_iso'] !== null)
        <div class="clock" id="clock" aria-label="Time remaining for this offer">
          <span class="clock__cell"><b data-unit="h">00</b><span>HRS</span></span>
          <span class="clock__sep">:</span>
          <span class="clock__cell"><b data-unit="m">00</b><span>MIN</span></span>
          <span class="clock__sep">:</span>
          <span class="clock__cell"><b data-unit="s">00</b><span>SEC</span></span>
        </div>
      @endif
    </section>
  @endif

  <nav class="side-panel__cats" aria-label="Shop by category">
    <h2 class="side__title">Categories</h2>
    <ul class="side__list">
      @foreach ($sideCategories as $sideCategory)
        <li>
          <a href="{{ route('product-category', ['slug' => $sideCategory->slug]) }}"
             @class(['is-on' => $sideActiveSlug === $sideCategory->slug])
             @if ($sideActiveSlug === $sideCategory->slug) aria-current="page" @endif>
            {{ $sideCategory->name }}
          </a>
        </li>
      @endforeach
    </ul>
  </nav>
</aside>
