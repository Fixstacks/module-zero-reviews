<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;

/**
 * "Verified purchase" (zero_review_verified): the review's author is a
 * customer with an order that has the product and wasn't canceled.
 */
class Verified
{
    private const TABLE = 'zero_review_verified';

    public function __construct(
        private readonly ResourceConnection $resource
    ) {
    }

    /**
     * The customer's latest order with the product (a configurable product's
     * order line is the product itself), or null.
     */
    public function orderWith(int $customerId, int $productId): ?int
    {
        $db = $this->resource->getConnection();
        $id = $db->fetchOne($db->select()
            ->from(['o' => $this->resource->getTableName('sales_order')], ['entity_id'])
            ->join(['i' => $this->resource->getTableName('sales_order_item')], 'i.order_id = o.entity_id', [])
            ->where('o.customer_id = ?', $customerId)
            ->where('i.product_id = ?', $productId)
            ->where('o.state <> ?', Order::STATE_CANCELED)
            ->order('o.entity_id DESC')
            ->limit(1));

        return $id === false ? null : (int) $id;
    }

    public function mark(int $reviewId, int $orderId): void
    {
        $this->resource->getConnection()->insertOnDuplicate(
            $this->resource->getTableName(self::TABLE),
            ['review_id' => $reviewId, 'order_id' => $orderId],
            ['order_id']
        );
    }

    /**
     * @param int[] $reviewIds
     * @return array<int, true> the verified ones, by id
     */
    public function among(array $reviewIds): array
    {
        if (!$reviewIds) {
            return [];
        }
        $db = $this->resource->getConnection();
        $ids = $db->fetchCol($db->select()
            ->from($this->resource->getTableName(self::TABLE), ['review_id'])
            ->where('review_id IN (?)', array_map('intval', $reviewIds)));

        return array_fill_keys(array_map('intval', $ids), true);
    }
}
