import { __ } from '@wordpress/i18n'
import { updateSnippet } from './requests'
import type { Snippet } from '../../types/Snippet'

/**
 * Update the snippet count of a specific view
 * @param element
 * @param increment
 */
const updateViewCount = (element: HTMLElement | null, increment: boolean) => {
	if (element?.textContent) {
		let count = parseInt(element.textContent.replace(/\((?<count>\d+)\)/, '$1'), 10)
		count += increment ? 1 : -1
		element.textContent = `(${count})`
	} else {
		console.error('Could not update view count.', element)
	}
}

/**
 * Activate an inactive snippet, or deactivate an active snippet
 * @param link
 * @param event
 */
export const toggleSnippetActive = (link: HTMLAnchorElement, event: Event) => {
	const row = link.parentElement?.parentElement // Switch < cell < row
	if (!row) {
		console.error('Could not toggle snippet active status.', row)
		return
	}

	const match = /\b(?:in)?active-snippet\b/.exec(row.className)
	if (!match) {
		return
	}

	event.preventDefault()

	const activating = 'inactive-snippet' === match[0]
	const snippet: Partial<Snippet> = { active: activating }

	updateSnippet('active', row, snippet, response => {
		const button: HTMLAnchorElement | null = row.querySelector('.snippet-activation-switch')

		if (response.success) {
			row.className = activating
				? row.className.replace(/\binactive-snippet\b/, 'active-snippet')
				: row.className.replace(/\bactive-snippet\b/, 'inactive-snippet')

			const views = document.querySelector('.subsubsub')
			const activeCount = views?.querySelector<HTMLElement>('.active .count')
			const inactiveCount = views?.querySelector<HTMLElement>('.inactive .count')

			if (activeCount) {
				updateViewCount(activeCount, activating)
			}

			if (inactiveCount) {
				updateViewCount(inactiveCount, activating)
			}

			if (button) {
				button.title = activating ? __('Deactivate', 'code-snippets') : __('Activate', 'code-snippets')
			}
		} else {
			row.className += ' erroneous-snippet'

			if (button) {
				button.title = __('An error occurred when attempting to activate', 'code-snippets')
			}
		}
	})
}

export const handleSnippetActivationSwitches = () => {
	for (const link of document.getElementsByClassName('snippet-activation-switch')) {
		link.addEventListener('click', event => toggleSnippetActive(<HTMLAnchorElement> link, event))
	}
}
