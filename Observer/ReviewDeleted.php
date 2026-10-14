<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zero\Reviews\Model\Photos;

/**
 * A review deleted: its photo files too (the rows go by foreign key).
 */
class ReviewDeleted implements ObserverInterface
{
    public function __construct(
        private readonly Photos $photos
    ) {
    }

    public function execute(Observer $observer): void
    {
        $review = $observer->getEvent()->getDataObject();
        if ($review && $review->getId()) {
            $this->photos->deleteFiles((int) $review->getId());
        }
    }
}
