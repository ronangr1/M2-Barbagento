<?php
/**
 * Copyright © Ronangr1. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Ronangr1\Barbagento\Block\Html;

use Magento\Catalog\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class Breadcrumbs extends \Magento\Catalog\Block\Breadcrumbs
{
    protected array $_crumbs = [];
    protected array $_properties = ['label', 'title', 'link', 'first', 'last', 'readonly'];

    public function __construct(
        private readonly Registry $registry,
        Context $context,
        Data $catalogData, array $data = []
    )
    {
        parent::__construct($context, $catalogData, $data);
    }

    public function addCrumb($crumbName, $crumbInfo): static
    {
        foreach ($this->_properties as $key) {
            if (!isset($crumbInfo[$key])) {
                $crumbInfo[$key] = null;
            }
        }

        if (!isset($this->_crumbs[$crumbName]) || !$this->_crumbs[$crumbName]['readonly']) {
            $this->_crumbs[$crumbName] = $crumbInfo;
        }

        return $this;
    }

    protected function _toHtml()
    {
        $this->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->getBaseUrl()
            ]
        );

        /** @var \Magento\Catalog\Model\Product $currentProduct */
        $currentProduct = $this->registry->registry('current_product');

        $this->addCrumb(
            $currentProduct->getName(),
            [
                'label' => $currentProduct->getName(),
                'title' => "",
                'link' => ""
            ]
        );

        reset($this->_crumbs);
        $this->_crumbs[key($this->_crumbs)]['first'] = true;
        end($this->_crumbs);
        $this->_crumbs[key($this->_crumbs)]['last'] = true;

        $this->assign('crumbs', $this->_crumbs);

        return parent::_toHtml();
    }
}
