<?php

namespace Give\DonationForms\V2\Endpoints;

use Give\DonationForms\V2\ListTable\DonationFormsListTable;
use Give\Framework\Database\DB;
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

        $forms = $this->getForms();
        $totalForms = $this->getTotalFormsCount();
        $totalPages = (int)ceil($totalForms / $this->request->get_param('perPage'));

        if ('model' === $this->request->get_param('return')) {
            $items = $forms;
        } else {
            $this->listTable->items($forms, $this->request->get_param('locale') ?? '');
            $items = $this->listTable->getItems();

            foreach ($items as $i => &$item) {
                $item['name'] = get_the_title($item['id']);
                $item['edit'] = add_query_arg(['locale' => Language::getLocale()],
                    get_edit_post_link($item['id'], 'edit'));
                $item['permalink'] = get_permalink($item['id']);
                $item['v3form'] = (bool)give_get_meta($item['id'], 'formBuilderSettings');
                $item['status_raw'] = $forms[$i]->status->getValue();
            }
        }

        return new WP_REST_Response(
            [
                'items' => $items,
                'totalItems' => $totalForms,
                'totalPages' => $totalPages,
                'trash' => defined('EMPTY_TRASH_DAYS') && EMPTY_TRASH_DAYS > 0,
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
        $sortDirection = $this->request->get_param('sortDirection') ?: 'desc';

        $query = give()->donationForms->prepareQuery();
        $query = $this->getWhereConditions($query);

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
     * @since 2.24.0
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

        return $query;
    }
}
