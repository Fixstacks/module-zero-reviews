<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Review\Model\Review;
use Psr\Log\LoggerInterface;
use Zero\Reviews\Model\Config;
use Zero\Reviews\Model\Photos;
use Zero\Reviews\Model\Verified;

/**
 * A review just written on the storefront (review/product/post): its photos,
 * and whether its author bought the product.
 */
class ReviewSubmitted implements ObserverInterface
{
    public function __construct(
        private readonly Http $request,
        private readonly Config $config,
        private readonly Photos $photos,
        private readonly Verified $verified,
        private readonly ManagerInterface $messageManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(Observer $observer): void
    {
        $review = $observer->getEvent()->getDataObject();
        if (!$review instanceof Review || $this->request->getFullActionName() !== 'review_product_post'
            || (int) $review->getEntityId() !== (int) $review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE)
        ) {
            return;
        }
        $reviewId = (int) $review->getId();
        try {
            if ($this->config->verifiedEnabled() && $review->getCustomerId()) {
                $orderId = $this->verified->orderWith((int) $review->getCustomerId(), (int) $review->getEntityPkValue());
                if ($orderId !== null) {
                    $this->verified->mark($reviewId, $orderId);
                }
            }
            if ($this->config->photosEnabled()) {
                $uploads = $this->request->getFiles('photos');
                $uploads = is_array($uploads) ? (isset($uploads['tmp_name']) ? [$uploads] : array_values($uploads)) : [];
                foreach ($this->photos->add($reviewId, $uploads) as $error) {
                    $this->messageManager->addNoticeMessage(__('A photo was left out: %1', $error));
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('Zero_Reviews: review ' . $reviewId . ': ' . $e->getMessage());
        }
    }
}
