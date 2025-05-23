<?php

use Prli\GroundLevel\Container\Concerns\HasStaticContainer;
use Prli\GroundLevel\Container\Container;
use Prli\GroundLevel\Container\Contracts\StaticContainerAwareness;
use Prli\GroundLevel\InProductNotifications\Service as IPNService;
use Prli\GroundLevel\Mothership\Service as MoshService;
use Prli\GroundLevel\Support\Concerns\Hookable;
use Prli\GroundLevel\Support\Models\Hook;

/**
 * Initializes a GroundLevel container and dependent services.
 */
class PrliGrdLvlController extends PrliBaseController implements StaticContainerAwareness
{
    use HasStaticContainer;
    use Hookable;

    /**
     * Returns an array of Hooks that should be added by the class.
     *
     * @return array
     */
    protected function configureHooks(): array
    {
        return [
            new Hook(Hook::TYPE_ACTION, 'init', __CLASS__ . '::init', 5),
        ];
    }

    /**
     * Loads the hooks for the controller.
     */
    public function load_hooks()
    {
        $this->addHooks();
    }

    /**
     * Initializes a GroundLevel container and dependent services.
     *
     */
    public static function init(): void
    {
        /**
         * Currently we're loading a container, mothership, and ipn services in order
         * to power IPN functionality. We don't need the container or mothership
         * for anything other than IPN so we can skip the whole load if notifications
         * are disabled or unavailable for the user.
         *
         * Later we'll want to move this condition to be only around the {@see self::init_ipn()}
         * load method.
         */
        if (PrliNotifications::has_access()) {
            self::setContainer(new Container());

            /**
             * @todo: Later we'll want to "properly" bootstrap a container via a
             * plugin bootstrap via GrdLvl package.
             */

            self::init_mothership();
            self::init_ipn();
        }
    }

    /**
     * Initializes and configures the IPN Service.
     */
    private static function init_ipn(): void
    {
        $pl_link_cpt = PrliLink::$cpt;

        // Set IPN Service parameters.
        self::$container->addParameter(IPNService::PRODUCT_SLUG, PRLI_EDITION);
        self::$container->addParameter(IPNService::PREFIX, 'prli');
        self::$container->addParameter(IPNService::MENU_SLUG, "edit.php?post_type={$pl_link_cpt}");
        self::$container->addParameter(
            IPNService::RENDER_HOOK,
            'prli_admin_header_actions'
        );
        self::$container->addParameter(
            IPNService::THEME,
            [
                'primaryColor'       => '#4751ff',
                'primaryColorDarker' => '#ff9000',
            ]
        );

        self::$container->addService(
            IPNService::class,
            static function (Container $container): IPNService {
                return new IPNService($container);
            },
            true
        );
    }

    /**
     * Initializes the Mothership Service.
     */
    private static function init_mothership(): void
    {
        self::$container->addService(
            MoshService::class,
            static function (Container $container): MoshService {
                return new MoshService(
                    $container,
                    new PrliMothershipPluginConnector()
                );
            },
            true
        );
    }
}
