<?php

namespace FluentCrm\App\Http\Controllers;

use FluentCrm\App\Models\Company;
use FluentCrm\App\Models\Funnel;
use FluentCrm\App\Models\Tag;
use FluentCrm\App\Models\Lists;
use FluentCrm\App\Services\Helper;
use FluentCrm\App\Models\Campaign;
use FluentCrm\Framework\Request\Request;

/**
 *  OptionsController - REST API Handler Class
 *
 *  REST API Handler
 *
 * @package FluentCrm\App\Http
 *
 * @version 1.0.0
 */
class OptionsController extends Controller
{
    /**
     * Get options based on the requested fields.
     *
     * @return array options.
     * @throws \Exception
     */
    public function index()
    {
        if ($fileds = $this->request->get('fields')) {
            $options = array_unique(explode(',', $fileds));

            $response = [];

            foreach ($options as $method) {
                if (method_exists($this, $method)) {
                    $result = $this->{$method}();
                    $response = array_merge($response, $result);
                }
            }

            return [
                'options' => $response
            ];
        }

        throw new \Exception('Missing requested fields field.', 422);
    }

    /**
     * Include the countries options.
     *
     * @return array
     */
    public function countries()
    {
        /**
         * Determine the list of countries in FluentCRM.
         *
         * This filter allows you to modify the list of countries used in FluentCRM.
         *
         * @since 2.7.0
         *
         * @param array An array of countries.
         */
        $countries = apply_filters('fluent_crm/countries', []);
        $formattedCountries = [];
        foreach ($countries as $country) {
            $country['id'] = $country['code'];
            $country['slug'] = $country['code'];
            $formattedCountries[] = $country;
        }
        return [
            'countries' => $formattedCountries
        ];
    }

    /**
     * Include all the lists.
     *
     * @return array
     */
    public function lists()
    {
        $lists = Lists::select(['id', 'slug', 'title'])->orderBy('title', 'ASC')->get();

        $withCount = (array)$this->request->get('with_count', []);

        if ($withCount && in_array('lists', $withCount)) {
            foreach ($lists as $list) {
                $list->subscribersCount = $list->countByStatus('subscribed');
            }
        }

        return [
            'lists' => $lists
        ];
    }

    /**
     * Include all the tags.
     *
     * @return array
     */
    public function tags()
    {
        $tags = Tag::select(['id', 'slug', 'title'])->orderBy('title', 'ASC')->get();
        foreach ($tags as $tag) {
            $tag->value = strval($tag->id);
            $tag->label = $tag->title;
        }
        return [
            'tags' => $tags
        ];
    }

    /**
     * Include all the Campaigns.
     *
     * @return array
     */
    public function campaigns()
    {
        return [
            'campaigns' => Campaign::select('id', 'title')->orderBy('id', 'DESC')->get()
        ];
    }

    /**
     * Include all the EmailSequences.
     *
     * @return array
     */
    public function email_sequences()
    {
        $sequences = [];

        if (defined('FLUENTCAMPAIGN')) {
            $sequences = \FluentCampaign\App\Models\Sequence::select('id', 'title')->orderBy('id', 'DESC')->get();
        }

        return [
            'email_sequences' => $sequences
        ];
    }

    /**
     * Include all the Automation Funnels.
     *
     * @return array
     */
    public function automation_funnels()
    {
        $funnels = Funnel::select('id', 'title', 'status')
            ->orderBy('id', 'DESC')->get();

        foreach ($funnels as $funnel) {
            $funnel->title .= ' (' . $funnel->status . ')';
        }

        return [
            'automation_funnels' => $funnels
        ];
    }

    /**
     * Include all the Companies.
     *
     * @return array
     */
    public function companies()
    {
        return [
            'companies' => Company::select('id', 'name as title')->orderBy('id', 'DESC')->get()
        ];
    }

    /**
     * Include subscriber statuses.
     *
     * @return array
     */
    public function statuses()
    {
        return [
            'statuses' => fluentcrm_subscriber_statuses(true)
        ];
    }

    /**
     * Include subscriber editable statuses.
     *
     * @return array
     */
    public function editable_statuses()
    {
        return [
            'editable_statuses' => fluentcrm_subscriber_editable_statuses(true)
        ];
    }

    /**
     * Include subscriber Contact Types.
     *
     * @return array
     */
    public function contact_types()
    {
        return [
            'contact_types' => fluentcrm_contact_types(true)
        ];
    }

    /**
     * Include the sample csv url.
     *
     * @return array
     */
    public function sampleCsv()
    {
        return [
            'sampleCsv' => $this->app['url.assets'] . 'sample.csv'
        ];
    }

    public function segments()
    {
        /**
         * Determine the dynamic segments in FluentCRM.
         *
         * This filter allows you to modify the dynamic segments used in FluentCRM.
         *
         * @since 1.0.0
         *
         * @param array An array of dynamic segments.
         */
        $segments = apply_filters('fluentcrm_dynamic_segments', []);

        return [
            'segments' => $segments
        ];
    }

    public function roles()
    {
        if (!function_exists('get_editable_roles')) {
            require_once(ABSPATH . '/wp-admin/includes/user.php');
        }

        return [
            'roles' => \get_editable_roles()
        ];
    }

    public function user_roles_options()
    {
        $roles = $this->roles();

        $formattedRoles = [];
        foreach ($roles['roles'] as $role => $roleData) {
            $formattedRoles[] = [
                'id'    => $role,
                'title' => $roleData['name'],
                'slug'  => $role
            ];
        }

        return [
            'user_roles_options' => $formattedRoles
        ];
    }

    public function profile_sections()
    {
        return [
            'profile_sections' => Helper::getProfileSections()
        ];
    }

    public function custom_fields()
    {
        return [
            'custom_fields' => fluentcrm_get_option('contact_custom_fields', [])
        ];
    }

    public function getAjaxOptions(Request $request)
    {
        $optionKey = $request->getSafe('option_key');
        $search = $request->getSafe('search');
        $includedIds = $request->getSafe('values');

        $options = [];

        if ($optionKey == 'woo_categories') {
            // woocommerce categories
            if (defined('WC_PLUGIN_FILE')) {
                $cat_args = array(
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                    'hide_empty' => false,
                    'search'     => $search,
                    'number'     => 50
                );
                $product_categories = get_terms('product_cat', $cat_args);

                $pushedIds = [];
                foreach ($product_categories as $category) {
                    $options[] = [
                        'id'    => $category->term_id,
                        'title' => $category->name
                    ];
                    $pushedIds[] = $category->term_id;
                }

                if (empty($includedIds)) {
                    $includedIds = $pushedIds;
                }
                $includedIds = array_diff($includedIds, $pushedIds);

                if ($includedIds) {
                    $cat_args = array(
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'hide_empty' => false,
                        'include'    => $includedIds
                    );
                    $product_categories = get_terms('product_cat', $cat_args);
                    foreach ($product_categories as $category) {
                        $options[] = [
                            'id'    => $category->term_id,
                            'title' => $category->name
                        ];
                    }
                }
            }

            return [
                'options' => $options
            ];
        }

        $wooProductKeys = ['woo_products', 'product_selector_woo', 'product_selector_woo_order'];

        if (in_array($optionKey, $wooProductKeys)) {
            if (defined('WC_PLUGIN_FILE')) {

                $args = [
                    'limit'   => 50,
                    'orderby' => 'date',
                    'order'   => 'DESC',
                    's'       => $search
                ];

                $pushedIds = [];

                $subOptionKey = $request->getSafe('sub_option_key', []);
                if (!empty($subOptionKey)) {
                    $args['type'] = $subOptionKey;
                }

                $products = wc_get_products($args);

                foreach ($products as $product) {
                    $productId = $product->get_id();
                    $options[] = [
                        'id'    => $productId,
                        'title' => $product->get_name()
                    ];
                    $pushedIds[] = $productId;
                }

                if (empty($includedIds)) {
                    $includedIds = $pushedIds;
                } else {
                    $includedIds = (array)$includedIds;
                }

                $includedIds = array_diff($includedIds, $pushedIds);

                if ($includedIds) {
                    $products = wc_get_products([
                        'orderby' => 'date',
                        'order'   => 'DESC',
                        'include' => $includedIds
                    ]);
                    foreach ($products as $product) {
                        $productId = $product->get_id();
                        $options[] = [
                            'id'    => $productId,
                            'title' => $product->get_name()
                        ];
                    }
                }
            }

            return [
                'options' => $options
            ];

        }

        if ($optionKey == 'edd_products' || $optionKey == 'product_selector_edd') {
            if (class_exists('Easy_Digital_Downloads') && defined('FLUENTCAMPAIGN')) {
                $options = \FluentCampaign\App\Services\Integrations\Edd\Helper::getProducts();
            }

            return [
                'options' => $options
            ];

        }

        if($optionKey == 'voxel_products' || $optionKey == 'product_selector_voxel') {
            $pushedIds = [];
            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => 20
            );

            if ($search) {
                $args['s'] = $search;
            }

            $query = new \WP_Query($args);
            $products = $query->posts;

            foreach ($products as $product) {
                $options[] = [
                    'id' => $product->ID,
                    'title' => $product->post_title
                ];
                $pushedIds[] = $product->ID;
            }

            if ($includedIds) {
                $includedIds = array_diff($includedIds, $pushedIds);
                if ($includedIds) {
                    $args = array(
                        'post_type' => 'product',
                        'post_status' => 'publish',
                        'post__in' => $includedIds
                    );
                    $query = new \WP_Query($args);
                    $products = $query->posts;

                    foreach ($products as $product) {
                        $options[] = [
                            'id' => $product->ID,
                            'title' => $product->post_title
                        ];
                    }
                }
            }

            return [
                'options' => $options
            ];
        }

        if($optionKey == 'voxel_product_types') {
            $formattedTypes = [];
            
            if (class_exists('\Voxel\Product_Type')) {
                $product_types = \Voxel\Product_Type::get_all();
                
                foreach ($product_types as $product_type) {
                    $formattedTypes[] = [
                        'id' => $product_type->get_key(),
                        'title' => $product_type->get_label()
                    ];
                }
            }

            return [
                'options' => $formattedTypes
            ];
        }

        if ($optionKey == 'campaigns' || $optionKey == 'funnels' || $optionKey == 'email_sequences') {

            if ($optionKey == 'campaigns') {
                $objectModel = Campaign::select(['id', 'title', 'status'])->where('status', '!=', 'draft');
            } else if ($optionKey == 'funnels') {
                $objectModel = Funnel::select(['id', 'title', 'status']);
            } else if ($optionKey == 'email_sequences') {
                if (!defined('FLUENTCAMPAIGN')) {
                    return [
                        'options' => []
                    ];
                }
                $objectModel = \FluentCampaign\App\Models\Sequence::select(['id', 'title', 'status']);
            } else {
                return [
                    'options' => []
                ];
            }

            $items = $objectModel
                ->when($search, function ($query) use ($search) {
                    return $query->where('title', 'LIKE', "%$search%");
                })
                ->limit(20)
                ->orderBy('id', 'DESC')
                ->get();

            $pushedIds = [];

            foreach ($items as $item) {
                $options[] = [
                    'id'    => $item->id,
                    'title' => $item->title . ' - ' . $item->id
                ];
                $pushedIds[] = $item->id;
            }

            if (!$includedIds) {
                return [
                    'options' => $options
                ];
            }

            $includedIds = (array)$includedIds;

            $includedIds = array_diff($includedIds, $pushedIds);
            if ($includedIds) {

                if ($optionKey == 'campaigns') {
                    $objectModel = Campaign::select(['id', 'title', 'status']);
                } else if ($optionKey == 'funnels') {
                    $objectModel = Funnel::select(['id', 'title', 'status']);
                } else if ($optionKey == 'email_sequences') {
                    $objectModel = \FluentCampaign\App\Models\Sequence::select(['id', 'title', 'status']);
                } else {
                    return [
                        'options' => $options
                    ];
                }

                $items = $objectModel->whereIn('id', $includedIds)->get();
                foreach ($items as $item) {
                    $options[] = [
                        'id'    => $item->id,
                        'title' => $item->title . ' - ' . $item->id
                    ];
                }
            }

            return [
                'options' => $options
            ];
        }

        if ($optionKey == 'companies') {
            if (!Helper::isCompanyEnabled()) {
                return [
                    'options' => []
                ];
            }

            $companies = Company::select(['id', 'name'])
                ->searchBy($search)
                ->limit(20)
                ->orderBy('id', 'DESC')
                ->get();

            $pushedIds = [];
            foreach ($companies as $company) {
                $options[] = [
                    'id'    => $company->id,
                    'title' => $company->name
                ];
                $pushedIds[] = $company->id;
            }

            if (empty($includedIds)) {
                $includedIds = $pushedIds;
            }
            $includedIds = array_diff($includedIds, $pushedIds);

            if ($includedIds) {
                $companies = Company::select(['id', 'name'])
                    ->whereIn('id', $includedIds)
                    ->get();
                foreach ($companies as $company) {
                    $options[] = [
                        'id'    => $company->id,
                        'title' => $company->name
                    ];
                }
            }

            return [
                'options' => $options
            ];
        }

        if ($optionKey == 'post_type') {
            $postType = $request->getSafe('sub_option_key', '');
            if (!$postType) {
                return [
                    'options' => []
                ];
            }

            $args = [
                'post_type'      => $postType,
                'posts_per_page' => 20
            ];

            if ($search) {
                $args['s'] = $search;
            }

            $posts = get_posts($args);

            $formattedPosts = [];
            if (!is_wp_error($posts)) {
                foreach ($posts as $post) {
                    $formattedPosts[$post->ID] = [
                        'id'    => strval($post->ID),
                        'title' => $post->post_title
                    ];
                }
            }

            if (!$includedIds) {
                return [
                    'options' => array_values($formattedPosts)
                ];
            }

            $includedIds = (array)$includedIds;

            $includedIds = array_diff($includedIds, array_keys($formattedPosts));
            if ($includedIds) {
                $posts = get_posts([
                    'post_type' => $postType,
                    'post__in'  => $includedIds
                ]);
                foreach ($posts as $post) {
                    $formattedPosts[$post->ID] = [
                        'id'    => strval($post->ID),
                        'title' => $post->post_title
                    ];
                }
            }

            return [
                'options' => array_values($formattedPosts)
            ];
        }

        if ($optionKey == 'company_industries') {
            $companyCategories = Helper::companyCategories();

            $formattedCategories = [];
            foreach ($companyCategories as $category) {
                $formattedCategories[] = [
                    'id'    => $category,
                    'title' => $category
                ];
            }

            return [
                'options' => $formattedCategories
            ];
        }

        if ($optionKey == 'company_types') {
            $companyTypes = Helper::companyTypes();

            $formattedTypes = [];
            foreach ($companyTypes as $type) {
                $formattedTypes[] = [
                    'id'    => $type,
                    'title' => $type
                ];
            }

            return [
                'options' => $formattedTypes
            ];
        }

        if ($optionKey == 'users') {
            $users = Helper::searchWPUsers($search);

            $usersWithLessFields = [];

            foreach ($users as $user) {
                $usersWithLessFields[] = [
                    'id'               => $user->ID,
                    'user_email'       => $user->user_email,
                    'name'             => $user->display_name ?? $user->user_email,
                    'title'            => $user->display_name . ' (' . $user->user_email . ')'
                ];
            }

            return [
                'options' => $usersWithLessFields
            ];
        }


        return [
            /**
             * Determine the AJAX options for FluentCRM.
             *
             * This filter allows modification of the AJAX options based on the provided option key, search term, and included IDs.
             *
             * @since 2.5.9
             *
             * @param array  The options array to be filtered.
             * @param string $search      The search term used to filter the options.
             * @param array  $includedIds The IDs to be included in the options.
             */
            'options' => apply_filters('fluentcrm_ajax_options_' . $optionKey, [], $search, $includedIds)
        ];
    }

    public function getTaxonomyTerms(Request $request)
    {
        $taxonomy = $request->get('taxonomy');
        $search = $request->get('search');
        $includeIds = (array)$request->get('values', []);

        $args = [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'number'     => 20
        ];

        if ($search) {
            $args['search'] = $search;
        }

        $terms = get_terms($args);

        $formattedTerms = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $formattedTerms[$term->term_id] = [
                    'id'    => strval($term->term_id),
                    'title' => $term->name
                ];
            }
        }

        if ($includeIds && $formattedTerms) {
            $includeIds = array_diff($includeIds, array_keys($formattedTerms));
            if ($includeIds) {
                $includedTerms = get_terms([
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                    'include'    => $includeIds
                ]);

                if (!is_wp_error($includedTerms)) {
                    foreach ($includedTerms as $includedTerm) {
                        $formattedTerms[$includedTerm->term_id] = [
                            'id'    => strval($includedTerm->term_id),
                            'title' => $includedTerm->name
                        ];
                    }
                }
            }
        }

        return [
            'options' => array_values($formattedTerms)
        ];

    }

    public function getCascadeSelections(Request $request)
    {
        $provider = $request->get('provider');

        /**
         * Determine the cascade selection options for a given provider.
         *
         * The dynamic portion of the hook name, `$provider`, refers to the specific provider for which the options are being filtered.
         *
         * @since 2.9.23
         *
         * @param array {
         *     An array of options for the cascade selection.
         *
         *     @type array $options  The options for the selection.
         *     @type bool  $has_more Whether there are more options available.
         * }
         * @param array $request The request data.
         */
        return apply_filters('fluent_crm/cascade_selection_options_'.$provider, [
            'options' => [],
            'has_more' => true
        ], $request->all());
    }
}
