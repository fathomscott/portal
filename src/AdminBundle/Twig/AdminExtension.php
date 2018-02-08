<?php
namespace AdminBundle\Twig;

use BackendBundle\Entity\Subscription;
use BackendBundle\Manager\SubscriptionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class SortClassExtension
 * @package AdminBundle\Twig
 */
class AdminExtension extends \Twig_Extension
{
    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var SubscriptionManager
     */
    private $subscriptionManager;

    /**
     * AdminExtension constructor.
     * @param RequestStack        $requestStack
     * @param SubscriptionManager $subscriptionManager
     */
    public function __construct(RequestStack $requestStack, SubscriptionManager $subscriptionManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sort_class', array($this, 'sortClassFilter')),
            new \Twig_SimpleFilter('subscription_amount', array($this, 'subscriptionAmountFilter')),
        );
    }

    /**
     * Check if list is sorted by give condition and return class to display right icon.
     * @param string $condition
     * @return string
     */
    public function sortClassFilter($condition)
    {
        if (!$this->request instanceof Request) {
            return '';
        }

        if ($this->request->get('sort') === $condition) {
            return $this->request->get('direction') === 'desc' ? 'sorting_desc' : 'sorting_asc';
        }

        return 'sorting';
    }

    /**
     * @param Subscription $subscription
     * @return float
     */
    public function subscriptionAmountFilter(Subscription $subscription)
    {
        $amount = $subscription->getPlan()->getMonthlyPrice();
        $referralDiscount = $this->subscriptionManager->countReferralDiscount($subscription);
        if ($referralDiscount >= $amount) {
            $amount = 0;
        } else {
            $amount -= $referralDiscount;
        }

        return $amount;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sort_class_extension';
    }
}
