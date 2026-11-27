@extends('components.baselayout')

@section('title', 'All products - NoRx Dose')

@push('styles')
    <link rel="stylesheet" href="@versionedAsset('css/pages/all-products.css')">
@endpush

@section('sprite')
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
      <symbol id="jar" viewBox="0 0 120 150">
        <rect x="44" y="6" width="32" height="16" rx="4" fill="#C9A227"/>
        <rect x="30" y="20" width="60" height="16" rx="5" fill="#B8912012"/>
        <rect x="26" y="30" width="68" height="106" rx="12" fill="#fff" stroke="#E7E2D6" stroke-width="2"/>
        <rect x="34" y="52" width="52" height="44" rx="6" fill="#F7EFD8"/>
        <rect x="42" y="64" width="36" height="4" rx="2" fill="#C9A227"/>
        <rect x="46" y="74" width="28" height="3" rx="1.5" fill="#D9CFAF"/>
        <rect x="50" y="82" width="20" height="3" rx="1.5" fill="#D9CFAF"/>
        <rect x="34" y="106" width="52" height="3" rx="1.5" fill="#EFEBE0"/>
        <rect x="34" y="114" width="38" height="3" rx="1.5" fill="#EFEBE0"/>
      </symbol>
      <symbol id="leaf" viewBox="0 0 200 200">
        <path d="M100 8c52 26 78 62 78 96 0 48-36 88-78 88S22 152 22 104C22 70 48 34 100 8zm0 30c-32 20-52 44-52 66 0 34 24 60 52 60s52-26 52-60c0-22-20-46-52-66z" fill="currentColor"/>
        <path d="M100 40v144" stroke="currentColor" stroke-width="7"/>
      </symbol>
    </svg>

    <!-- ==================== TICKER ==================== -->
@endsection

@section('page-head')
<!-- ==================== PAGE HEAD ==================== -->
<section class="head">
  <svg class="leaf leaf--a" viewBox="0 0 200 200" aria-hidden="true"><use href="#leaf"/></svg>
  <svg class="leaf leaf--b" viewBox="0 0 200 200" aria-hidden="true"><use href="#leaf"/></svg>

  <div class="shell head__in">
    <span class="head__count" id="catCount">{{ $products->count() }} {{ Str::plural('product', $products->count()) }}</span>
    <h1>{!! $content->html('hero.title') !!}</h1>
    <p>Everything we make, in one place. Batch tested, fragrance free, and shipped within 24 hours.</p>
  </div>
</section>
@endsection

@section('content')
<!-- ==================== CATALOG ==================== -->
<main class="catalog">
  <div class="shell">
    {{-- Rendered server-side; all-products.js only reveals and counts what is already here. --}}
    <div class="grid" id="grid">
      @foreach ($products as $product)
        @include('partials.product-card-catalog', ['product' => $product, 'index' => $loop->index])
      @endforeach

      {{-- Shown when the catalogue is empty. --}}
      <div class="empty" id="empty" @if ($products->isNotEmpty()) style="display:none" @endif>
        <h3>Nothing in the shop yet</h3>
        <p>New products appear here as soon as they go live.</p>
      </div>
    </div>
  </div>
</main>

<!-- ==================== FOOTER ==================== -->
@endsection

@push('scripts')
    <script src="@versionedAsset('js/pages/all-products.js')"></script>
@endpush
