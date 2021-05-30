<?php
/**
 * Copyright © Ronan Guérin. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ronangr1\Barbagento\Observer;

use Magento\Framework\Event\ObserverInterface;

class LayoutSchemaAttributeObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        $elementName = $observer->getEvent()->getElementName();
        if ($layout->isContainer($elementName) && $elementName === 'main.content') {
            $output = $observer->getEvent()->getTransport()->getOutput();
            $output = str_replace(
                '<main id="maincontent" class="page-main">',
                '<main id="maincontent" class="page-main" data-barba="container" data-barba-namespace="' . $elementName . '">',
                $output
            );
            $observer->getEvent()->getTransport()->setOutput($output);
        }
    }
}
