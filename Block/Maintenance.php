<?php
namespace Mavenbird\MaintenancePage\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mavenbird\MaintenancePage\Helper\Data as MaintenanceHelper;

class Maintenance extends Template
{
    private MaintenanceHelper $helper;

    public function __construct(Context $context, MaintenanceHelper $helper, array $data = [])
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getPageTitle(): string        { return $this->helper->getMaintenanceTitle(); }
    public function getPageDescription(): string  { return $this->helper->getMaintenanceDescription(); }
    public function getTextColor(): string        { return $this->helper->getMaintenanceTextColor(); }
    public function getLogoUrl(): string          { return $this->helper->getMaintenanceLogo(); }
    public function getBackgroundType(): string   { return $this->helper->getMaintenanceBgType(); }
    public function getBackgroundColor(): string  { return $this->helper->getMaintenanceBgColor(); }
    public function getBackgroundVideo(): string  { return $this->helper->getMaintenanceBgVideo(); }
    public function isProgressBarEnabled(): bool  { return $this->helper->isMaintenanceProgressEnabled(); }
    public function getProgressValue(): int       { return $this->helper->getMaintenanceProgressValue(); }
    public function getProgressColor(): string    { return $this->helper->getMaintenanceProgressColor(); }
    public function isClockEnabled(): bool        { return $this->helper->isClockEnabled(); }
    public function getClockStyle(): string       { return $this->helper->getClockStyle(); }
    public function getClockBgColor(): string     { return $this->helper->getClockBgColor(); }
    public function getClockNumberColor(): string { return $this->helper->getClockNumberColor(); }
    public function getClockLabelColor(): string  { return $this->helper->getClockLabelColor(); }
    public function getEndTimestamp(): int        { return $this->helper->getEndDateTimeTimestamp(); }
    public function isSubscribeEnabled(): bool    { return $this->helper->isSubscribeEnabled(); }
    public function getSubscriptionType(): string { return $this->helper->getSubscriptionType(); }
    public function getSubscribeDescription(): string    { return $this->helper->getSubscribeDescription(); }
    public function getSubscribeDescColor(): string      { return $this->helper->getSubscribeDescriptionColor(); }
    public function getSubscribeBtnLabel(): string       { return $this->helper->getSubscribeButtonLabel() ?: 'Subscribe'; }
    public function getSubscribeBtnTextColor(): string   { return $this->helper->getSubscribeButtonTextColor(); }
    public function getSubscribeBtnBgColor(): string     { return $this->helper->getSubscribeButtonBgColor(); }
    public function getSubscribeUrl(): string            { return $this->getUrl('maintenance/index/subscribe'); }
    public function isSocialEnabled(): bool       { return $this->helper->isSocialEnabled(); }
    public function getSocialLabel(): string      { return $this->helper->getSocialLabel(); }
    public function getSocialLabelColor(): string { return $this->helper->getSocialLabelColor(); }
    public function getSocialLinks(): array       { return $this->helper->getSocialLinks(); }

    public function getBackgroundImage(): string
    {
        $img = $this->helper->getMaintenanceBgImage();
        if ($img) return $this->_urlBuilder->getBaseUrl(['_type' => 'media']) . 'mavenbird/maintenance/' . $img;
        return '';
    }
}