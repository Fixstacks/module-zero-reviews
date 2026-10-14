<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Observer;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zero\Reviews\Model\GoogleFeed;

/**
 * A review saved (approved, edited) or deleted: the cached Google review
 * feed is built again on its next request.
 */
class CleanFeed implements ObserverInterface
{
    public function __construct(private readonly CacheInterface $cache)
    {
    }

    public function execute(Observer $observer): void
    {
        $this->cache->clean([GoogleFeed::CACHE_TAG]);
    }
}
