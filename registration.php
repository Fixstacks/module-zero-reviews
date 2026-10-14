<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 *
 * Zero_Reviews: more from Magento's product reviews.
 * - Photos with a review (up to 5): re-encoded on upload (no EXIF, no GPS, no
 *   files posing as images), shown under the review once it is approved.
 * - "Verified purchase": the review's author is a customer who ordered the
 *   product.
 * - Google's product reviews feed ({store URL}/reviewfeed/google) for star
 *   ratings in Shopping ads.
 * Stores > Configuration > Zero > Reviews.
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Zero_Reviews', __DIR__);
