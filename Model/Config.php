<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Stores > Configuration > Zero > Reviews.
 */
class Config
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function photosEnabled(): bool
    {
        return $this->flag('photos/enabled');
    }

    public function verifiedEnabled(): bool
    {
        return $this->flag('verified/enabled');
    }

    public function googleFeedEnabled(): bool
    {
        return $this->flag('google_feed/enabled');
    }

    private function flag(string $path): bool
    {
        return $this->scopeConfig->isSetFlag('zero_reviews/' . $path, ScopeInterface::SCOPE_STORE);
    }
}
