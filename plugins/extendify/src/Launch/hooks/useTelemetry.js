import { useEffect, useRef, useState } from '@wordpress/element';
import { INSIGHTS_HOST } from '@constants';
import { useGlobalStore } from '@launch/state/Global';
import { usePagesStore } from '@launch/state/Pages';
import { usePagesSelectionStore } from '@launch/state/pages-selections';
import { useUserSelectionStore } from '@launch/state/user-selections';

// Dev note: This entire section is opt-in only when partnerID is set as a constant
export const useTelemetry = () => {
	const {
		goals,
		getGoalsPlugins,
		siteStructure,
		variation,
		siteProfile,
		siteObjective,
	} = useUserSelectionStore();
	const { pages: selectedPages, style: selectedStyle } =
		usePagesSelectionStore();
	const selectedPlugins = getGoalsPlugins();
	const { generating } = useGlobalStore();
	const { pages, currentPageIndex, preselectedPages } = usePagesStore();
	const [stepProgress, setStepProgress] = useState([]);
	const [viewedStyles, setViewedStyles] = useState(new Set());
	const running = useRef(false);
	const firstRun = useRef(true);

	useEffect(() => {
		const p = [...pages].map((p) => p[0]);
		// Add pages as they move around
		setStepProgress((progress) => {
			const withoutSkipped = progress.filter((p) => !preselectedPages.has(p));
			// Return early if launched, or on the same page
			if ([p[currentPageIndex], 'launched'].includes(progress?.at(-1))) {
				return withoutSkipped;
			}
			return [...withoutSkipped, p[currentPageIndex]];
		});
	}, [currentPageIndex, pages, preselectedPages]);

	useEffect(() => {
		if (!generating) return;
		// They pressed Launch
		setStepProgress((progress) => [...progress, 'launched']);
	}, [generating]);

	useEffect(() => {
		if (!Object.keys(selectedStyle ?? {})?.length) return;
		// Add selectedStyle to the set
		setViewedStyles((styles) => {
			const newStyles = new Set(styles);
			newStyles.add(selectedStyle);
			return newStyles;
		});
	}, [selectedStyle]);

	useEffect(() => {
		let id = 0;
		let innerId = 0;
		const timeout = firstRun.current
			? // Send the first request immediately,
				0
			: // if on page 0, wait 2s
				currentPageIndex === 0
				? 2000
				: // Every other request will be 1s
					1000;
		firstRun.current = false;
		id = window.setTimeout(() => {
			if (running.current) return;
			running.current = true;
			const controller = new AbortController();
			innerId = window.setTimeout(() => {
				running.current = false;
				controller.abort();
			}, 900);
			fetch(`${INSIGHTS_HOST}/api/v1/launch`, {
				method: 'POST',
				headers: {
					'Content-type': 'application/json',
					Accept: 'application/json',
					'X-Extendify': 'true',
				},
				signal: controller.signal,
				body: JSON.stringify({
					siteType: siteProfile?.aiSiteType,
					siteCategory: siteProfile?.aiSiteCategory,
					siteCreatedAt: window.extSharedData?.siteCreatedAt,
					style: variation?.title,
					siteStructure,
					siteObjective,
					pages: selectedPages?.map((p) => p.slug),
					goals: goals?.map((g) => g.slug),
					lastCompletedStep: stepProgress?.at(-1),
					progress: stepProgress,
					preSelect: [...preselectedPages],
					stylesViewed: [...viewedStyles]
						.filter((s) => s?.variation)
						.map((s) => s.variation.title),
					insightsId: window.extSharedData?.siteId,
					activeTests:
						window.extOnbData?.activeTests?.length > 0
							? JSON.stringify(window.extOnbData?.activeTests)
							: undefined,
					hostPartner: window.extSharedData?.partnerId,
					language: window.extSharedData?.wpLanguage,
					siteURL: window.extSharedData?.homeUrl,
				}),
			})
				.catch(() => undefined)
				.finally(() => {
					running.current = false;
				});
		}, timeout);
		return () => {
			running.current = false;
			[id, innerId].forEach((i) => window.clearTimeout(i));
		};
	}, [
		selectedPages,
		selectedPlugins,
		selectedStyle,
		pages,
		stepProgress,
		viewedStyles,
		currentPageIndex,
		goals,
		siteProfile?.aiSiteType,
		siteProfile?.aiSiteCategory,
		siteStructure,
		siteObjective,
		variation,
		preselectedPages,
	]);
};
