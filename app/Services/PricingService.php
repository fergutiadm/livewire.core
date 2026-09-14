<?php

namespace App\Services;

use App\Models\Gestor;
use App\Models\GestorPriceOverride;
use App\Models\Product;

class PricingService {
    public function priceFor(?Gestor $gestor, Product $product, ?float $upliftPctFromUrl): float {
        // Priority: explicit override > base * (1 + uplift/100) > base
        if ($gestor) {
            $override = GestorPriceOverride::where('gestor_id', $gestor->id)
                ->where('product_id', $product->id)->first();
            if ($override && $override->price_override !== null) {
                return (float) $override->price_override;
            }
            $uplift = $upliftPctFromUrl ?? $gestor->uplift_pct_default ?? 0;
            return round($product->base_price * (1 + ($uplift/100)), 2);
        }
        return (float) $product->base_price;
    }
}
