<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Reviews\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Review\Controller\Product\Post;
use Zero\Reviews\Model\Photos;

/**
 * A review posted with more photos than PHP takes in one request
 * (post_max_size) arrives with no fields at all, and the stock controller
 * would say the nickname, summary and review are missing. Say what happened
 * instead, and go back to the product.
 */
class OversizedPost
{
    public function __construct(
        private readonly Http $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectInterface $redirect,
        private readonly Photos $photos
    ) {
    }

    public function aroundExecute(Post $subject, callable $proceed): mixed
    {
        if (!$this->request->isPost() || $this->request->getPostValue()
            || (int) $this->request->getServer('CONTENT_LENGTH') <= 0
        ) {
            return $proceed();
        }
        $this->messageManager->addErrorMessage(__(
            'The photos are too large together (at most %1 MB per review). Choose fewer or smaller ones.',
            $this->photos->requestLimitMegabytes()
        ));

        return $this->redirectFactory->create()->setUrl($this->redirect->getRefererUrl());
    }
}
