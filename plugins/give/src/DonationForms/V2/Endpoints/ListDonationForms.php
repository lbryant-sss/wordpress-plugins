<?php

namespace Give\DonationForms\V2\Endpoints;

use Give\Campaigns\Models\Campaign;
use Give\Campaigns\ValueObjects\CampaignType;
use Give\DonationForms\Repositories\DonationFormDataRepository;
use Give\DonationForms\V2\ListTable\DonationFormsListTable;
use Give\DonationForms\V2\Models\DonationForm;
use Give\Framework\Database\DB;
use Give\Framework\QueryBuilder\JoinQueryBuilder;
use Give\Framework\QueryBuilder\QueryBuilder;
use Give\Helpers\Language;
use WP_REST_Request;
use WP_REST_Response;

/**
 * @since 2.19.0
 */
class ListDonationForms extends Endpoint
{
    /**
     * @var string
     */
    protected $endpoint = 'admin/forms';

    /**
     * @var WP_REST_Request
     */
    private $request;

    /**
     * @var DonationFormsListTable
     */
    protected $listTable;

    /**
     * @var int
     */
    protected $defaultForm;

    /**
     * @since 4.0.0 Add campaignId parameter
     * @inheritDoc
     */
    public function registerRoute()
    {
        register_rest_route(
            'give-api/v2',
            $this->endpoint,
            [
                [
                    'methods' => 'GET',
                    'callback' => [$this, 'handleRequest'],
                    'permission_callback' => [$this, 'permissionsCheck'],
                ],
                'args' => [
                    'page' => [
                        'type' => 'integer',
                        'required' => false,
                        'default' => 1,
                        'minimum' => 1,
                    ],
                    'perPage' => [
                        'type' => 'integer',
                        'required' => false,
                        'default' => 30,
                        'minimum' => 1,
                    ],
                    'status' => [
                        'type' => 'string',
                        'required' => false,
                        'default' => 'any',
                        'enum' => [
                            'publish',
                            'future',
                            'draft',
                            'pending',
                            'trash',
                            'auto-draft',
                            'inherit',
                            'any',
                            'upgraded',
                        ],
                    ],
                    'search' => [
                        'type' => 'string',
                        'required' => false,
                    ],
                    'sortColumn' => [
                        'type' => 'string',
                        'required' => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'sortDirection' => [
                        'type' => 'string',
                        'required' => false,
                        'enum' => [
                            'asc',
                            'desc',
                        ],
                    ],
                    'locale' => [
                        'type' => 'string',
                        'required' => false,
                        'default' => get_locale(),
                    ],
                    'return' => [
                        'type' => 'string',
                        'required' => false,
                        'default' => 'columns',
                        'enum' => [
                            'model',
                            'columns',
                        ],
                    ],
                    'campaignId' => [
                        'type' => 'integer',
                        'required' => false,
                    ],
                ],
            ]
        );
    }

    /**
     * @since 3.22.0 Add locale support
     * @since 2.24.0 Change this to use the new ListTable class
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $this->request = $request;
        $this->listTable = give(DonationFormsListTable::class);
        $campaignId = (int)($this->request->get_param('campaignId'));
        $campaign = $campaignId ? Campaign::find($campaignId) : null;
        $defaultCampaignForm = $campaign ? $campaign->defaultForm() : null;

        $this->defaultForm = $defaultCampaignForm->id ?? 0;

        $forms = $this->getForms();
        $totalForms = $this->getTotalFormsCount();
        $totalPages = (int)ceil($totalForms / $this->request->get_param('perPage'));

        $formsData = DonationFormDataRepository::forms($forms);

        $this->listTable->setData($formsData);

        // get p2p forms
        $p2pForms = DB::table('give_campaigns')
            ->select('form_id')
            ->where('campaign_type', CampaignType::CORE, '!=')
            ->getAll(ARRAY_A);

        if ('model' === $this->request->get_param('return')) {
            $items = $forms;
        } else {
            $this->listTable->items($forms, $this->request->get_param('locale') ?? '');
            $items = $this->listTable->getItems();

            foreach ($items as $i => &$item) {
                $queryArgs = [
                    'locale' => Language::getLocale(),
                ];

                foreach ($p2pForms as $form) {
                    if ($item['id'] == $form['form_id']) {
                        $queryArgs['p2p'] = true;
                        break;
                    }
                }

                $item['name'] = $forms[$i]->title;
                $item['edit'] = add_query_arg($queryArgs, get_edit_post_link($item['id'], 'edit'));
                $item['permalink'] = get_permalink($item['id']);
                $item['v3form'] = $forms[$i]->usesFormBuilder;
                $item['status_raw'] = $forms[$i]->status->getValue();
                $item['isDefaultCampaignForm'] = $defaultCampaignForm && $item['id'] === $defaultCampaignForm->id;
            }
        }

        return new WP_REST_Response(
            [
                'items' => $items,
                'totalItems' => $totalForms,
                'totalPages' => $totalPages,
                'trash' => defined('EMPTY_TRASH_DAYS') && EMPTY_TRASH_DAYS > 0,
                'defaultForm' => $this->defaultForm,
            ]
        );
    }

    /**
     * @since 3.2.0 added distinct() to the query
     * @since 2.24.0 Refactor to query through the ModelQueryBuilder
     *
     * @return array
     */
    public function getForms(): array
    {
        $page = $this->request->get_param('page');
        $perPage = $this->request->get_param('perPage');
        $sortColumns = $this->listTable->getSortColumnById($this->request->get_param('sortColumn') ?: 'id');

        $query = give()->donationForms->prepareQuery();

        $query = $this->getWhereConditions($query);

        $query->orderByRaw('FIELD(ID, %d) DESC', $this->defaultForm);

        $sortDirection = $this->request->get_param('sortDirection') ?: 'desc';
        foreach ($sortColumns as $sortColumn) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $query->limit($perPage)
            ->offset(($page - 1) * $perPage);

        $donationForms = $query->distinct()->getAll();

        if ( ! $donationForms) {
            return [];
        }

        return $donationForms;
    }

    /**
     * @since 2.24.0 Refactor to query through the ModelQueryBuilder
     *
     * @return int
     */
    public function getTotalFormsCount(): int
    {
        $query = DB::table('posts')
            ->where('post_type', 'give_forms');

        $query = $this->getWhereConditions($query);

        return $query->count();
    }

    /**
     * @since      4.0.0 Add "campaignId" support
     * @since      2.24.0
     *
     * @param QueryBuilder $query
     *
     * @return QueryBuilder
     */
    private function getWhereConditions(QueryBuilder $query): QueryBuilder
    {
        $search = $this->request->get_param('search');
        $status = $this->request->get_param('status');

        // Status
        if ($status === 'any') {
            $query->whereIn('post_status', ['publish', 'draft', 'pending', 'private', 'upgraded']);
        } else {
            $query->where('post_status', $status);
        }

        // Search
        if ($search) {
            if (ctype_digit($search)) {
                $query->where('ID', $search);
            } else {
                $searchTerms = array_map('trim', explode(' ', $search));
                foreach ($searchTerms as $term) {
                    if ($term) {
                        $query->whereLike('post_title', $term);
                    }
                }
            }
        }

        if ($campaignId = $this->request->get_param('campaignId')) {
            $query
                ->join(function (JoinQueryBuilder $builder) {
                    $builder
                        ->leftJoin('give_campaign_forms', 'campaign_forms')
                        ->on('campaign_forms.form_id', 'ID');
                })
                ->where('campaign_forms.campaign_id', $campaignId);
        }

        return $query;
    }
}
