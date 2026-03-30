<?php
namespace Mavenbird\MaintenancePage\Cron;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Psr\Log\LoggerInterface;
use Mavenbird\MaintenancePage\Helper\Data as MaintenanceHelper;

class AutoDisable
{
    protected MaintenanceHelper $helper;
    protected WriterInterface $configWriter;
    protected TypeListInterface $cacheTypeList;
    protected LoggerInterface $logger;

    public function __construct(
        MaintenanceHelper $helper,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        LoggerInterface $logger
    ) {
        $this->helper        = $helper;
        $this->configWriter  = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->logger        = $logger;
    }

    public function execute(): void
    {
        // ----------------------------------------------------------------
        // STEP 1: Module must be enabled
        // ----------------------------------------------------------------
        if (!$this->helper->isEnabled()) {
            $this->logger->debug('[MaintenancePage Cron] Module is disabled — skipping.');
            return;
        }

        // ----------------------------------------------------------------
        // STEP 2: Auto switch setting must be ON
        // ----------------------------------------------------------------
        if (!$this->helper->isAutoSwitch()) {
            $this->logger->debug('[MaintenancePage Cron] Auto-switch is OFF — skipping.');
            return;
        }

        // ----------------------------------------------------------------
        // STEP 3: Check if the end date/time has passed
        // ----------------------------------------------------------------
        $endTimestamp = $this->helper->getEndDateTimeTimestamp();

        $this->logger->debug(sprintf(
            '[MaintenancePage Cron] End timestamp: %d | Current time: %d',
            $endTimestamp,
            time()
        ));

        if ($endTimestamp <= 0) {
            $this->logger->debug('[MaintenancePage Cron] No end date set — skipping.');
            return;
        }

        if (!$this->helper->isEndDateTimePassed()) {
            $this->logger->debug('[MaintenancePage Cron] Timer has NOT expired yet — skipping.');
            return;
        }

        // ----------------------------------------------------------------
        // STEP 4: Time has passed → disable the maintenance/coming-soon page
        // ----------------------------------------------------------------
        $this->logger->info('[MaintenancePage Cron] Timer expired → Disabling maintenance page.');

        $this->configWriter->save(
            MaintenanceHelper::XML_GENERAL_ENABLED,
            0
        );

        // FIX: Clean BOTH config and full_page cache so the change reflects
        // immediately on the storefront without a manual cache flush.
        $this->cacheTypeList->cleanType('config');
        $this->cacheTypeList->cleanType('full_page');

        $this->logger->info('[MaintenancePage Cron] Maintenance page disabled successfully.');
    }
}