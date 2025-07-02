<?php
/**
 * Copyright © Ronangr1, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Ronangr1\Barbagento\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Ronangr1\Barbagento\Model\Config;

class Init implements ArgumentInterface
{
    public function __construct(
        private readonly Config $config
    )
    {
    }

    public function getSettings(): array
    {
        return $this->config->getSettings();
    }
}
