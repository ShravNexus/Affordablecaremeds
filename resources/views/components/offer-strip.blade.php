{{-- ==================== CHRISTMAS OFFER STRIP ====================
     Sits above the sticky ticker + nav, so it scrolls away with the page.
     The offer, code and end time come from the live featured coupon
     (OfferStripComposer). A coupon with no end time gets no fake "Ends in":
     the clock then counts down to Christmas Day and says so. --}}
@php
    $stripEndsAt = $stripOffer['ends_at_iso'] ?? null;
    $stripCountdownTo = $stripEndsAt ?? now()->month(12)->day(25)->startOfDay()->toIso8601String();
    $stripCountdownLabel = $stripEndsAt !== null ? 'Ends in' : 'Christmas in';
@endphp
<div class="xmas" id="xmasStrip" role="region" aria-label="Christmas offer">
    {{-- falling snow, purely decorative --}}
    <div class="xmas__snow" aria-hidden="true">
        @foreach (range(1, 14) as $flake)
            <span style="--x:{{ ($flake * 7) % 100 }}%;--d:{{ 6 + ($flake % 5) }}s;--delay:-{{ $flake * 0.7 }}s;--s:{{ 0.5 + ($flake % 3) * 0.2 }}rem">❄</span>
        @endforeach
    </div>

    <div class="xmas__lead">
        <span class="xmas__art" aria-hidden="true">🎅</span>
        <span class="xmas__hello">Ho ho ho!</span>
    </div>

    <div class="xmas__marquee">
        <div class="xmas__track" id="xmasTrack">
            <div class="xmas__group">
                @if ($stripOffer !== null)
                    <span class="xmas__item">
                        <b>{{ $stripOffer['promotion_label'] }}</b> — use code
                        <span class="xmas__code">{{ $stripOffer['code'] }}</span>
                    </span>
                @else
                    <span class="xmas__item"><b>Christmas deals</b> across the whole shop</span>
                @endif
                <span class="xmas__script">Merry Christmas!</span>
                <span class="xmas__deco" aria-hidden="true">🎁</span>
                <span class="xmas__item">Festive picks for <b>everyone on your list</b></span>
                <span class="xmas__deco" aria-hidden="true">🎄</span>
            </div>
        </div>
    </div>

    <div class="xmas__clock" data-countdown-to="{{ $stripCountdownTo }}">
        <span class="xmas__clock-label">{{ $stripCountdownLabel }}</span>
        <span class="xmas__clock-box" aria-live="off">
            <b data-unit="d">0</b>d <b data-unit="h">00</b>h <b data-unit="m">00</b>m <b data-unit="s">00</b>s
        </span>
    </div>

    <a href="{{ route('shop') }}" class="xmas__btn">
        Shop Now
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
    </a>
</div>
