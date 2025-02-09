<?php

namespace ShikhBas\BasLaravelSdk\Services;

class PaymentService
{
    protected $basService;
    protected $config;

    public function __construct(BasService $basService, array $config)
    {
        $this->basService = $basService;
        $this->config = $config;
    }

    public function initiateTransactionOrder(array $payload)
    {
        $endpoint = '/api/v1/merchant/secure/transaction/initiate';
        return $this->basService->post($endpoint, $payload);
    }

    public function checkTransactionStatus(array $payload)
    {
        $endpoint = '/api/v1/merchant/secure/transaction/status';
        return $this->basService->post($endpoint, $payload);
    }

    public function refundPayment(array $payload)
    {
        $endpoint = '/api/v1/merchant/refund-payment/request';
        return $this->basService->post($endpoint, $payload);
    }

    public function generateBasPaymentJS(array $payload): string
    {
        $payloadJson = json_encode($payload);
        return <<<JS
        function basPayment(){JSBridge.call('basPayment', $payloadJson).then(function(result) {
                console.log('basPayment result:', result);
                // يمكنك إضافة المزيد من كود JavaScript هنا على أسطر جديدة
                // واستخدام المسافات البادئة لتنظيم الكود JavaScript
                if (result && result.status === 1) {
                    // ... إجراءات النجاح ...
                } else {
                    // ... إجراءات الفشل ...
                }
            });
                }
        JS; // علامة الإغلاق JS; تبدأ من بداية السطر بدون مسافات بادئة
    }

}