<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zero\Reviews\Model\Config;
use Zero\Reviews\Model\Photos;
use Zero\Reviews\Model\Verified;

/**
 * Photos and "Verified purchase" for the review list (the product page's
 * Reviews section, review/product/list), loaded once for the page's reviews.
 */
class Extras implements ArgumentInterface
{
    /** @var array<int, array<int, array{url: string, thumb: string, width: int, height: int}>> */
    private array $photos = [];
    /** @var array<int, true> */
    private array $verified = [];

    public function __construct(
        private readonly Config $config,
        private readonly Photos $photoStore,
        private readonly Verified $verifiedStore
    ) {
    }

    /**
     * @param iterable<\Magento\Review\Model\Review> $reviews
     */
    public function load(iterable $reviews): void
    {
        $ids = [];
        foreach ($reviews as $review) {
            $ids[] = (int) $review->getId();
        }
        $this->photos = $this->config->photosEnabled() ? $this->photoStore->forReviews($ids) : [];
        $this->verified = $this->config->verifiedEnabled() ? $this->verifiedStore->among($ids) : [];
    }

    /**
     * @return array<int, array{url: string, thumb: string, width: int, height: int}>
     */
    public function photos(int $reviewId): array
    {
        return $this->photos[$reviewId] ?? [];
    }

    public function isVerified(int $reviewId): bool
    {
        return isset($this->verified[$reviewId]);
    }

    public function photosEnabled(): bool
    {
        return $this->config->photosEnabled();
    }

    public function maxPhotos(): int
    {
        return Photos::MAX_PHOTOS;
    }

    public function maxMegabytes(): float
    {
        return round($this->photoStore->maxBytes() / 1024 / 1024, 1);
    }
}
