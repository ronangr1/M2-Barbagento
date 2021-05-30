<?php
/**
 * Copyright © Ronan Guérin. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ronangr1\Barbagento\Observer;

use Magento\Framework\Event\ObserverInterface;

class LayoutSchemaAttributeObserver implements ObserverInterface
{
    private \Magento\Framework\App\Request\Http $request;

    /**
     * LayoutSchemaAttributeObserver constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
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
