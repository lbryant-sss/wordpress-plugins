import './style.scss';
import './live-reload-conditions';

import classnames from 'classnames/dedupe';
import iframeResizer from 'iframe-resizer/js/iframeResizer';
import $ from 'jquery';
import { isEqual, uniq } from 'lodash';
import rafSchd from 'raf-schd';
import { debounce, throttle } from 'throttle-debounce';

import { Spinner } from '@wordpress/components';
import { dispatch, select, subscribe, withSelect } from '@wordpress/data';
import { Component, createRef, Fragment } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

import getDynamicCSS, { hasDynamicCSS } from '../../utils/controls-dynamic-css';
import { convertModernToLegacy } from '../../utils/convert-legacy-attributes';

const {
	VPAdminGutenbergVariables: variables,
	VPGutenbergVariables: { controls: registeredControls },
} = window;

let uniqueIdCount = 1;

function getUpdatedKeys(oldData, newData) {
	const keys = uniq([...Object.keys(oldData), ...Object.keys(newData)]);
	const changedKeys = [];

	keys.forEach((k) => {
		if (!isEqual(oldData[k], newData[k])) {
			changedKeys.push(k);
		}
	});

	return changedKeys;
}

/**
 * Component Class
 */
class IframePreview extends Component {
	constructor(...args) {
		super(...args);

		this.state = {
			loading: true,
			uniqueId: `vpf-preview-${uniqueIdCount}`,
			currentIframeHeight: 0,
			latestIframeHeight: 0,
			blockPosition: null,
		};

		uniqueIdCount += 1;

		this.frameRef = createRef();
		this.formRef = createRef();

		this.maybePreviewTypeChanged = this.maybePreviewTypeChanged.bind(this);
		this.maybeAttributesChanged = this.maybeAttributesChanged.bind(this);
		this.onFrameLoad = this.onFrameLoad.bind(this);
		this.maybeReload = this.maybeReload.bind(this);
		this.maybeReloadDebounce = debounce(
			300,
			rafSchd(this.maybeReload.bind(this))
		);
		this.maybeResizePreviews = this.maybeResizePreviews.bind(this);
		this.maybeResizePreviewsThrottle = throttle(
			100,
			rafSchd(this.maybeResizePreviews)
		);
		this.updateIframeHeight = this.updateIframeHeight.bind(this);

		this.updateIframeHeightThrottle = throttle(
			100,
			rafSchd(this.updateIframeHeight)
		);
		this.printInput = this.printInput.bind(this);

		this.trackBlockPosition = this.trackBlockPosition.bind(this);
	}

	componentDidMount() {
		const self = this;

		const { clientId } = self.props;

		// Set initial block position
		this.setState({
			blockPosition: this.getBlockPosition(clientId),
		});

		// Subscribe to block position changes
		this.unsubscribe = subscribe(() => {
			this.trackBlockPosition(clientId);
		});

		iframeResizer(
			{
				interval: 10,
				warningTimeout: 60000,
				checkOrigin: false,
				onMessage({ message }) {
					// select current block on click message.
					if (message === 'clicked') {
						dispatch('core/block-editor').selectBlock(clientId);

						window.focus();
					}
				},
				onResized({ height }) {
					self.updateIframeHeightThrottle(`${height}px`);
				},
			},
			self.frameRef.current
		);

		self.frameRef.current.addEventListener('load', self.onFrameLoad);
		window.addEventListener('resize', self.maybeResizePreviewsThrottle);

		self.maybeReload();
	}

	componentDidUpdate(prevProps) {
		this.maybePreviewTypeChanged(prevProps);
		this.maybeAttributesChanged(prevProps);
	}

	componentWillUnmount() {
		// Unsubscribe from block position tracking
		if (this.unsubscribe) {
			this.unsubscribe();
		}

		this.frameRef.current.removeEventListener('load', this.onFrameLoad);
		window.removeEventListener('resize', this.maybeResizePreviewsThrottle);

		if (this.frameRef.current.iframeResizer) {
			this.frameRef.current.iframeResizer.close();
			this.frameRef.current.iframeResizer.removeListeners();
		}
	}

	/**
	 * On frame load event.
	 *
	 * @param {Object} e - event data.
	 */
	onFrameLoad(e) {
		this.frameWindow = e.target.contentWindow;
		this.frameJQuery = e.target.contentWindow.jQuery;

		if (this.frameJQuery) {
			this.$framePortfolio = this.frameJQuery('.vp-portfolio');

			this.maybeResizePreviews();

			if (this.frameTimeout) {
				clearTimeout(this.frameTimeout);
			}

			// We need this timeout, since we resize iframe size and layouts resized with transitions.
			this.frameTimeout = setTimeout(() => {
				this.setState({
					loading: false,
				});
			}, 300);
		}
	}

	maybePreviewTypeChanged(prevProps) {
		if (prevProps.previewDeviceType === this.props.previewDeviceType) {
			return;
		}

		this.maybeResizePreviews();
	}

	maybeAttributesChanged(prevProps) {
		if (this.busyReload) {
			return;
		}
		this.busyReload = true;

		const frame = this.frameRef.current;

		const newContext = this.props.context
			? Object.fromEntries(
					Object.entries(this.props.context).map(([key, value]) => [
						key.replace('vp/', ''),
						value,
					])
				)
			: {};
		const oldContext = prevProps.context
			? Object.fromEntries(
					Object.entries(prevProps.context).map(([key, value]) => [
						key.replace('vp/', ''),
						value,
					])
				)
			: {};

		const newAttributes = {
			...this.props.attributes,
			...convertModernToLegacy(newContext),
		};

		const oldAttributes = {
			...prevProps.attributes,
			...convertModernToLegacy(oldContext),
		};

		const changedAttributes = {};
		const changedAttributeKeys = getUpdatedKeys(
			oldAttributes,
			newAttributes
		);

		// check changed attributes
		changedAttributeKeys.forEach((name) => {
			if (typeof newAttributes[name] !== 'undefined') {
				changedAttributes[name] = newAttributes[name];
			}
		});

		if (Object.keys(changedAttributes).length) {
			let reload = false;

			Object.keys(changedAttributes).forEach((name) => {
				// Don't reload if block has dynamic styles.
				const hasStyles = hasDynamicCSS(name);

				// Don't reload if reloading disabled in control attributes.
				const hasReloadAttribute =
					registeredControls[name] &&
					registeredControls[name].reload_iframe;

				reload = reload || (!hasStyles && hasReloadAttribute);
			});

			const data = applyFilters('vpf.editor.changed-attributes', {
				attributes: changedAttributes,
				reload,
				$frame: this.frameRef.current,
				frameWindow: this.frameWindow,
				frameJQuery: this.frameJQuery,
				$framePortfolio: this.$framePortfolio,
			});

			if (!data.reload) {
				// Update AJAX dynamic data.
				if (data.frameWindow && data.frameWindow.vp_preview_post_data) {
					data.frameWindow.vp_preview_post_data[data.name] =
						data.value;
				}

				// Insert dynamic CSS.
				if (frame.iFrameResizer && newAttributes.block_id) {
					frame.iFrameResizer.sendMessage({
						name: 'dynamic-css',
						blockId: newAttributes.block_id,
						styles: getDynamicCSS(newAttributes),
					});
				}
			}

			if (data.reload) {
				this.maybeReloadDebounce();
			}
			this.busyReload = false;
		} else {
			this.busyReload = false;
		}
	}

	// Add new methods to track block position
	getBlockPosition(clientId) {
		const { getBlockIndex, getBlockRootClientId } =
			select('core/block-editor');
		const rootClientId = getBlockRootClientId(clientId);
		return getBlockIndex(clientId, rootClientId);
	}

	trackBlockPosition(clientId) {
		const newPosition = this.getBlockPosition(clientId);

		if (this.state.blockPosition !== newPosition) {
			this.setState(
				{
					blockPosition: newPosition,
					loading: true,
				},
				() => {
					// Reload the iframe with a slight delay to ensure DOM is updated
					setTimeout(() => {
						this.maybeReload();
					}, 100);
				}
			);
		}
	}

	maybeReload() {
		let latestIframeHeight = 0;

		if (this.frameRef.current) {
			latestIframeHeight = this.state.currentIframeHeight;
		}

		this.setState(
			{
				loading: true,
				latestIframeHeight,
			},
			() => {
				if (this.formRef.current) {
					this.formRef.current.submit();
				}
			}
		);
	}

	/**
	 * Resize frame to properly work with @media.
	 */
	maybeResizePreviews() {
		const contentWidth = $(
			'.editor-styles-wrapper, .edit-post-visual-editor__content-area'
		)
			.eq(0)
			.width();

		if (!contentWidth || !this.frameRef.current) {
			return;
		}

		const frame = this.frameRef.current;
		const $frame = $(frame);
		const parentWidth = $frame
			.closest('.visual-portfolio-gutenberg-preview')
			.width();

		$frame.css({
			width: contentWidth,
		});

		if (frame.iFrameResizer) {
			frame.iFrameResizer.sendMessage({
				name: 'resize',
				width: parentWidth,
			});
			frame.iFrameResizer.resize();
		}
	}

	/**
	 * Update iframe height.
	 *
	 * @param newHeight
	 */
	updateIframeHeight(newHeight) {
		this.setState({
			currentIframeHeight: newHeight,
		});
	}

	/**
	 * Prepare form input for POST variables.
	 *
	 * @param {string} name - option name.
	 * @param {Mixed}  val  - option value.
	 *
	 * @return {JSX} - form control.
	 */
	printInput(name, val) {
		const params = {
			type: 'text',
			name,
			value: val,
			readOnly: true,
		};

		if (typeof val === 'number') {
			params.type = 'number';
		} else if (typeof val === 'boolean') {
			params.type = 'number';
			params.value = val ? 1 : 0;
		} else if (typeof val === 'object' && val !== null) {
			return (
				<>
					{Object.keys(val).map((i) => (
						<Fragment key={`${name}[${i}]`}>
							{this.printInput(`${name}[${i}]`, val[i])}
						</Fragment>
					))}
				</>
			);
		} else {
			params.value = params.value || '';
		}

		return <input {...params} />;
	}

	render() {
		const { postType, postId, context } = this.props;

		const { loading, uniqueId, currentIframeHeight, latestIframeHeight } =
			this.state;

		// Collect all data into a single object, prioritizing context values
		const formData = {};
		let contextAttributes = {};

		if (context) {
			// Then override with context values (they take priority)
			Object.entries(context).forEach(([key, value]) => {
				if (key.startsWith('vp/')) {
					const formKey = `vp_${key.replace('vp/', '')}`;
					formData[formKey] = value;
				}
			});

			contextAttributes = Object.fromEntries(
				Object.entries(this.props.context).map(([key, value]) => [
					key.replace('vp/', ''),
					value,
				])
			);
		}

		const attributes = {
			...this.props.attributes,
			...convertModernToLegacy(contextAttributes),
		};

		const { id, content_source: contentSource } = attributes;

		// Convert attributes for form submission.
		Object.keys(attributes).forEach((key) => {
			formData[`vp_${key}`] = attributes[key];
		});

		return (
			<div
				className={classnames(
					'visual-portfolio-gutenberg-preview',
					loading ? 'visual-portfolio-gutenberg-preview-loading' : ''
				)}
				style={{
					height: loading ? latestIframeHeight : currentIframeHeight,
				}}
			>
				<div className="visual-portfolio-gutenberg-preview-inner">
					<form
						action={variables.preview_url}
						target={uniqueId}
						method="POST"
						style={{ display: 'none' }}
						ref={this.formRef}
					>
						<input
							type="hidden"
							name="vp_preview_frame"
							value="true"
							readOnly
						/>
						<input
							type="hidden"
							name="vp_preview_type"
							value="gutenberg"
							readOnly
						/>
						<input
							type="hidden"
							name="vp_preview_post_type"
							value={postType}
							readOnly
						/>
						<input
							type="hidden"
							name="vp_preview_post_id"
							value={postId}
							readOnly
						/>
						<input
							type="hidden"
							name="vp_preview_nonce"
							value={variables.nonce}
							readOnly
						/>

						{contentSource === 'saved' ? (
							<input
								type="text"
								name="vp_id"
								value={id}
								readOnly
							/>
						) : (
							<>
								{Object.entries(formData).map(
									([key, value]) => (
										<Fragment key={key}>
											{this.printInput(key, value)}
										</Fragment>
									)
								)}
							</>
						)}
					</form>
					<iframe
						title="vp-preview"
						id={uniqueId}
						name={uniqueId}
						// eslint-disable-next-line react/no-unknown-property
						allowtransparency="true"
						ref={this.frameRef}
					/>
				</div>
				{loading ? <Spinner /> : ''}
			</div>
		);
	}
}

export default withSelect((selectEditor) => {
	const {
		getDeviceType,
		getCurrentPost,
		getBlockIndex,
		getBlockRootClientId,
	} = selectEditor('core/editor') || {};
	return {
		previewDeviceType: getDeviceType ? getDeviceType() : 'desktop',
		postType: getCurrentPost ? getCurrentPost().type : 'standard',
		postId: getCurrentPost ? getCurrentPost().id : 'widgets',
		getBlockIndex,
		getBlockRootClientId,
	};
})(IframePreview);
