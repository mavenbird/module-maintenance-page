<?php
namespace Mavenbird\MaintenancePage\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{
    private PageFactory $pageFactory;

    public function __construct(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setStatusHeader(503, '1.1', 'Service Unavailable');
        $page->addHandle('mavenbird_maintenance_index_index');
        return $page;
    }
}