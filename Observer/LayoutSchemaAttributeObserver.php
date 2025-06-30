<?php
/**
 * Copyright © Ronangr1. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ronangr1\Barbagento\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutSchemaAttributeObserver implements ObserverInterface
{
    public function __construct(
        private readonly RequestInterface $request
    )
    {
    }

    public function execute(Observer $observer): void
    {
        if ('page.wrapper' !== $observer->getEvent()->getElementName()) {
            return;
        }

        $transport = $observer->getEvent()->getTransport();
        $output = $transport->getOutput();

        if (!is_string($output) || '' === $output) {
            return;
        }

        $pattern = '/(<main\s+id="maincontent")/i';

        if (!preg_match($pattern, $output)) {
            return;
        }

        $namespace = htmlspecialchars(
            $this->request->getRouteName() ?? 'default',
            ENT_QUOTES,
            'UTF-8'
        );

        $attributesToAdd = sprintf(
            'data-barba="container" data-barba-namespace="%s"',
            $namespace
        );

        $replacement = sprintf('$1 %s', $attributesToAdd);

        $newOutput = preg_replace($pattern, $replacement, $output, 1);

        if (null !== $newOutput) {
            $transport->setOutput($newOutput);
        }
    }
}
