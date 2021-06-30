<?php
/**
 * @category Learning_RestrictPaymentMethod 
 * @package Learning_RestrictPaymentMethod
 * To check and restrict Check/Money Order Payment Method for Not Allowed Customers
 */
namespace Learning\RestrictPaymentMethod\Plugin\Model\Method;

class Available
{
    const CUST_GROUP_AVAILABLE = 'payment/checkmo/customer_groups';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
    }

    /**
     * 
     * afterGetAvailableMethods
     *
     * @param mixed $subject
     * @param mixed $result
     * 
     * @return array
     */
    public function afterGetAvailableMethods($subject, $result)
    {
        foreach ($result as $key=>$_result) {
            if ($_result->getCode() == "checkmo") {
                $customerGroupId = 0;
                // Update customerGroupId for logged in user
                if ($this->_customerSession->isLoggedIn()) {
                    $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
                }
                // Restrciting Payemnt Method Logic
                if (!in_array($customerGroupId, $this->getCustomerGroupsAvailable())) {
                    $isAllowed =  false;
                    if (!$isAllowed) {
                        unset($result[$key]);
                    }
                }
            }
        }
        return $result;
    }

    /**
    * getCustomerGroupsAvailable
    *
    * @return array
    */
    public function getCustomerGroupsAvailable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return explode(",", $this->scopeConfig->getValue(self::CUST_GROUP_AVAILABLE, $storeScope));
    }
}
