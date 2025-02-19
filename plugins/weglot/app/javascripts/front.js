//find and place wg-ajax-button-switcher

function switcherPlacement() {
	const buttons = document.querySelectorAll(".weglot-custom-switcher-ajax");

	buttons.forEach(button => {
		const targetSelector = button.getAttribute('data-wg-target');
		const siblingSelector = button.getAttribute('data-wg-sibling');
		const target = targetSelector ? document.querySelector(targetSelector) : null;
		const sibling = siblingSelector ? document.querySelector(siblingSelector) : null;

		if (target && sibling) {
			target.insertBefore(button, sibling);
		} else if (target) {
			target.insertBefore(button, target.firstChild);
		} else if (sibling) {
			sibling.parentNode.insertBefore(button, sibling);
		}else{
			button.classList.add("weglot-default", "weglot-invert");
		}

		button.classList.remove("weglot-custom-switcher-ajax");
	});
}

//detect iframe
function inFrame() {
	try {
		return window.frameElement || window.self !== window.top;
	} catch (_) {
		return false;
	}
}

setTimeout(() => {
	if (document.readyState === "loading") {
		document.addEventListener( "DOMContentLoaded", () => switcherPlacement() );
	} else {
		switcherPlacement();
	}
}, 1500);

window.WeglotSwitcherControl = {
	hideSwitchers: true, // Default behavior to hide switchers
	updateSwitchers: function () {
		const switchers = document.querySelectorAll('.weglot-dropdown');
		if (switchers !== null) {
			[].forEach.call(switchers, function (switcher) {
				switcher.style.display = window.WeglotSwitcherControl.hideSwitchers ? "none" : "";
			});
		}
	}
};

document.addEventListener("WeglotSwitcherUpdate", function () {
	window.WeglotSwitcherControl.updateSwitchers();
});


document.addEventListener( "DOMContentLoaded", function ( event ) {

	function getOffset(element) {
		let top = 0, left = 0;
		do {
			top += element.offsetTop || 0;
			left += element.offsetLeft || 0;
			element = element.offsetParent;
		} while (element);

		return {
			top: top, left: left
		};
	}

	const button = document.querySelector(".country-selector");
	if (!button) {
		return;
	}
	const h = getOffset(button).top;
	const body = document.body, html = document.documentElement;
	const page_height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);

	const position = window.getComputedStyle(button).getPropertyValue("position");
	const bottom = window.getComputedStyle(button).getPropertyValue("bottom");
	const top = window.getComputedStyle(button).getPropertyValue("top");

	if ((position !== "fixed" && h > page_height / 2) || (position === "fixed" && h > 100)) {
		button.className += " weglot-invert";
	}

	if (inFrame()) {
		// Initial update based on the default setting
		window.WeglotSwitcherControl.updateSwitchers();
	}

	document.addEventListener("click", (evt) => {
		let targetEl = evt.target;

		if(targetEl.closest('.country-selector.close_outside_click') == null){
			document.querySelectorAll(".country-selector.close_outside_click.weglot-dropdown input").forEach( (node) => {
				node.checked = false
			})
		}
	});

	const asides = document.getElementsByClassName("country-selector");
	const isOpen = link => !link.className.includes("closed");
	let focusedLang;
	if (asides.length > 0) {
		const selectedLang = document.getElementsByClassName("wgcurrent");
		for (let aside of asides) {

			// accessiblity button
			const KEYCODE = {
				ENTER: 13, ESCAPE: 27, ARROWUP: 38, ARROWDOWN: 40,
			};

			const isOpenUp = () => {
				// If switcher is in second half of page, set weg-openup class
				const {bottom = 0} = aside.getBoundingClientRect();
				return bottom > window.innerHeight / 2;
			};

			const openSwitcher = () => {
				aside.classList.remove("closed");
				document.querySelectorAll(".country-selector.weglot-dropdown input").checked = true;
				aside.setAttribute("aria-expanded", "true");
			};

			const closeSwitcher = () => {
				aside.classList.add("closed");
				document.querySelectorAll(".country-selector.weglot-dropdown input").checked = false
				aside.setAttribute("aria-expanded", "false");
				if (focusedLang) {
					focusedLang.classList.remove("focus");
					focusedLang = null;
				}
			};

			const setAriaLabel = code => {
				const fullNameLang = getLangNameFromCode(code);
				//aside.setAttribute("aria-activedescendant", "weglot-language-" + code);
				aside.setAttribute("aria-label", "Language selected: " + code);
			};

			const toggleSwitcher = () => {
				if (aside.classList.contains("closed")) {
					openSwitcher();
				} else {
					closeSwitcher();
				}
				if (focusedLang) {
					focusedLang.classList.remove("focus");
				}
				focusedLang = null;
			};

			// Toggle when focused and keydown ENTER.
			aside.addEventListener("keydown", event => {
				if (event.keyCode === KEYCODE.ENTER) {
					//event.preventDefault();
					//selectedLang.click();
					for (var i = 0; i < selectedLang.length; i++) {
						selectedLang[i].click();
					}
					if (focusedLang) {
						const destinationLanguage = focusedLang.getAttribute("data-l");
						setAriaLabel(destinationLanguage);
						aside.focus();
					}
					toggleSwitcher();
					return;
				}
				if (event.keyCode === KEYCODE.ARROWDOWN || event.keyCode === KEYCODE.ARROWUP) {
					event.preventDefault();
					moveFocus(event.keyCode);
					return;
				}
				if (event.keyCode === KEYCODE.ESCAPE && isOpen(aside)) {
					// Close switcher.
					event.preventDefault();
					closeSwitcher();
					aside.focus();
				}
			});

			function handleMouseDown(event) {
				if (focusedLang) {
					const destinationLanguage = focusedLang.getAttribute("data-l");
					setAriaLabel(destinationLanguage);
					aside.focus();
				}
				toggleSwitcher();
				return;
			}

			aside.addEventListener("mousedown", handleMouseDown);

			if(aside.className.includes("open_hover")){
				aside.addEventListener("mouseenter", event => {
					if (focusedLang) {
						const destinationLanguage = focusedLang.getAttribute("data-l");
						setAriaLabel(destinationLanguage);
						aside.focus();
					}
					toggleSwitcher();
					aside.querySelector("input.weglot_choice").checked = true
					return;
				});

				aside.addEventListener("mouseleave", event => {
					if (focusedLang) {
						const destinationLanguage = focusedLang.getAttribute("data-l");
						setAriaLabel(destinationLanguage);
						aside.focus();
					}
					toggleSwitcher();
					aside.querySelector("input.weglot_choice").checked = false
					return;
				});
			}


			const moveFocus = keyCode => {
				const direction = keyCode === KEYCODE.ARROWDOWN ? "nextSibling" : "previousSibling";
				const openUp = isOpenUp();

				if (!focusedLang || !isOpen(aside)) {
					// Focus the first or last language
					const selector = openUp ? "ul li.wg-li:last-child" : "ul li.wg-li";

					for (var i = 0; i < selectedLang.length; i++) {
						//selectedLang[i].click();
						focusedLang = selectedLang[i].parentNode.querySelector(selector);
					}

					if (!focusedLang) {
						return;
					}
					focusedLang.classList.add("focus");
					focusedLang.childNodes[0].focus();
					focusedLang.scrollIntoView({block: "center"});

					// if right direction, open it
					const needToOpen = (keyCode === KEYCODE.ARROWUP && openUp) || (keyCode === KEYCODE.ARROWDOWN && !openUp);
					if (!isOpen(aside) && needToOpen) {
						openSwitcher();
					}
					return;
				}

				// Focus next or prev language, if exists
				if (!focusedLang[direction]) {
					// if last element, close it
					if ((keyCode === KEYCODE.ARROWUP && !openUp) || (keyCode === KEYCODE.ARROWDOWN && openUp)) {
						closeSwitcher();
						aside.focus();
					}
					return;
				}

				focusedLang.classList.remove("focus");
				focusedLang = focusedLang[direction];
				focusedLang.classList.add("focus");
				focusedLang.childNodes[0].focus();
				focusedLang.scrollIntoView({block: "center"});
			};
		}
	}

	return false;
});
