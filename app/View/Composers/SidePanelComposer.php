<?php

namespace App\View\Composers;

use App\Services\Catalogue\StorefrontCatalogue;
use App\Services\Commerce\CouponService;
use Illuminate\Contracts\View\View;

/**
 * The storefront side panel - the featured offer above the category list -
 * appears on every page but the home page, so it is fed here once rather
 * than by every controller action.
 */
class SidePanelComposer
{
    public function __construct(
        private readonly StorefrontCatalogue $catalogue,
        private readonly CouponService $coupons,
    ) {}

    public function compose(View $view): void
    {
        $slug = request()->routeIs('product-category') ? request()->route('slug') : null;

        $view->with([
            'sidePromotion' => $this->coupons->featuredOffer(),
            'sideCategories' => $this->catalogue->categories(),
            'sideActiveSlug' => is_string($slug) ? $slug : null,
        ]);
    }
}
