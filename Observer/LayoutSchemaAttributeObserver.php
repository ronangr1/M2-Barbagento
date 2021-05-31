<?php
/**
 * Copyright © Ronan Guérin. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ronangr1\Barbagento\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;

class LayoutSchemaAttributeObserver implements ObserverInterface
{
    private Http $request;

    /**
     * LayoutSchemaAttributeObserver constructor.
     * @param Http $request
     */
    public function __construct(
        Http $request
    )
    {
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        $elementName = $observer->getEvent()->getElementName();
        if ($layout->isContainer($elementName) && $elementName === 'main.content') {
            $output = $observer->getEvent()->getTransport()->getOutput();
            $output = str_replace(
                '<main id="maincontent" class="page-main">',
                '<main id="maincontent" class="page-main" data-barba="container" data-barba-namespace="' . $this->request->getRouteName() . '">',
                $output
            );
            $observer->getEvent()->getTransport()->setOutput($output);
        }
    }
}
