<?php
namespace AdminBundle\Menu;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuBuilder
 */
class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var User|Agent
     */
    private $user;

    /**
     * MenuBuilder constructor.
     * @param FactoryInterface              $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param RequestStack $requestStack
     * @return \Knp\Menu\ItemInterface
     */
    public function createAdminSidebarMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(['class' => 'nav']);

        $menu->addChild('navigation.dashboard', [
            'route' => 'admin_dashboard',
            'attributes' => [
                'icon' => 'fa fa-laptop',
            ],
        ])->setExtra('translation_domain', 'messages');

        if ($this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $menu->addChild('navigation.document.documents', [
                'route' => 'admin_document_index',
                'attributes' => [
                    'icon' => 'fa fa-file-text',
                ],
            ])->setExtra('translation_domain', 'messages');

            $menu->addChild('labels.payment_history', [
                'route' => 'admin_agent_transaction_index',
                'attributes' => [
                    'icon' => 'fa fa-history',
                ],
            ])->setExtra('translation_domain', 'messages');

            $menu->addChild('navigation.referral.referrals', [
                'route' => 'admin_agent_referral_index',
                'attributes' => [
                    'icon' => 'fa fa-users',
                ],
            ])->setExtra('translation_domain', 'messages');
/*
            $menu->addChild('navigation.plan.plans', [
                'route' => 'admin_agent_profile_plan',
                'attributes' => [
                    'icon' => 'fa fa-file-text',
                ],
            ])->setExtra('translation_domain', 'messages');
*/
            $menu->addChild('navigation.credit_card.edit', [
                'route' => 'admin_agent_profile_payment_method_manage',
                'attributes' => [
                    'icon' => 'fa fa-credit-card',
                ],
            ])->setExtra('translation_domain', 'messages');
        }


            $menu->addChild('navigation.team.teams', [
                'route' => 'admin_agent_team_index',
                'attributes' => [
                    'icon' => 'fa fa-user-plus',
                ],
            ])->setExtra('translation_domain', 'messages');

        if ($this->isAdminOrDD()) {
            $menu->addChild('navigation.agent.agents', [
                'route' => 'admin_agent_index',
                'attributes' => [
                    'icon' => 'fa fa-users',
                ],
            ])->setExtra('translation_domain', 'messages');
        }

        if ($this->isSuperAdminOrDD()) {
            $this->createReportsDropdown($menu);
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $this->createSettingsDropdown($menu);
        }

        if ($this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $menu->addChild('navigation.contact', [
                'route' => 'admin_agent_contact_index',
                'attributes' => [
                    'icon' => 'fa fa-address-card',
                ],
            ])->setExtra('translation_domain', 'messages');
        }

/* 
        if ($this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $this->thirdPartyLinksMenu($menu);
        }
       
        if ($this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $this->createTermsDropdown($menu);
        }
*/

        /* toggle */
        $menu->addChild('', [
            'uri' => 'javascript:;',
            'attributes' => [
                'icon' => 'fa fa-angle-double-left',
                'data-click' => 'sidebar-minify',
            ],
            'linkAttributes' => [
                'class' => 'sidebar-minify-btn',
            ],
        ])->setExtra('translation_domain', 'messages');

        return $menu;
    }

    /**
     * @param RequestStack $requestStack
     * @return \Knp\Menu\ItemInterface
     */
    public function createProfileMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(['class' => 'dropdown-menu animated fadeInLeft']);

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('arrow', [
                'divider' => true,
                'label' => '',
                'attributes' => ['class' => 'arrow'],
            ]);
        }

        if ($this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $menu->addChild('arrow', [
                'divider' => true,
                'label' => '',
                'attributes' => ['class' => 'arrow'],
            ]);

            $menu->addChild('navigation.profile.edit', [
                'route' => 'admin_agent_profile',
            ])->setExtra('translation_domain', 'messages');

            $menu->addChild('labels.change_password', [
                'route' => 'admin_agent_profile_change_password',
            ])->setExtra('translation_domain', 'messages');

        }

        $menu->addChild('devider', [
            'divider' => true,
            'label' => '',
            'attributes' => ['class' => 'divider'],
        ]);

        if ($this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $menu->addChild('navigation.back_to_admin', [
                'route' => 'admin_dashboard',
                'routeParameters' => ['_switch_user' => '_exit'],
            ]);
        } else {
            $menu->addChild('navigation.log_out', [
                'route' => 'backend_logout',
            ])->setExtra('translation_domain', 'messages');
        }

        return $menu;
    }


    private function createReportsDropdown(ItemInterface $menu)
    {
        $dropdown = $menu->addChild('navigation.reports.reports', [
            'uri' => 'javascript:;',
            'attributes' => [
                'dropdown' => true,
                'class' => 'has-sub',
                'icon' => 'fa fa-list',
            ],
            'childrenAttributes' => [
                'class' => 'sub-menu',
            ],
            'routes' => [
                ['route' => 'admin_overdue_subscription'],
                ['route' => 'admin_transaction_index'],
                ['route' => 'admin_variable_mls_dues_transaction_index'],
                ['route' => 'admin_referral_index'],
                ['route' => 'admin_team_index'],
                ['route' => 'admin_pending_document_index'],
                ['route' => 'admin_expiring_document_index'],
                ['route' => 'admin_expired_document_index'],
                ['route' => 'admin_email_index'],
            ],
        ])->setExtra('translation_domain', 'messages');

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $dropdown->addChild('navigation.subscriptions.overdue', [
                'route' => 'admin_overdue_subscription',
                'attributes' => ['child' => true],
            ])->setExtra('translation_domain', 'messages');

            $dropdown->addChild('navigation.transaction.transactions', [
                'route' => 'admin_transaction_index',
                'attributes' => ['child' => true],
            ])->setExtra('translation_domain', 'messages');
            
            $dropdown->addChild('navigation.pending_transaction.pending_transactions', [
                'route' => 'admin_variable_mls_dues_transaction_index',
                'attributes' => ['child' => true],
            ])->setExtra('translation_domain', 'messages');
        }

        $dropdown->addChild('navigation.referral.referrals', [
            'route' => 'admin_referral_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.team.teams', [
            'route' => 'admin_team_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('labels.pending_documents', [
            'route' => 'admin_pending_document_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('labels.expiring_documents', [
            'route' => 'admin_expiring_document_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('labels.expired_documents', [
            'route' => 'admin_expired_document_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $dropdown->addChild('navigation.reports.mail_history', [
                'route' => 'admin_email_index',
                'attributes' => ['child' => true],
            ])->setExtra('translation_domain', 'messages');
        }
    }

    private function createSettingsDropdown(ItemInterface $menu)
    {
        $dropdown = $menu->addChild('navigation.settings.settings', [
            'uri' => 'javascript:;',
            'attributes' => [
                'dropdown' => true,
                'class' => 'has-sub',
                'icon' => 'fa fa-cog',
            ],
            'childrenAttributes' => [
                'class' => 'sub-menu',
            ],
            'routes' => [
                ['route' => 'admin_state_index'],
                ['route' => 'admin_district_index'],
                ['route' => 'admin_plan_index'],
                ['route' => 'admin_staff_index'],
                ['route' => 'admin_license_category_index'],
                ['route' => 'admin_billing_option_index'],
                ['route' => 'admin_system_message_index'],
                ['route' => 'admin_system_default_manage'],
                ['route' => 'admin_document_option_index'],
            ],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.settings.states', [
            'route' => 'admin_state_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.district.districts', [
            'route' => 'admin_district_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.plan.plans', [
            'route' => 'admin_plan_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.settings.staff', [
            'route' => 'admin_staff_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.license_category.license_categories', [
            'route' => 'admin_license_category_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.billing_option.billing_options', [
            'route' => 'admin_billing_option_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.system_message.system_messages', [
            'route' => 'admin_system_message_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.system_default.system_defaults', [
            'route' => 'admin_system_default_manage',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.document_option.document_options', [
            'route' => 'admin_document_option_index',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');
    }

    private function thirdPartyLinksMenu(ItemInterface $menu)
    {
        $dropdown = $menu->addChild('navigation.third_party_links.third_party_links', [
            'uri' => 'javascript:;',
            'attributes' => [
                'dropdown' => true,
                'class' => 'has-sub third-party-links',
                'icon' => 'fa fa-external-link-square',
            ],
            'childrenAttributes' => [
                'class' => 'sub-menu',
            ],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.agent_crm_website_rew', [
            'uri' => 'http://www.fathomrealty.com/backend/login/',
            'attributes' => [
                'child' => true,
                'class' => 'agent-crm-website-rew',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.e_endorsements', [
            'uri' => 'https://eendorsements.com/login/',
            'attributes' => [
                'child' => true,
                'class' => 'e-endorsements',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.email_fantom_web_email', [
            'uri' => 'http://mail.fathomrealty.com/index.php',
            'attributes' => [
                'child' => true,
                'class' => 'email-fantom-web-email',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.my_lingle_propery_website', [
            'uri' => 'http://www.mysinglepropertywebsites.com/session/new',
            'attributes' => [
                'child' => true,
                'class' => 'my-lingle-propery-website',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.transaction_manager_back_agent', [
            'uri' => 'https://fathom.backagent.net/start/',
            'attributes' => [
                'child' => true,
                'class' => 'transaction-manager-back-agent',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.video_training_on_demand', [
            'uri' => 'http://www.realestatetrainingbydavidknox.com/login',
            'attributes' => [
                'child' => true,
                'class' => 'video-training-on-demand',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.third_party_links.knowlage_base_all_things_fantom', [
            'uri' => 'https://www.fathomwiki.com/#',
            'attributes' => [
                'child' => true,
                'class' => 'knowlage-base-all-things-fantom',
            ],
            'linkAttributes' => ['target' => '_blank'],
        ])->setExtra('translation_domain', 'messages');
    }
    
    private function createTermsDropdown(ItemInterface $menu)
    {
        $dropdown = $menu->addChild('navigation.terms.terms', [
            'uri' => 'javascript:;',
            'attributes' => [
                'dropdown' => true,
                'class' => 'has-sub',
                'icon' => 'fa fa-legal',
            ],
            'childrenAttributes' => [
                'class' => 'sub-menu',
            ],
            'routes' => [
                ['route' => 'admin_agent_terms_privacy_policy'],
                ['route' => 'admin_agent_terms_refund_policy'],
            ],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.terms.privacy_policy', [
            'route' => 'admin_agent_terms_privacy_policy',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');

        $dropdown->addChild('navigation.terms.refund_policy', [
            'route' => 'admin_agent_terms_refund_policy',
            'attributes' => ['child' => true],
        ])->setExtra('translation_domain', 'messages');
    }

    /**
     * Check if current authenticated user is Admin or District Director
     * @return boolean
     */
    private function isAdminOrDD()
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN') ||
               $this->authorizationChecker->isGranted('ROLE_AGENT') && $this->user->isDistrictDirector();
    }

    /**
     * Check if current authenticated user is Admin or District Director
     * @return boolean
     */
    private function isSuperAdminOrDD()
    {
        return $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') ||
        $this->authorizationChecker->isGranted('ROLE_AGENT') && $this->user->isDistrictDirector();
    }
}
