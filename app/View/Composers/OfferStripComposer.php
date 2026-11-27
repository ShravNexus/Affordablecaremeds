<?php

namespace App\View\Composers;

use App\Services\Commerce\CouponService;
use Illuminate\Contracts\View\View;

/**
 * The Christmas offer strip at the top of every storefront page advertises
 * the live featured coupon, so it is fed here rather than by every action.
 */
class OfferStripComposer
{
    public function __construct(private readonly CouponService $coupons) {}

    public function compose(View $view): void
    {
        $view->with('stripOffer', $this->coupons->featuredOffer());
    }
}
