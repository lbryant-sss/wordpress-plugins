<?php

namespace PublishPress\Future\Modules\Workflows\Rest;

use PublishPress\Future\Core\HookableInterface;
use PublishPress\Future\Modules\Workflows\HooksAbstract;
use WP_Error;
use WP_REST_Server;
use PublishPress\Future\Modules\Workflows\Interfaces\RestApiManagerInterface;
use PublishPress\Future\Modules\Workflows\Models\PostAuthorsModel;
use PublishPress\Future\Modules\Workflows\Models\PostModel;
use PublishPress\Future\Modules\Workflows\Models\WorkflowModel;
use PublishPress\Future\Modules\Workflows\Models\WorkflowsModel;

// TODO: Move this to a controller on the workflows module.
class RestApiV1 implements RestApiManagerInterface
{
    public const ERROR_WORKFLOW_NOT_FOUND = 'publishpressfuture_workflow_not_found';

    public const BASE_PATH = RestApiManager::API_BASE . '/v1';

    public const PERMISSION_READ = 'edit_posts';

    public const PERMISSION_CREATE = 'edit_posts';

    public const PERMISSION_UPDATE = 'edit_posts';

    public const PERMISSION_DELETE = 'edit_posts';

    /**
     * @var HookableInterface
     */
    private HookableInterface $hooks;

    public function __construct(HookableInterface $hooks)
    {
        $this->hooks = $hooks;
    }

    public function register()
    {
        // Read a single workflow
        register_rest_route(
            self::BASE_PATH,
            '/workflows/(?P<id>\d+)',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getWorkflow'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'args' => [
                    'id' => [
                        'description' => __('The ID of the workflow', 'post-expirator'),
                        'type' => 'integer',
                        'required' => true
                    ]
                ],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Get the workflows with manual trigger
        // @since 4.3.0
        register_rest_route(
            self::BASE_PATH,
            '/workflows/with-manual-trigger/(?P<postType>[a-zA-Z0-9_-]+)',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getWorkflowsWithManualTrigger'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'args' => [
                    'postType' => [
                        'description' => __('The post type', 'post-expirator'),
                        'type' => 'string',
                        'required' => true
                    ]
                ],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Create a new workflow
        register_rest_route(
            self::BASE_PATH,
            '/workflows',
            [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'createWorkflow'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Update a workflow
        register_rest_route(
            self::BASE_PATH,
            '/workflows/(?P<id>\d+)',
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'updateWorkflow'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'args' => [
                    'id' => [
                        'description' => __('The ID of the workflow', 'post-expirator'),
                        'type' => 'integer',
                        'required' => true
                    ]
                ],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Delete a workflow
        register_rest_route(
            self::BASE_PATH,
            '/workflows/(?P<id>\d+)',
            [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => [$this, 'deleteWorkflow'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'args' => [
                    'id' => [
                        'description' => __('The ID of the workflow', 'post-expirator'),
                        'type' => 'integer',
                        'required' => true
                    ]
                ],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Get all terms for a taxonomy
        register_rest_route(
            self::BASE_PATH,
            '/terms/(?P<taxonomy>[a-zA-Z0-9_-]+)',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getTaxonomyTerms'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'args' => [
                    'taxonomy' => [
                        'description' => __('The taxonomy name', 'post-expirator'),
                        'type' => 'string',
                        'required' => true
                    ]
                ],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Get the post workflow settings
        register_rest_route(
            self::BASE_PATH,
            '/posts/workflow-settings/(?P<post>\d+)',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getPostWorkflowSettings'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'args' => [
                    'post' => [
                        'description' => __('The post ID', 'post-expirator'),
                        'type' => 'integer',
                        'required' => true
                    ]
                ],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        // Get all authors
        register_rest_route(
            self::BASE_PATH,
            '/authors',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getAuthors'],
                'permission_callback' => [$this, 'checkUserCanCallApi'],
                'show_in_index' => false,
                'show_in_rest' => true,
            ]
        );

        $this->hooks->doAction(HooksAbstract::ACTION_REGISTER_REST_ROUTES);
    }


    private function getWorkflowForResponse(WorkflowModel $workflowModel)
    {
        return [
            'id' => $workflowModel->getId(),
            'title' => $workflowModel->getTitle(),
            'description' => $workflowModel->getDescription(),
            'flow' => $workflowModel->getFlow(),
            'status' => $workflowModel->getStatus(),
            'debugRayShowQueries' => $workflowModel->isDebugRayShowQueriesEnabled(),
            'debugRayShowEmails' => $workflowModel->isDebugRayShowEmailsEnabled(),
            'debugRayShowWordPressErrors' => $workflowModel->isDebugRayShowWordPressErrorsEnabled(),
            'debugRayShowCurrentRunningStep' => $workflowModel->isDebugRayShowCurrentRunningStepEnabled(),
        ];
    }

    public function getWorkflow($request)
    {
        $id = (int) $request['id'];

        $workflowModel = new WorkflowModel();
        $workflowExists = $workflowModel->load($id);

        if (! $workflowExists) {
            return new WP_Error(
                self::ERROR_WORKFLOW_NOT_FOUND,
                __('Workflow not found', 'post-expirator'),
                ['status' => 404]
            );
        }

        return rest_ensure_response($this->getWorkflowForResponse($workflowModel));
    }

    public function createWorkflow($request)
    {
        $workflowModel = new WorkflowModel();
        $workflowModel->createNew();

        return rest_ensure_response($this->getWorkflowForResponse($workflowModel));
    }

    public function updateWorkflow($request)
    {
        $id = (int) $request['id'];

        $workflowModel = new WorkflowModel();
        $workflowExists = $workflowModel->load($id);

        if (! $workflowExists) {
            return new WP_Error(
                self::ERROR_WORKFLOW_NOT_FOUND,
                __('Workflow not found', 'post-expirator'),
                ['status' => 404]
            );
        }

        $isPublishing = $workflowModel->getStatus() !== 'publish' && $request['status'] === 'publish';
        $isUnpublishing = $workflowModel->getStatus() === 'publish' && $request['status'] !== 'publish';

        if (isset($request['title'])) {
            $workflowModel->setTitle($request['title']);
        }

        if (isset($request['description'])) {
            $workflowModel->setDescription($request['description']);
        }

        if (isset($request['flow'])) {
            $workflowModel->setFlow($request['flow']);
        }

        if (isset($request['status'])) {
            $workflowModel->setStatus($request['status']);
        }

        if (isset($request['debugRayShowQueries'])) {
            $workflowModel->setDebugRayShowQueries($request['debugRayShowQueries']);
        }

        if (isset($request['debugRayShowEmails'])) {
            $workflowModel->setDebugRayShowEmails($request['debugRayShowEmails']);
        }

        if (isset($request['debugRayShowWordPressErrors'])) {
            $workflowModel->setDebugRayShowWordPressErrors($request['debugRayShowWordPressErrors']);
        }

        if (isset($request['debugRayShowCurrentRunningStep'])) {
            $workflowModel->setDebugRayShowCurrentRunningStep($request['debugRayShowCurrentRunningStep']);
        }

        if ($isPublishing) {
            $workflowModel->publish();
        } elseif ($isUnpublishing) {
            $workflowModel->unpublish();
        } else {
            $workflowModel->save();
        }

        return rest_ensure_response($this->getWorkflowForResponse($workflowModel));
    }

    public function deleteWorkflow($request)
    {
        $id = (int) $request['id'];

        $workflowModel = new WorkflowModel();
        $workflowExists = $workflowModel->load($id);

        if (! $workflowExists) {
            return new WP_Error(
                self::ERROR_WORKFLOW_NOT_FOUND,
                __('Workflow not found', 'post-expirator'),
                ['status' => 404]
            );
        }

        $workflowModel->delete();

        return rest_ensure_response(true);
    }

    public function getTaxonomyTerms($request)
    {
        $taxonomy = $request['taxonomy'];

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);

        if (is_array($terms) && ! empty($terms)) {
            $terms = array_map(function ($term) {
                return [
                'id' => $term->term_id,
                'name' => $term->name,
                    'slug' => $term->slug,
                ];
            }, $terms);
        } else {
            $terms = [];
        }

        return rest_ensure_response($terms);
    }

    public function getPostWorkflowSettings($request)
    {
        $postIds = explode(',', $request['post']);
        $postIds = array_map('intval', $postIds);

        $postModel = new PostModel();

        foreach ($postIds as $postId) {
            $postModel->load($postId);

            $workflowsWithManualTrigger = $postModel->getValidWorkflowsWithManualTrigger($postId);
            $manuallyEnabledWorkflows = $postModel->getManuallyEnabledWorkflows();
        }

        return rest_ensure_response([
            'workflowsWithManualTrigger' => $workflowsWithManualTrigger,
            'manuallyEnabledWorkflows' => $manuallyEnabledWorkflows,
        ]);
    }

    public function getWorkflowsWithManualTrigger($request)
    {
        $postType = sanitize_text_field($request['postType']);

        $workflowsModel = new WorkflowsModel();
        $workflows = $workflowsModel->getPublishedWorkflowsWithManualTrigger($postType);

        return rest_ensure_response([
            'workflowsWithManualTrigger' => $workflows,
        ]);
    }

    public function checkUserCanCallApi($request)
    {
        return current_user_can(self::PERMISSION_READ);
    }

    public function getAuthors($request)
    {
        $postAuthorsModel = new PostAuthorsModel();
        $authors = $postAuthorsModel->getAuthors();

        $authors = array_map(function ($user) {
            return [
                'id' => $user->ID,
                'userLogin' => $user->user_login,
                'name' => $user->display_name,
                'email' => $user->user_email,
            ];
        }, $authors);

        return rest_ensure_response($authors);
    }
}
