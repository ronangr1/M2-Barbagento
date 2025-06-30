<?php

namespace Ronangr1\Barbagento\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    )
    {
    }

    public function isActive(): bool
    {
        return $this->scopeConfig->isSetFlag('barbagento/general/is_active');
    }
}
