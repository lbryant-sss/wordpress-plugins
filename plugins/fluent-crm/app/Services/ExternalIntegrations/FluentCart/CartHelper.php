<?php

namespace FluentCrm\App\Services\ExternalIntegrations\FluentCart;

use FluentCart\Api\Taxonomy;
use FluentCart\App\Helpers\Status;
use FluentCart\App\Models\Coupon;
use FluentCart\App\Models\Product;
use FluentCart\App\Models\ProductVariation;
use FluentCart\Framework\Database\Orm\Collection;
use FluentCart\Framework\Support\Str;
use FluentCart\App\Models\Customer;

class CartHelper
{
    public static function getFluentCartProducts($items, $search, $ids = [])
    {
        $search = (string)$search;
        $ids = is_array($ids) ? $ids : [];

        try {
            $productQuery = Product::query()->published();
            if ($search) {
                $productQuery->where('post_title', 'like', '%' . $search . '%');
            }

            $queried = $productQuery
                ->orderBy('post_title')
                ->limit(50)
                ->get(['ID', 'post_title']);

            $options = [];
            $pushedIds = [];
            foreach ($queried as $product) {
                $options[] = [
                    'id'    => $product->ID,
                    'title' => $product->ID . '# ' . $product->post_title,
                ];
                $pushedIds[] = $product->ID;
            }

            if ($ids) {
                $remaining = array_diff($ids, $pushedIds);
                if ($remaining) {
                    $extraProducts = Product::query()->published()->whereIn('ID', $remaining)->get(['ID', 'post_title']);
                    foreach ($extraProducts as $product) {
                        $options[] = [
                            'id'    => $product->ID,
                            'title' => $product->ID . '# ' . $product->post_title,
                        ];
                    }
                }
            }

            return $options;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public static function getFluentCartCoupons($items, $search, $ids)
    {
        try {
            $coupons = Coupon::all(['id', 'title'])
                ->map(function ($coupon) {
                    return [
                        'id'    => $coupon->id,
                        'title' => $coupon->title,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $coupons = [];
        }

        return $coupons;
    }

    public static function getFluentCartProductCategories($items, $search, $ids)
    {
        try {
            $taxonomies = Taxonomy::getTaxonomies();

            $taxonomies = Collection::make($taxonomies)
                ->map(function ($taxonomy) {
                    return [
                        'name'  => $taxonomy,
                        'label' => Str::headline($taxonomy),
                        'terms' => Taxonomy::getFormattedTerms($taxonomy),
                    ];
                });

            $categories = Collection::make($taxonomies['product-categories']['terms'])
                ->map(function ($term) {
                    return [
                        'id'    => $term['value'],
                        'title' => $term['label'],
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $categories = [];
        }

        return $categories;
    }

    public static function getProductCategoriesByIds($ids)
    {
        try {
            $products = Product::with('wp_terms')->whereIn('id', $ids)->get();

            $categories = $products->flatMap(function ($product) {
                return $product->wp_terms->pluck('term_taxonomy_id');
            })->unique()->values()->toArray();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $categories = [];
        }

        return $categories;
    }

    public static function getFluentCartSubscriptionProducts($items, $search, $ids)
    {
        $search = (string)$search;
        $ids = is_array($ids) ? $ids : [];

        try {
            $variationQuery = ProductVariation::query()
                ->where('payment_type', 'subscription')
                ->where('item_status', 'active');

                if ($search) {
                    $variationQuery->where('variation_title', 'like', '%' . $search . '%');
                }

                $productIds = $variationQuery->pluck('post_id')->unique()->slice(0, 50)->values();

                $pushedIds = $productIds->toArray();
                if ($ids) {
                    $appendIds = array_diff($ids, $pushedIds);
                    if ($appendIds) {
                        $productIds = $productIds->merge($appendIds);
                    }
                }

                if ($productIds->isEmpty()) {
                    return [];
                }

                $products = Product::query()
                    ->published()
                    ->whereIn('ID', $productIds->toArray())
                    ->orderBy('post_title')
                    ->get(['ID', 'post_title']);

                $formatted = [];
                foreach ($products as $product) {
                    $formatted[] = [
                        'id'    => $product->ID,
                        'title' => $product->ID . '# ' . $product->post_title,
                    ];
                }
                return $formatted;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return [];
            }
        }
    public static function getAutomationConditions($groups, $funnel)
    {
        $disabled = false;

        $customerItems = [
            [
                'value'    => 'total_order_count', // customer_order_count
                'label'    => __('Total Order Count', 'fluent-crm'),
                'type'     => 'numeric',
                'disabled' => false,
                'min'      => 1,
                'help'     => __('Will filter the contacts who have at least one order', 'fluent-crm')
            ],
            [
                'value'    => 'total_order_value', // customer_total_spend
                'label'    => __('Total Order Value', 'fluent-crm'),
                'type'     => 'numeric',
                'disabled' => false,
                'min'      => 1,
                'help'     => __('Will filter the contacts who have at least one order', 'fluent-crm')
            ],
            [
                'value'             => 'billing_country', // customer_billing_country
                'label'             => __('Country', 'fluent-crm'),
                'type'              => 'selections',
                'component'         => 'options_selector',
                'option_key'        => 'countries',
                'is_multiple'       => true,
                'disabled'          => true,
                'is_singular_value' => true
            ],
            [
                'value'       => 'guest_user', // customer_guest_user
                'label'       => __('Is guest?', 'fluent-crm'),
                'type'        => 'single_assert_option',
                'options'     => [
                    'yes' => __('Yes', 'fluent-crm'),
                    'no'  => __('No', 'fluent-crm')
                ],
                'disabled'    => true,
                'is_multiple' => false,
            ],
            [
                'value'    => 'first_order_date',
                'label'    => __('First Order Date', 'fluent-crm'),
                'type'     => 'dates',
                'disabled' => $disabled,
                'help'     => __('Will filter the contacts who have at least one order', 'fluent-crm')
            ],
            [
                'value'    => 'last_order_date',
                'label'    => __('Last Order Date', 'fluent-crm'),
                'type'     => 'dates',
                'disabled' => $disabled,
                'help'     => __('Will filter the contacts who have at least one order', 'fluent-crm')
            ],
            [
                'value'       => 'purchased_products',
                'option_key'  => 'fluent_cart_products',
                'label'       => 'Purchased Products',
                'type'        => 'selections',
                'is_multiple' => true,
                'disabled'    => false,
                'component'   => 'ajax_selector'
            ],
            [
                'value'       => 'purchased_categories',
                'option_key'  => 'fluent_cart_product_categories',
                'label'       => 'Product Categories',
                'type'        => 'selections',
                'is_multiple' => true,
                'disabled'    => false,
                'component'   => 'ajax_selector'
            ],
            [
                'value'       => 'used_coupons',
                'option_key'  => 'fluent_cart_coupons',
                'label'       => 'Used Coupons - 99',
                'type'        => 'selections',
                'is_multiple' => true,
                'disabled'    => false,
                'component'   => 'ajax_selector'
            ],
            [
                'value'       => 'purchased_tags',
                'label'       => __('Purchased Tags', 'fluent-crm'),
                'type'        => 'selections',
                'component'   => 'tax_selector',
                'taxonomy'    => 'product_tag',
                'is_multiple' => true,
                'disabled'    => true,
                'help'        => __('Will filter the contacts who have at least one order', 'fluent-crm')
            ],
            [
                'value'               => 'purchase_times_count',
                'label'               => __('Specific Product Purchase Times', 'fluent-crm'),
                'help'                => __('Count how many times a specific product was purchased by this contact', 'fluent-crm'),
                'type'                => 'times_numeric',
                'component'           => 'item_times_selection',
                'is_multiple'         => false,
                'disabled'            => true,
                'primary_selector'    => 'product_selector_woo',
                'primary_placeholder' => __('Select Product', 'fluent-crm'),
                'numeric_placeholder' => __('Purchase Times', 'fluent-crm'),
                'input_help'          => __('Select which product you want to match first then how many times it was purchased separately', 'fluent-crm'),
            ],
            [
                'value'             => 'commerce_exist',
                'label'             => __('Is a customer?', 'fluent-crm'),
                'type'              => 'selections',
                'is_multiple'       => false,
                'disable_values'    => true,
                'value_description' => __('This filter will check if a contact has at least one shop order or not', 'fluent-crm'),
                'custom_operators'  => [
                    'exist'     => __('Yes', 'fluent-crm'),
                    'not_exist' => __('No', 'fluent-crm'),
                ],
                'disabled'          => true
            ]
        ];

        $groups['fluent_cart_customer'] = [
            'label'    => __('FluentCart Customer', 'fluent-crm'),
            'value'    => 'fluent_cart_customer',
            'children' => $customerItems,
        ];

        $orderProps = [
            [
                'value'    => 'total_value',
                'label'    => __('Total Order Value', 'fluent-crm'),
                'type'     => 'numeric',
                'disabled' => false
            ],
            [
                'value'       => 'products_in_order',
                'label'       => __('Products in Order', 'fluent-crm'),
                'option_key'  => 'fluent_cart_products',
                'type'        => 'selections',
                'is_multiple' => true,
                'disabled'    => false,
                'component'   => 'ajax_selector'
            ],
            [
                'value'       => 'categories_in_order',
                'label'       => __('Purchased From Categories', 'fluent-crm'),
                'option_key'  => 'fluent_cart_product_categories',
                'type'        => 'selections',
                'is_multiple' => true,
                'disabled'    => false,
                'component'   => 'ajax_selector'
            ],
            [
                'value'       => 'coupons_in_order',
                'option_key'  => 'fluent_cart_coupons',
                'label'       => 'Used Coupons in Order',
                'type'        => 'selections',
                'is_multiple' => true,
                'disabled'    => false,
                'component'   => 'ajax_selector'
            ],
            [
                'value'             => 'billing_country',
                'label'             => __('Country', 'fluent-crm'),
                'type'              => 'selections',
                'component'         => 'options_selector',
                'option_key'        => 'countries',
                'is_multiple'       => true,
                'is_singular_value' => true
            ],
            [
                'value'    => 'shipping_method',
                'label'    => __('Shipping Method', 'fluent-crm'),
                'type'     => 'single_assert_option',
                'disabled' => true
            ],
            [
                'value'   => 'payment_gateway',
                'label'   => __('Payment Gateway', 'fluent-crm'),
                'type'    => 'single_assert_option',
                'options' => [
                    'Stripe' => 'stripe',
                    'Paypal' => 'paypal'
                ],
            ],
            [
                'value'   => 'order_status',
                'label'   => __('Order Status', 'fluent-crm'),
                'type'    => 'straight_assert_option',
                'options' => [
                    'on-hold'        => Status::ORDER_ON_HOLD,
                    'processing'     => Status::ORDER_PROCESSING,
                    'completed'      => Status::ORDER_COMPLETED,
                    'canceled'       => Status::ORDER_CANCELED,
                    'failed'         => Status::ORDER_FAILED,
                ],
            ]
        ];
        $groups['fluent_cart_order'] = [
            'label'    => __('FluentCart Current Order', 'fluent-crm'),
            'value'    => 'fluent_cart_order',
            'children' => $orderProps
        ];

        return $groups;
    }

    public static function prepareSubsciberData($customer)
    {
        if(!is_object($customer)) {
            $customer = (object) $customer;
        }
        return [
            'email' => $customer->email,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'full_name' => $customer->first_name . ' ' . $customer->last_name,
            'user_id' => $customer->user_id,
            'postal_code' => $customer->postcode,
            'country' => $customer->country,
            'state' => $customer->state,
            'city' => $customer->city,
            'phone' => $customer->phone,
        ];
    }

    public static function getCustomersByProductIds($productIds, $offset = 0, $limit = 100)
    {
        $customers = [];
        try {

            $customers = Customer::query()->whereHas('success_order_items', function ($q) use ($productIds) {
                $q->whereIn('post_id', $productIds);
            })->offset($offset)->limit($limit)->get();

        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $customers;
    }

    public static function getPurchasedProductsByCustomerId($customerId)
    {
        $productIds = [];
        try {
            $orderIds = fluentCrmDb()->table('fct_orders')
                ->where('customer_id', $customerId)
                ->pluck('id');

            $productIds = fluentCrmDb()->table('fct_order_items')
                ->whereIn('order_id', $orderIds)
                ->pluck('post_id');
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $productIds;
    }
}
