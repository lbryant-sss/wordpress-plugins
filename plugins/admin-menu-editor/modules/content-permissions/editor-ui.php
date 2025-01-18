<?php

namespace YahnisElsts\AdminMenuEditor\ContentPermissions\UserInterface;

use YahnisElsts\AdminMenuEditor\Actors\ActorManager;
use YahnisElsts\AdminMenuEditor\Actors\User;
use YahnisElsts\AdminMenuEditor\ContentPermissions\ContentPermissionsEnforcer;
use YahnisElsts\AdminMenuEditor\ContentPermissions\ContentPermissionsModule;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\Action;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\ActionRegistry;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\ContentItemPolicy;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\PolicyStore;

class ContentPermissionsMetaBox {
	const BOX_ID = 'ame-cpe-content-permissions';
	const BOX_NONCE_NAME = 'ame_cpe_box_nonce';
	const BOX_NONCE_ACTION = 'ame_cpe_box_save_policy';

	const POLICY_FIELD_NAME = 'ame_cpe_policy_data';

	/**
	 * @var PolicyStore
	 */
	private $policyStore;
	/**
	 * @var ContentPermissionsModule
	 */
	private $module;
	/**
	 * @var ActionRegistry
	 */
	private $actionRegistry;
	/**
	 * @var ContentPermissionsEnforcer
	 */
	private $enforcer;
	/**
	 * @var string Full URL to the main plugin settings page.
	 */
	private $settingsPageUrl;
	/**
	 * @var ActorManager
	 */
	private $actorManager;

	public function __construct(
		ActionRegistry             $actionRegistry,
		ActorManager               $actorManager,
		PolicyStore                $policyStore,
		ContentPermissionsModule   $module,
		ContentPermissionsEnforcer $enforcer,
		                           $settingsPageUrl
	) {
		$this->policyStore = $policyStore;
		$this->module = $module;
		$this->actionRegistry = $actionRegistry;
		$this->actorManager = $actorManager;
		$this->enforcer = $enforcer;
		$this->settingsPageUrl = $settingsPageUrl;

		foreach (['load-post.php', 'load-post-new.php'] as $hook) {
			add_action($hook, [$this, 'addBoxHooks']);
		}
	}

	public function addBoxHooks() {
		if ( !is_textdomain_loaded('admin-menu-editor') ) {
			load_plugin_textdomain('admin-menu-editor');
		}

		add_action('add_meta_boxes', [$this, 'addMetaBox'], 8, 2);
		add_action('admin_enqueue_scripts', [$this, 'enqueueMetaBoxDependencies']);
		add_action('save_post', [$this, 'saveMetaBox'], 10, 2);
	}

	/**
	 * @param string $postType
	 * @param \WP_Post $post
	 * @return void
	 * @noinspection PhpUnusedParameterInspection -- Signature required by the add_meta_boxes action.
	 */
	public function addMetaBox($postType, $post = null) {
		if ( $post && !$this->module->userCanEditPolicyForPost($post->ID) ) {
			return;
		}

		add_meta_box(
			self::BOX_ID,
			__('Content Permissions (AME)', 'admin-menu-editor'),
			[$this, 'renderMetaBox'],
			array_values($this->getEnabledPostTypes()),
			'advanced',
			'high'
		);
	}

	public function renderMetaBox($post) {
		if ( !$this->module->userCanEditPolicyForPost($post->ID) ) {
			//This should never happen since the box won't be added if the user doesn't have
			//permission, but let's handle it just in case.
			echo '<p>' . esc_html__(
					'You do not have permission to edit the content permissions for this post.',
					'admin-menu-editor'
				) . '</p>';
			return;
		}

		//Nonce field(s). The referer field may be redundant if another plugin also adds a meta box
		//with its own nonce fields, but we can't easily detect that.
		wp_nonce_field(self::BOX_NONCE_ACTION, self::BOX_NONCE_NAME);

		//Fetch the actions that will be shown in the UI.
		$applicableActions = $this->actionRegistry->getApplicableActions('post', $post);
		//Remove the "publish" action since it's not fully implemented/supported.
		$applicableActions = array_filter($applicableActions, function (Action $action) {
			return $action->getName() !== 'publish';
		});

		//Figure out what capabilities are required for each action. This is used to populate
		//the default permissions for each action (only for presentation purposes).
		$requiredCapabilities = $this->enforcer->runWithoutPolicyEnforcement(
			function () use ($post, $applicableActions) {
				$postStatus = get_post_status_object($post->ID);
				$isDraftLike = !$postStatus || (!$postStatus->public && !$postStatus->private);

				$requiredCapabilities = [];
				foreach ($applicableActions as $action) {
					$metaCap = $action->getPostMetaCap();
					if ( empty($metaCap) ) {
						continue;
					}

					if ( $isDraftLike && ($metaCap === 'read_post') ) {
						//Special case: The trick we use to get required caps through map_meta_cap() doesn't
						//work so well for drafts because the logic in the "read_post" case ends up calling
						//map_meta_cap() recursively with "edit_post" and returns "edit_others_posts" or similar.
						//Instead, let's look at post type capabilities directly.
						$postTypeObject = get_post_type_object($post->post_type);
						if ( $postTypeObject && !empty($postTypeObject->cap->read) ) {
							$requiredCapabilities[$action->getName()] = [$postTypeObject->cap->read];
						}
					} else {
						//Note: Invalid user ID is intentional. We want a "general" mapping, for someone
						//that's not the author of the post or otherwise special. There doesn't seem to be
						//a good way to do that with real users (and there might be no real user that fits
						//those criteria).
						$caps = map_meta_cap($metaCap, 0, $post->ID);
						if ( !empty($caps) ) {
							$requiredCapabilities[$action->getName()] = $caps;
						}
					}
				}
				return $requiredCapabilities;
			}
		);

		//Load the policy for the post.
		$policy = $this->policyStore->getPostPolicy($post->ID);

		/** @noinspection PhpArrayWriteIsNotUsedInspection -- Used in the template and passed to JS. */
		$editorData = [
			'applicableActions'    => $applicableActions,
			'requiredCapabilities' => $requiredCapabilities,
			'policy'               => $policy,
			'enforcementDisabled'  => boolval($this->module->loadSettings()['enforcementDisabled']),
			'adminLikeRoles'       => $this->getAdminLikeRoles(),
		];
		$cpeSettingsUrl = $this->settingsPageUrl . '#ame-content-permissions-section';
		require __DIR__ . '/metabox-template.php';
	}

	private function getEnabledPostTypes() {
		//It may be possible to replace this with a direct call to $this->module->getEnabledPostTypes()
		//if we don't need additional filtering. Consider changing this if users request support for
		//more post types.

		//Also, the "editor" support check might not really be necessary, I don't have a strong reason
		//for it. It's just a heuristic to exclude "weird" post types that might not work well with
		//this feature. (Note that $module->getEnabledPostTypes() currently also has this check.)

		static $enabledPostTypes = null;
		if ( $enabledPostTypes !== null ) {
			return $enabledPostTypes;
		}

		$enabledPostTypes = [];
		foreach (array_keys($this->module->getEnabledPostTypes()) as $postType) {
			if ( post_type_supports($postType, 'editor') ) {
				$enabledPostTypes[$postType] = $postType;
			}
		}

		return $enabledPostTypes;
	}

	public function enqueueMetaBoxDependencies() {
		$enabledPostTypes = $this->getEnabledPostTypes();
		$currentScreen = get_current_screen();
		if (
			$currentScreen
			&& ($currentScreen->base === 'post')
			&& !empty($enabledPostTypes[$currentScreen->post_type])
		) {
			$this->module->enqueuePolicyEditorStyles();

			$uiScript = $this->module->getMetaBoxScript();
			$uiScript->addJsVariable('wsAmeCpeScriptData', [
				'translations' => [
					'tabTitles'         => [
						'basic'      => _x('Basic', 'content permissions tab', 'admin-menu-editor'),
						'advanced'   => _x('Advanced', 'content permissions tab', 'admin-menu-editor'),
						'protection' => _x('Protection', 'content permissions tab', 'admin-menu-editor'),
					],
					'permissionOptions' => [
						'allow'        => _x('Allow', 'content permissions: option label', 'admin-menu-editor'),
						'deny'         => _x('Deny', 'content permissions: option label', 'admin-menu-editor'),
						'default'      => _x('Default', 'content permissions: option label', 'admin-menu-editor'),
						'defaultAllow' => _x('Default: Allow', 'content permissions: option label', 'admin-menu-editor'),
						'defaultDeny'  => _x('Default: Deny', 'content permissions: option label', 'admin-menu-editor'),
					],
					'protectionLabels'  => [
						'replace'      => _x('Show replacement content', 'content permissions: protection type', 'admin-menu-editor'),
						'notFound'     => _x('Show "Not Found" error', 'content permissions: protection type', 'admin-menu-editor'),
						'errorMessage' => _x('Block access', 'content permissions: protection type', 'admin-menu-editor'),
						'redirect'     => _x('Redirect', 'content permissions: protection type', 'admin-menu-editor'),
					],
					'general'           => [
						'noCustomPermissionsReset' => __('No custom permissions to reset.', 'admin-menu-editor'),
					],
				],
			]);
			$uiScript->enqueue();
		}
	}

	/**
	 * @param int $postId
	 * @param \WP_Post $post
	 * @return void
	 */
	public function saveMetaBox($postId, $post = null) {
		//Do nothing if content permissions are not enabled for this post type.
		$enabledPostTypes = $this->getEnabledPostTypes();
		if ( !$post || empty($post->post_type) || empty($enabledPostTypes[$post->post_type]) ) {
			return;
		}

		//Check user permissions.
		if ( !$this->module->userCanEditPolicyForPost($postId) ) {
			return;
		}

		//Check nonce.
		if (
			!isset($_POST[self::BOX_NONCE_NAME])
			//Lots of discussion about if and how nonces should be sanitized, but no clear consensus.
			//See for example https://github.com/WordPress/WordPress-Coding-Standards/issues/869
			//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			|| !wp_verify_nonce(wp_unslash($_POST[self::BOX_NONCE_NAME]), self::BOX_NONCE_ACTION)
		) {
			return;
		}

		if ( empty($_POST[self::POLICY_FIELD_NAME]) ) {
			return;
		}

		//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Custom JSON data.
		$policyJson = wp_unslash((string)$_POST[self::POLICY_FIELD_NAME]);
		$policyData = json_decode($policyJson, true);
		if ( !is_array($policyData) ) {
			return;
		}

		//Sanitize the replacement text and error message.
		if ( !current_user_can('unfiltered_html') ) {
			if ( isset($policyData['replacementContent']) ) {
				$policyData['replacementContent'] = wp_kses_post((string)$policyData['replacementContent']);
			}

			$errorMessagePath = ['accessProtection', 'protections', 'errorMessage', 'errorMessage'];
			$errorMessage = \ameMultiDictionary::get($policyData, $errorMessagePath);
			if ( !empty($errorMessage) ) {
				\ameMultiDictionary::set($policyData, $errorMessagePath, wp_kses_post((string)$errorMessage));
			}
		}

		$policy = ContentItemPolicy::fromArray($policyData);

		//Try to prevent the user from accidentally creating a policy that blocks themselves from viewing
		//or editing the post. If necessary, enable the relevant actions for at least one of the user's roles.
		//This is not perfect as we don't consider potential cascading permissions from parent posts.
		$requiredActions = [
			//"view in lists" controls whether the post is visible in the post list in the dashboard,
			//among other things.
			$this->actionRegistry->getAction(ActionRegistry::ACTION_VIEW_IN_LISTS),
			//"edit" is required to edit the post and the associated policy.
			$this->actionRegistry->getAction(ActionRegistry::ACTION_EDIT),
		];
		$requiredActions = array_filter($requiredActions);
		$currentUser = $this->actorManager->getCurrentUserActor();

		if ( ($currentUser instanceof User) && !empty($requiredActions) ) {
			$userRoles = $currentUser->getRoleIds();

			//Prioritize admin-like roles.
			$adminLikeRoles = $this->getAdminLikeRoles($userRoles);
			$sortedRoleIds = array_merge($adminLikeRoles, array_diff($userRoles, $adminLikeRoles));
			$sortedRoles = [];
			foreach ($sortedRoleIds as $roleId) {
				$sortedRoles[$roleId] = $this->actorManager->getRole($roleId);
			}

			foreach ($requiredActions as $action) {
				$result = $policy->evaluate($currentUser, $action);
				if ( ($result === null) || !$result->isDenied() ) {
					continue; //The policy doesn't block this action, so we're good.
				}

				//Try to find a role that doesn't already have a custom setting for this action.
				//If the user has multiple roles, they might legitimately want to deny the permission
				//for one of the roles, so we'll try not to override that.
				$chosenRole = null;
				foreach ($sortedRoles as $role) {
					if ( !$policy->hasPermissionSettingFor($role, $action) ) {
						$chosenRole = $role;
						break;
					}
				}

				//Otherwise, just pick the first role.
				if ( !$chosenRole ) {
					$chosenRole = reset($sortedRoles);
				}

				if ( $chosenRole ) {
					$policy->setActorPermission($chosenRole, $action, true);
				}
			}
		}

		$this->policyStore->setPostPolicy($postId, $policy);
	}

	/**
	 * Find the roles that are similar to the "administrator" role in terms of capabilities.
	 *
	 * Note: "names" refers to internal role names/slugs, not display names.
	 *
	 * @param string[]|null $roleNames Optional. If specified, only these roles will be considered.
	 * @return string[] List of role names.
	 */
	private function getAdminLikeRoles($roleNames = null) {
		$wpRoles = wp_roles();
		if ( $roleNames === null ) {
			$roleNames = array_keys($wpRoles->role_names);
		}

		//A subset of "sufficiently powerful" administrator capabilities. We'll consider a role
		//to be "admin-like" if it has at least one of these capabilities.
		$adminCapsToCheck = [
			'install_plugins',
			'install_themes',
			'delete_plugins',
			'delete_themes',
			'delete_users',
			'edit_plugins',
			'edit_themes',
			'update_core',
			'update_plugins',
			'update_themes',
			'activate_plugins',
			'switch_themes',
			'manage_options',
		];

		$adminLikeRoles = [];
		foreach ($roleNames as $roleName) {
			$role = $wpRoles->get_role($roleName);
			if ( $role ) {
				foreach ($adminCapsToCheck as $cap) {
					if ( !empty($role->capabilities[$cap]) ) {
						$adminLikeRoles[] = $roleName;
						break;
					}
				}
			}
		}

		//Always include the "administrator" role, if it exists.
		if ( in_array('administrator', $roleNames, true) && !in_array('administrator', $adminLikeRoles, true) ) {
			$adminLikeRoles[] = 'administrator';
		}

		return $adminLikeRoles;
	}
}