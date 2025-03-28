/// <reference path="../../js/jquery.d.ts" />
/// <reference path="../../js/actor-manager.ts" />

declare var wsAmeLodash: _.LoDashStatic;
declare var AmeVisibleUserDialog: any;
//Created using wp_localize_script.
declare var wsAmeActorSelectorData: {
	visibleUsers: string[],
	currentUserLogin: string,

	ajaxUpdateAction: string,
	ajaxUpdateNonce: string,
	adminAjaxUrl: string,
};

interface SelectedActorChangedCallback {
	(newSelectedActor: string | null, oldSelectedActor: string | null): void
}

interface VisibleActorsChangedCallback {
	(newVisibleActors: IAmeActor[]): void;
}

interface SaveVisibleActorAjaxParams {
	ajaxUpdateAction: string,
	ajaxUpdateNonce: string,
	adminAjaxUrl: string,
}

class AmeActorSelectorCore {
	protected static _ = wsAmeLodash;

	public selectedActor: string | null = null;
	public selectedDisplayName: string = 'All';

	private visibleUsers: string[] = [];
	private subscribers: SelectedActorChangedCallback[] = [];
	private visibleActorsSubscribers: VisibleActorsChangedCallback[] = [];

	protected readonly actorManager: AmeActorManagerInterface;
	private readonly currentUserLogin: string;
	protected readonly isProVersion: boolean = false;
	private ajaxParams: SaveVisibleActorAjaxParams;
	protected readonly allOptionEnabled: boolean = true;

	protected cachedVisibleActors: IAmeActor[] | null = null;
	private specialActors: IAmeActor[] = [];

	constructor(
		actorManager: AmeActorManagerInterface,
		isProVersion?: boolean,
		allOptionEnabled: boolean = true,
		private readonly specialActorPosition: number = -1
	) {
		this.actorManager = actorManager;

		if (typeof isProVersion !== 'undefined') {
			this.isProVersion = isProVersion;
		}
		this.allOptionEnabled = allOptionEnabled;

		this.currentUserLogin = wsAmeActorSelectorData.currentUserLogin;
		this.visibleUsers = wsAmeActorSelectorData.visibleUsers;
		this.ajaxParams = wsAmeActorSelectorData;

		//Discard any users that don't exist / were not loaded by the actor manager.
		const _ = AmeActorSelector._;
		this.visibleUsers = _.intersection(this.visibleUsers, _.keys(actorManager.getUsers()));
	}

	setSelectedActor(actorId: string | null) {
		if ((actorId !== null) && !this.actorManager.actorExists(actorId)) {
			return;
		}

		const previousSelection = this.selectedActor;
		this.selectedActor = actorId;

		if (actorId !== null) {
			const actor = this.actorManager.getActor(actorId);
			if (actor !== null) {
				this.selectedDisplayName = actor.getDisplayName();
			} else {
				this.selectedDisplayName = '[' + actorId + ']';
			}
		} else {
			this.selectedDisplayName = 'All';
		}

		//Notify subscribers that the selection has changed.
		if (this.selectedActor !== previousSelection) {
			for (let i = 0; i < this.subscribers.length; i++) {
				this.subscribers[i](this.selectedActor, previousSelection);
			}
		}
	}

	onChange(callback: SelectedActorChangedCallback) {
		this.subscribers.push(callback);
	}

	getVisibleActors(): IAmeActor[] {
		if (this.cachedVisibleActors) {
			return this.cachedVisibleActors;
		}

		const _ = AmeActorSelector._;
		let actors: IAmeActor[] = [];

		//Include all roles.
		//Idea: Sort roles either alphabetically or by typical privilege level (admin, editor, author, ...).
		_.forEach(this.actorManager.getRoles(), function (role: IAmeActor) {
			actors.push(role);
		});

		//Sort the default roles in a fixed order, the rest alphabetically.
		const defaultRoleOrder: Record<string, number> = {
			'role:administrator': 1,
			'role:editor': 2,
			'role:author': 3,
			'role:contributor': 4,
			'role:subscriber': 5
		};
		actors.sort(function (a, b) {
			const aId = a.getId();
			const bId = b.getId();
			if (defaultRoleOrder.hasOwnProperty(aId) && defaultRoleOrder.hasOwnProperty(bId)) {
				return defaultRoleOrder[aId] - defaultRoleOrder[bId];
			} else if (defaultRoleOrder.hasOwnProperty(aId)) {
				return -1;
			} else if (defaultRoleOrder.hasOwnProperty(bId)) {
				return 1;
			} else {
				return a.getDisplayName().localeCompare(b.getDisplayName());
			}
		});

		//Include the Super Admin (multisite only).
		const user = this.actorManager.getUser(this.currentUserLogin);
		if (user && user.isSuperAdmin) {
			actors.push(this.actorManager.getSuperAdmin());
		}

		//Include special actors, if any. Note that these must also be added to the actor manager;
		//the actor selector doesn't do that automatically.
		if (this.specialActorPosition < 0) {
			//Add them at the beginning.
			actors.unshift(...this.specialActors);
		} else {
			//Add them near the end, before individual users.
			actors.push(...this.specialActors);
		}

		//Include the current user.
		const currentUser = this.actorManager.getUser(this.currentUserLogin);
		if (currentUser) {
			actors.push(currentUser);
		}

		//Include other visible users.
		_(this.visibleUsers)
			.without(this.currentUserLogin)
			.sortBy()
			.forEach((login) => {
				const user = this.actorManager.getUser(login);
				if (user) {
					actors.push(user);
				}
			});

		this.cachedVisibleActors = actors;
		return actors;
	}

	protected refreshVisibleActors() {
		this.cachedVisibleActors = null;
		const newVisibleActors = this.getVisibleActors();
		for (let i = 0; i < this.visibleActorsSubscribers.length; i++) {
			this.visibleActorsSubscribers[i](newVisibleActors);
		}
	}

	onVisibleActorsChanged(callback: VisibleActorsChangedCallback) {
		this.visibleActorsSubscribers.push(callback);
	}

	addSpecialActor(actor: IAmeActor) {
		this.specialActors.push(actor);
	}

	private saveVisibleUsers() {
		jQuery.post(
			this.ajaxParams.adminAjaxUrl,
			{
				'action': this.ajaxParams.ajaxUpdateAction,
				'_ajax_nonce': this.ajaxParams.ajaxUpdateNonce,
				'visible_users': JSON.stringify(this.visibleUsers)
			}
		);
	}

	getCurrentUserActor(): IAmeUser | null {
		return this.actorManager.getUser(this.currentUserLogin);
	}

	getNiceName(actor: IAmeActor): string {
		let name = actor.getDisplayName();
		if (actor.hasOwnProperty('userLogin')) {
			const user = actor as IAmeUser;
			if (user.userLogin === this.currentUserLogin) {
				name = 'Current user (' + user.userLogin + ')';
			} else {
				name = user.getDisplayName() + ' (' + user.userLogin + ')';
			}
		}
		return name;
	}

	/**
	 * Wrap the selected actor ID in a computed observable so that it can be used with Knockout.
	 * @param ko
	 */
	createKnockoutObservable(ko: KnockoutStatic): KnockoutComputed<string | null> {
		const internalObservable = ko.observable(this.selectedActor);
		const publicObservable = ko.computed({
			read: function () {
				return internalObservable();
			},
			write: (newActor: string) => {
				this.setSelectedActor(newActor);
			}
		});
		this.onChange((newSelectedActor: string | null) => {
			internalObservable(newSelectedActor);
		});
		return publicObservable;
	}

	createIdObservable(ko: KnockoutStatic): KnockoutComputed<string | null> {
		return this.createKnockoutObservable(ko);
	}

	createActorObservable(ko: KnockoutStatic): KnockoutComputed<IAmeActor | null> {
		const internalObservable = ko.observable(
			(this.selectedActor === null) ? null : this.actorManager.getActor(this.selectedActor)
		);
		const publicObservable = ko.computed<IAmeActor | null>({
			read: function () {
				return internalObservable();
			},
			write: (newActor: IAmeActor | null) => {
				this.setSelectedActor(
					(newActor !== null) ? newActor.getId() : null
				);
			}
		});

		const self = this;
		this.onChange(function (newSelectedActor: string | null) {
			if (newSelectedActor === null) {
				internalObservable(null);
			} else {
				internalObservable(self.actorManager.getActor(newSelectedActor));
			}
		});
		return publicObservable;
	}

	/**
	 * Select an actor based on the "selected_actor" URL parameter.
	 *
	 * Does nothing if the parameter is not set or the actor ID is invalid.
	 */
	setSelectedActorFromUrl() {
		if (!URLSearchParams) {
			return;
		}

		//Select an actor based on the "selected_actor" URL parameter.
		const urlParams = new URLSearchParams(window.location.search);
		const selectedActor = urlParams.get('selected_actor');
		if (selectedActor !== null) {
			this.setSelectedActor(selectedActor);
		}
	}

	openVisibleUsersDialog() {
		if (!AmeVisibleUserDialog) {
			return;
		}

		AmeVisibleUserDialog.open({
			currentUserLogin: this.currentUserLogin,
			users: this.actorManager.getUsers(),
			visibleUsers: this.visibleUsers,
			actorManager: this.actorManager,

			save: (userDetails: IAmeUser[], selectedUsers: string[]) => {
				this.actorManager.addUsers(userDetails);
				this.visibleUsers = selectedUsers;
				//The user list has changed, so update the actor list.
				this.refreshVisibleActors();

				//Save the user list via AJAX.
				this.saveVisibleUsers();
			}
		});
	}
}

class AmeActorSelector extends AmeActorSelectorCore {

	private selectorNode: JQuery | null = null;
	private isDomInitStarted: boolean = false;

	constructor(
		actorManager: AmeActorManagerInterface,
		isProVersion?: boolean,
		allOptionEnabled: boolean = true,
		specialActorPosition: number = -1
	) {
		super(actorManager, isProVersion, allOptionEnabled, specialActorPosition);

		this.onVisibleActorsChanged(() => {
			this.populateActorSelector();
		});
		this.onChange(() => {
			this.highlightSelectedActor();
		});

		jQuery(() => {
			this.initDOM();
		});
	}

	private initDOM() {
		if (this.isDomInitStarted) {
			return;
		}
		this.isDomInitStarted = true;

		this.selectorNode = jQuery('#ws_actor_selector');
		this.populateActorSelector();

		//Don't show the selector in the free version.
		if (!this.isProVersion) {
			this.selectorNode.hide();
			return;
		}

		//Select an actor on click.
		this.selectorNode.on('click', 'li a.ws_actor_option', (event) => {
			const href = jQuery(event.target).attr('href');
			const fragmentStart = href.indexOf('#');

			let actor = null;
			if (fragmentStart >= 0) {
				actor = href.substring(fragmentStart + 1);
			}
			if (actor === '') {
				actor = null;
			}

			this.setSelectedActor(actor);
			event.preventDefault();
		});

		//Display the user selection dialog when the user clicks "Choose users".
		this.selectorNode.on('click', '#ws_show_more_users', (event) => {
			event.preventDefault();
			this.openVisibleUsersDialog();
		});
	}

	private highlightSelectedActor() {
		//Set up and populate the selector element if we haven't done that yet.
		if (!this.isDomInitStarted) {
			this.initDOM();
		}
		if (this.selectorNode === null) {
			return; //Should never happen since initDOM() should have set this.
		}

		//Deselect the previous item.
		this.selectorNode.find('.current').removeClass('current');

		//Select the new one or "All".
		let selector;
		if (this.selectedActor === null) {
			selector = 'a.ws_no_actor';
		} else {
			selector = 'a[href$="#' + this.selectedActor + '"]';
		}
		this.selectorNode.find(selector).addClass('current');
	}

	private populateActorSelector() {
		if (this.selectorNode === null) {
			return; //Not initialized yet.
		}

		const actorSelector = this.selectorNode,
			$ = jQuery;
		let isSelectedActorVisible = false;

		//Build the list of available actors.
		actorSelector.empty();
		if (this.allOptionEnabled) {
			actorSelector.append('<li><a href="#" class="current ws_actor_option ws_no_actor" data-text="All">All</a></li>');
		}

		const visibleActors = this.getVisibleActors();
		for (let i = 0; i < visibleActors.length; i++) {
			const actor = visibleActors[i],
				name = this.getNiceName(actor);

			actorSelector.append(
				$('<li></li>').append(
					$('<a></a>')
						.attr('href', '#' + actor.getId())
						.attr('data-text', name)
						.text(name)
						.addClass('ws_actor_option')
				)
			);
			isSelectedActorVisible = (actor.getId() === this.selectedActor) || isSelectedActorVisible;
		}

		if (this.isProVersion) {
			const moreUsersText = 'Choose users\u2026';
			actorSelector.append(
				$('<li>').append(
					$('<a></a>')
						.attr('id', 'ws_show_more_users')
						.attr('href', '#more-users')
						.attr('data-text', moreUsersText)
						.text(moreUsersText)
				)
			);
		}

		if (this.isProVersion) {
			actorSelector.show();
		}

		//If the selected actor is no longer on the list, select the first available option instead.
		if ((this.selectedActor !== null) && !isSelectedActorVisible) {
			if (this.allOptionEnabled) {
				this.setSelectedActor(null);
			} else {
				const availableActors = this.getVisibleActors();
				const firstActor = AmeActorSelector._.head(availableActors);
				if (firstActor) {
					this.setSelectedActor(firstActor.getId());
				} else {
					this.setSelectedActor(null);
				}
			}
		}

		this.highlightSelectedActor();
	}

	repopulate() {
		this.refreshVisibleActors();
		//This will clear the cache and indirectly trigger a call to populateActorSelector().
	}
}