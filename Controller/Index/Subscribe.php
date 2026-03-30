<?php
namespace Mavenbird\MaintenancePage\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Subscribe extends Action implements HttpPostActionInterface
{
    protected $jsonFactory;
    protected $subscriberFactory;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        SubscriberFactory $subscriberFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        $isEnabled = $this->scopeConfig->getValue(
            'mavenbird_maintenancepage/subscribe/enabled',
            ScopeInterface::SCOPE_STORE
        );

        if (!$isEnabled) {
            return $result->setData([
                'success' => false,
                'message' => __('Subscription is currently disabled.')
            ]);
        }

        $request = $this->getRequest(); // use $this->getRequest() from Action

        if (!$request->isXmlHttpRequest()) {
            return $result->setData([
                'success' => false,
                'message' => __('Invalid request type.')
            ]);
        }

        $email = trim((string)$request->getParam('email'));

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $result->setData([
                'success' => false,
                'message' => __('Please enter a valid email address.')
            ]);
        }

        try {
            $subscriberModel = $this->subscriberFactory->create();
            $subscriber = $subscriberModel->loadByEmail($email);

            if ($subscriber->getId()) {
                $subscriber->subscribe($email);
                try { $subscriber->sendConfirmationRequestEmail(); } catch (\Exception $e) {}
                return $result->setData([
                    'success' => true,
                    'message' => __('Already subscribed. Please check your email.')
                ]);
            }

            $subscriberModel->subscribe($email);
            try { $subscriberModel->sendConfirmationRequestEmail(); } catch (\Exception $e) {}

            return $result->setData([
                'success' => true,
                'message' => __('Thank you! Please check your email.')
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => __('Something went wrong. Please try again later.')
            ]);
        }
    }
}