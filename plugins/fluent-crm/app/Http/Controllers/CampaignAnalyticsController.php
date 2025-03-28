<?php

namespace FluentCrm\App\Http\Controllers;

use FluentCrm\App\Models\Campaign;
use FluentCrm\App\Models\CampaignUrlMetric;
use FluentCrm\App\Services\Helper;
use FluentCrm\Framework\Request\Request;

/**
 *  CampaignAnalyticsController - REST API Handler Class
 *
 *  REST API Handler
 *
 * @package FluentCrm\App\Http
 *
 * @version 1.0.0
 */
class CampaignAnalyticsController extends Controller
{
    public function getLinksReport(CampaignUrlMetric $campaignUrlMetric, $campaignId)
    {
        return $this->sendSuccess([
            'links' =>  array_values($campaignUrlMetric->getLinksReport($campaignId))
        ]);
    }

    public function getRevenueReport(Request $request, $campaignId)
    {
        $limit = intval($request->get('per_page', 10));
        $offset = (intval($request->get('page', 1)) - 1) * $limit;


        if (defined('WC_PLUGIN_FILE')) {

            if(Helper::isWooHposEnabled()) {
                $orderMetas = fluentCrmDb()->table('wc_orders_meta')
                    ->select('order_id')
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->orderBy('id', 'DESC')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();

                $totalOrders = fluentCrmDb()->table('wc_orders_meta')
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->count();
            } else {
                $orderMetas = fluentCrmDb()->table('postmeta')
                    ->select(fluentCrmDb()->raw('post_id as order_id'))
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->orderBy('meta_id', 'DESC')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();

                $totalOrders = fluentCrmDb()->table('postmeta')
                    ->select('post_id')
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->count();
            }


            $orders = [];
            foreach ($orderMetas as $orderMeta) {
                $order = wc_get_order($orderMeta->order_id);
                if (!$order || !$order->get_id()) {
                    continue;
                }

                $buyer = trim(sprintf(_x('%1$s %2$s', 'full name', 'fluent-crm'), $order->get_billing_first_name(), $order->get_billing_last_name()));

                $order_timestamp = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : '';

                if (!$order_timestamp) {
                    $show_date = '&ndash;';
                } else if ($order_timestamp > strtotime('-1 day', time()) && $order_timestamp <= time()) {
                    $show_date = sprintf(
                    /* translators: %s: human-readable time difference */
                        _x('%s ago', '%s = human-readable time difference', 'woocommerce'),
                        human_time_diff($order->get_date_created()->getTimestamp(), time())
                    );
                } else {
                    /**
                     * Determine the date format for displaying the order creation date in the WooCommerce admin in FluentCRM.
                     *
                     * @since 2.2.0
                     * 
                     * @param string The date format to be used. Default is 'M j, Y'.
                     * @param string The context for the date format. Default is 'woocommerce'.
                     */
                    $show_date = $order->get_date_created()->date_i18n(apply_filters('woocommerce_admin_order_date_format', __('M j, Y', 'woocommerce')));
                }

                $orders[] = [
                    'id'     => $order->get_id(),
                    'title'  => '<a href="' . esc_url(admin_url('post.php?post=' . absint($order->get_id())) . '&action=edit') . '" class="order-view"><strong>#' . esc_attr($order->get_order_number()) . ' ' . esc_html($buyer) . '</strong></a>',
                    'status' => wc_get_order_status_name($order->get_status()),
                    'date'   => $show_date,
                    'total'  => $order->get_formatted_order_total()
                ];
            }

            return [
                'orders' => $orders,
                'labels' => [
                    'id'     => __('ID', 'fluent-crm'),
                    'title'  => __('Title', 'fluent-crm'),
                    'status' => __('Status', 'fluent-crm'),
                    'date'   => __('Date', 'fluent-crm'),
                    'total'  => __('Total', 'fluent-crm')
                ],
                'total'  => $totalOrders
            ];
        } else if (class_exists('\Easy_Digital_Downloads')) {

            // We have Edd 2 Here
            $orderMetas = fluentCrmDb()->table('postmeta')
                ->select('post_id')
                ->where('meta_key', '_fc_cid')
                ->where('meta_value', $campaignId)
                ->orderBy('meta_id', 'DESC')
                ->limit($limit)
                ->offset($offset)
                ->get();

            foreach ($orderMetas as $orderMeta) {
                $payment = new \EDD_Payment($orderMeta->post_id);
                if (!$payment || !$payment->ID) {
                    continue;
                }
                $orderActionHtml = '<a href="' . add_query_arg('id', $payment->ID, admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details')) . '">' . __('View Order Details', 'fluent-crm') . '</a>';
                $amount = $payment->total;
                $amount = !empty($amount) ? $amount : 0;

                $customer_id = edd_get_payment_customer_id($payment->ID);

                if (!empty($customer_id)) {
                    $customer = new \EDD_Customer($customer_id);
                    $customerName = '<a href="' . esc_url(admin_url("edit.php?post_type=download&page=edd-customers&view=overview&id=$customer_id")) . '">' . $customer->name . '</a>';
                } else {
                    $email = edd_get_payment_user_email($payment->ID);
                    $customerName = '<a href="' . esc_url(admin_url("edit.php?post_type=download&page=edd-payment-history&s=$email")) . '">' . __('(customer missing)', 'easy-digital-downloads') . '</a>';
                }

                $formattedOrders[] = [
                    'order'  => '#' . $payment->number,
                    'title'  => $customerName,
                    'date'   => date_i18n(get_option('date_format'), strtotime($payment->date)),
                    'status' => $payment->status_nicename,
                    'total'  => edd_currency_filter(edd_format_amount($amount), edd_get_payment_currency_code($payment->ID)),
                    'action' => $orderActionHtml
                ];
            }
            return [
                'orders' => $formattedOrders,
                'labels' => [
                    'order'  => '#',
                    'title'  => __('Customer', 'fluent-crm'),
                    'status' => __('Status', 'fluent-crm'),
                    'date'   => __('Date', 'fluent-crm'),
                    'total'  => __('Total', 'fluent-crm'),
                    'action' => __('View', 'fluent-crm')
                ],
                'total'  => fluentCrmDb()->table('postmeta')
                    ->select('post_id')
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->count()
            ];
        }

        return [
            'orders' => [],
            'labels' => [
                'order'  => '#',
                'title'  => __('Customer', 'fluent-crm'),
                'status' => __('Status', 'fluent-crm'),
                'date'   => __('Date', 'fluent-crm'),
                'total'  => __('Total', 'fluent-crm')
            ],
            'total'  => 0
        ];
    }

    public function getRevenueReSyncReport(Request $request, $campaignId)
    {
        $campaignRevenue = fluentcrm_get_campaign_meta($campaignId, '_campaign_revenue');

        $orderIds = [];
        if (defined('WC_PLUGIN_FILE')) {
            if(Helper::isWooHposEnabled()) {
                $orderMetas = fluentCrmDb()->table('wc_orders_meta')
                    ->select('order_id')
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->orderBy('id', 'DESC')
                    ->get();
            } else {
                $orderMetas = fluentCrmDb()->table('postmeta')
                    ->select(fluentCrmDb()->raw('post_id as order_id'))
                    ->where('meta_key', '_fc_cid')
                    ->where('meta_value', $campaignId)
                    ->orderBy('meta_id', 'DESC')
                    ->get();
            }

            foreach ($orderMetas as $orderMeta) {
                $order = wc_get_order($orderMeta->order_id);
                if (!$order || !$order->get_id()) {
                    continue;
                }
                $orderIds[] = $order->get_id();
            }
        }

        if (!$campaignRevenue) {
            return [
                'message' => __('No revenue found for this campaign', 'fluent-crm')
            ];
        }

        if (!$orderIds) {
            return [
                'message' => __('No order found to re-sync', 'fluent-crm')
            ];
        }

        $currency = strtolower(get_woocommerce_currency());
        $revenueData = [
            'orderIds' => $orderIds
        ];
        $totalRevenue = 0;

        foreach ($orderIds as $orderId) {
            $order = wc_get_order( $orderId );
            if ($order) {
                $totalRevenue += intval($order->get_total() * 100);
            }
        }
        $revenueData[$currency] = $totalRevenue;

        fluentcrm_update_campaign_meta($campaignId, '_campaign_revenue', $revenueData);

        return [
            'message' => __('Revenue has been re-synced successfully', 'fluent-crm'),
            'total' => number_format($totalRevenue / 100, 2)
        ];
    }

    public function getUnsubscribers(Request $request, $campaignId)
    {
        $unsubscribes = CampaignUrlMetric::with('subscriber')
            ->where('campaign_id', $campaignId)
            ->where('type', 'unsubscribe')
            ->paginate();

        foreach ($unsubscribes as $unsubscribe) {
            $unsubscribe->subscriber->reason = $unsubscribe->subscriber->unsubscribeReason();
        }

        return [
            'unsubscribes' => $unsubscribes
        ];
    }

    public function getSegmentedContacts(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $contactsModel = $campaign->getSubscribersModel();

        $search = $request->getSafe('search');

        if ($search) {
            $contactsModel->searchBy($search);
        }

        if ($orderBy = $request->getSafe('sort_by', 'id', 'sanitize_sql_orderby')) {
            $orderType = $request->getSafe('sort_type', 'desc', 'sanitize_sql_orderby');
            $contactsModel->orderBy($orderBy, $orderType);
        }

        $contacts = $contactsModel->with(['lists', 'tags'])->paginate();

        return [
            'subscribers' => $contacts
        ];
    }
}
