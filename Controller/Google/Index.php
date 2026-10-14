<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Controller\Google;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Zero\Reviews\Model\Config;
use Zero\Reviews\Model\GoogleFeed;

/**
 * {store URL}/reviewfeed/google: Google's
 * product reviews feed, 404 when it is off.
 */
class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly GoogleFeed $feed,
        private readonly RawFactory $rawFactory,
        private readonly ForwardFactory $forwardFactory
    ) {
    }

    public function execute(): ResultInterface
    {
        if (!$this->config->googleFeedEnabled()) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $result = $this->rawFactory->create();
        $result->setHeader('Content-Type', 'application/xml; charset=UTF-8', true);
        $result->setHeader('Cache-Control', 'public, max-age=3600', true);
        $result->setHeader('X-Robots-Tag', 'noindex', true);
        $result->setContents($this->feed->get());

        return $result;
    }
}
