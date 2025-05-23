document.addEventListener("DOMContentLoaded", function (event) {
    let SetEqualHeightOfMultiColumnBlock = false;
    if (window?.eb_frontend && window?.eb_frontend.SetEqualHeightOfMultiColumnBlock === 'function') {
        SetEqualHeightOfMultiColumnBlock = window.eb_frontend.SetEqualHeightOfMultiColumnBlock
    }
    let toggleBlocks = document.querySelectorAll(".eb-toggle-wrapper");

    if (!toggleBlocks) return;

    for (let block of toggleBlocks) {
        // Selectors
        let switcher = block.querySelector(".eb-toggle-switch > input");
        let primaryLabel = block.querySelector(".eb-toggle-primary-label");
        let secondaryLabel = block.querySelector(".eb-toggle-secondary-label");
        let textPrimaryLabel = block.querySelector(
            ".eb-toggle-primary-label-text"
        );
        let textSecondaryLabel = block.querySelector(
            ".eb-toggle-secondary-label-text"
        );
        let slider = block.querySelector(".eb-toggle-slider");
        let controller = block.querySelector(".eb-toggle-controller");
        let content = block.querySelector(".eb-toggle-content");
        let seperator = block.querySelector(".eb-toggle-seperator");

        // Data attributes
        let initContent = block.getAttribute("data-init-content");
        let size = block.getAttribute("data-size");
        let switchStyle = block.getAttribute("data-switch-style");
        let primaryColor = block.getAttribute("data-primary-color");
        let secondaryColor = block.getAttribute("data-secondary-color");
        let activeColor = block.getAttribute("data-active-color");
        let bgColor = block.getAttribute("data-bg-color");
        let activeBg = block.getAttribute("data-active-bg");

        const defaultBg = "inherit";
        const defaultActiveBg = "inherit";

        // Move slider for different size
        let getTransform = () => {
            switch (size) {
                case "s":
                    return "translateX(22px)";
                case "m":
                    return "translateX(26px)";
                case "l":
                    return "translateX(36px)";
                case "xl":
                    return "translateX(42px)";
            }
        };

        let getRadius = () => {
            switch (size) {
                case "s":
                    return "10px";
                case "m":
                    return "13px";
                case "l":
                    return "18px";
                case "xl":
                    return "21px";
            }
        };

        // Toggle type switch
        let id = block
            .querySelector(".eb-text-switch-label")
            .getAttribute("for");
        let toggler = block.querySelector(`#${id}`);

        if (initContent === "primary") {
            showPrimary();
        } else {
            toggler.checked = true;
            showSecondary();
        }

        toggler.addEventListener("change", onTextToggleChange);

        function onTextToggleChange() {
            this.checked ? showSecondary() : showPrimary();
        }

        // Init text type label background
        if (switchStyle === "text") {
            primaryLabel.style.background = bgColor || defaultBg;
            secondaryLabel.style.background = bgColor || defaultBg;
            seperator.style.background = bgColor || defaultBg;
        }

        // Add click event for text type switch
        function activePrimary() {
            if (switchStyle !== "toggle") {
                primaryLabel.style.background = activeBg || defaultActiveBg;
                secondaryLabel.style.background = bgColor || defaultBg;
            }
            showPrimary();
        }

        function activeSecondary() {
            if (switchStyle !== "toggle") {
                secondaryLabel.style.background = activeBg || defaultActiveBg;
                primaryLabel.style.background = bgColor || defaultBg;
            }
            showSecondary();
        }

        primaryLabel.addEventListener("click", activePrimary);
        secondaryLabel.addEventListener("click", activeSecondary);

        if (switchStyle === "text") {
            initContent === "primary" ? activePrimary() : activeSecondary();
        }

        // Make controller round
        if (switchStyle == "rounded") {
            slider.style.borderRadius = "21px";
            controller.style.borderRadius = getRadius();
        }

        // Display initial content
        initContent === "primary" ? showPrimary() : showSecondary();

        switcher.addEventListener("change", onSwitch);

        function onSwitch() {
            this.checked ? showSecondary() : showPrimary();
        }

        function showPrimary() {
            switcher.checked = false;

            if (block.classList.contains('eb-toggle-secondary')) {
                block.classList.remove('eb-toggle-secondary')
                block.classList.add('eb-toggle-primary')
            }

            // Fade out secondary content
            const secondaryContent = content.children[1];
            secondaryContent.classList.remove("active");
            secondaryContent.classList.add("inactive");

            // Wait for the transition to finish before setting display to none
            // secondaryContent.addEventListener('transitionend', function () {
            //     if (secondaryContent.classList.contains("inactive")) {
            //         secondaryContent.style.display = 'none';
            //     }
            // }, { once: true });

            // Show primary content
            const primaryContent = content.children[0];
            primaryContent.classList.remove("inactive");
            // primaryContent.style.display = 'block';

            setTimeout(function () {
                const multiColumn = primaryContent.querySelector('.eb-mcpt-wrap');
                if (SetEqualHeightOfMultiColumnBlock && multiColumn) {
                    SetEqualHeightOfMultiColumnBlock(multiColumn);
                }
            }, 10)
            primaryContent.classList.add("active");

            secondaryLabel.style.color = secondaryColor;
            primaryLabel.style.color = activeColor || primaryColor;

            if (switchStyle === "toggle") {
                textSecondaryLabel.style.color = secondaryColor;
                textPrimaryLabel.style.color = activeColor || primaryColor;
            }

            if (switchStyle !== "text") {
                controller.style.transform = "translateX(0px)";
            }
        }

        function showSecondary() {
            switcher.checked = true;

            if (block.classList.contains('eb-toggle-primary')) {
                block.classList.remove('eb-toggle-primary')
                block.classList.add('eb-toggle-secondary')
            }

            // Fade out primary content
            const primaryContent = content.children[0];
            const secondaryContent = content.children[1];

            primaryContent.classList.remove("active");
            primaryContent.classList.add("inactive");

            // Show secondary content
            secondaryContent.classList.remove("inactive");

            setTimeout(function () {
                const multiColumn = secondaryContent.querySelector('.eb-mcpt-wrap');
                if (SetEqualHeightOfMultiColumnBlock && multiColumn) {
                    SetEqualHeightOfMultiColumnBlock(multiColumn);
                }
            }, 10)
            secondaryContent.classList.add("active");


            primaryLabel.style.color = primaryColor;
            secondaryLabel.style.color = activeColor;

            if (switchStyle === "toggle") {
                textPrimaryLabel.style.color = primaryColor;
                textSecondaryLabel.style.color = activeColor || secondaryColor;
            }

            if (switchStyle !== "text") {
                controller.style.transform = getTransform();
            }
        }
    }
});
