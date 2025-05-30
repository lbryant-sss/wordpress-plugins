/**
 * WordPress dependencies
 */
import { useBlockProps, RichText, InnerBlocks } from "@wordpress/block-editor";
import { omit } from "lodash";

import attributes from "./attributes";
import { EBDisplayIcon, BlockProps } from "@essential-blocks/controls";

const deprecated = [
    {
        // Add new deprecation for the version before scroll features were added
        attributes: omit({ ...attributes }, [
            "scrollType",
            "scrollPercentage",
            "scrollElement",
            "scrollDistance",
            "scrollOffset"
        ]),
        supports: {
            align: ["wide", "full"],
        },
        migrate: (oldAttributes) => {
            return {
                ...oldAttributes,
                scrollType: "percentage",
                scrollPercentage: 50,
                scrollElement: "",
                scrollDistance: "100px",
                scrollOffset: 0
            };
        },
        save: ({ attributes }) => {
            const {
                blockId,
                classHook,
                trigger,
                btnText,
                btnIcon,
                iconPosition,
                btnAlignment,
                displayCloseIcon,
                escToExit,
                clickToExit,
                position,
                btnType,
                triggerIcon,
                pageLoadDelay,
                eleIdentifier,
                useCloseIcon,
                closeBtnText,
                autoExit,
                autoExitTime,
                useCookies,
                cookieExpireTime,
                disablePageScroll,
            } = attributes;
            const alignmentClass =
                "left" === btnAlignment
                    ? " alignment-left"
                    : "right" === btnAlignment
                        ? " alignment-right"
                        : " alignment-center";
            return (
                <BlockProps.Save attributes={attributes}>
                    <div className="eb-parent-wrapper">
                        <div
                            id={blockId}
                            className={`eb-popup-container ${blockId}`}
                            data-block-id={blockId}
                            data-popup-type={trigger}
                            data-popup-delay={"page_load" == trigger ? pageLoadDelay : undefined}
                            data-external-identifier={
                                "external" == trigger ? eleIdentifier : undefined
                            }
                            data-close-btn={displayCloseIcon}
                            data-esc-btn={escToExit}
                            data-click-exit={clickToExit}
                            data-auto-exit={autoExit}
                            data-auto-exit-time={autoExitTime}
                            data-use-cookie={useCookies}
                            data-cookie-expire-time={cookieExpireTime}
                            data-page-scroll={disablePageScroll}
                        >
                            <div
                                className={`eb-popup-btn-wrapper eb-parent-${blockId} ${classHook}`}
                            >
                                {"btn_click" === trigger && (
                                    <>
                                        <div className={`eb-popup-button${alignmentClass}`}>
                                            <a className="eb-popup-button-anchor" role="button">
                                                {"button" === btnType && (
                                                    <>
                                                        {btnIcon && "left" === iconPosition && (
                                                            <>
                                                                <EBDisplayIcon
                                                                    classNam={`eb-popup-button-icon eb-popup-button-icon-left`}
                                                                    icon={btnIcon}
                                                                />
                                                            </>
                                                        )}
                                                        <RichText.Content value={btnText} tagName="p" />
                                                        {btnIcon && "right" === iconPosition && (
                                                            <>
                                                                <EBDisplayIcon
                                                                    classNam={`eb-popup-button-icon eb-popup-button-icon-right`}
                                                                    icon={btnIcon}
                                                                />
                                                            </>
                                                        )}
                                                    </>
                                                )}
                                                {"icon" === btnType && (
                                                    <>
                                                        <EBDisplayIcon classNam={`eb-popup-icon`} icon={triggerIcon} />
                                                    </>
                                                )}
                                            </a>
                                        </div>
                                    </>
                                )}
                            </div>
                            <div className="eb-popup-overlay"></div>
                            <div className="modal-main-wrap">
                                <div className={`eb-modal-container eb_popup_${position}`}>
                                    <div className="eb-popup-content">
                                        {displayCloseIcon && (
                                            <>
                                                <div className="eb-popup-close-icon">
                                                    {useCloseIcon ? (
                                                        <span className="dashicons dashicons-no-alt"></span>
                                                    ) : (
                                                        closeBtnText
                                                    )}
                                                </div>
                                            </>
                                        )}
                                        <InnerBlocks.Content />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </BlockProps.Save>
            );
        },
    },
    {
        attributes: omit({ ...attributes }, ["disablePageScroll"]),
        supports: {
            align: ["wide", "full"],
        },
        save: ({ attributes }) => {
            const {
                blockId,
                classHook,
                trigger,
                btnText,
                btnIcon,
                iconPosition,
                btnAlignment,
                displayCloseIcon,
                escToExit,
                clickToExit,
                position,
                btnType,
                triggerIcon,
                pageLoadDelay,
                eleIdentifier,
                useCloseIcon,
                closeBtnText,
                autoExit,
                autoExitTime,
                useCookies,
                cookieExpireTime,
            } = attributes;
            const alignmentClass =
                "left" === btnAlignment
                    ? " alignment-left"
                    : "right" === btnAlignment
                    ? " alignment-right"
                    : " alignment-center";
            return (
                <BlockProps.Save attributes={attributes}>
                    <div className="eb-parent-wrapper">
                        <div
                            id={blockId}
                            className={`eb-popup-container ${blockId}`}
                            data-block-id={blockId}
                            data-popup-type={trigger}
                            data-popup-delay={
                                "page_load" == trigger
                                    ? pageLoadDelay
                                    : undefined
                            }
                            data-external-identifier={
                                "external" == trigger
                                    ? eleIdentifier
                                    : undefined
                            }
                            data-close-btn={displayCloseIcon}
                            data-esc-btn={escToExit}
                            data-click-exit={clickToExit}
                            data-auto-exit={autoExit}
                            data-auto-exit-time={autoExitTime}
                            data-use-cookie={useCookies}
                            data-cookie-expire-time={cookieExpireTime}
                        >
                            <div
                                className={`eb-popup-btn-wrapper eb-parent-${blockId} ${classHook}`}
                            >
                                {"btn_click" === trigger && (
                                    <>
                                        <div
                                            className={`eb-popup-button${alignmentClass}`}
                                        >
                                            <a
                                                className="eb-popup-button-anchor"
                                                role="button"
                                            >
                                                {"button" === btnType && (
                                                    <>
                                                        {btnIcon &&
                                                            "left" ===
                                                                iconPosition && (
                                                                <>
                                                                    <EBDisplayIcon
                                                                        classNam={`eb-popup-button-icon eb-popup-button-icon-left`}
                                                                        icon={
                                                                            btnIcon
                                                                        }
                                                                    />
                                                                </>
                                                            )}
                                                        <RichText.Content
                                                            value={btnText}
                                                            tagName="p"
                                                        />
                                                        {btnIcon &&
                                                            "right" ===
                                                                iconPosition && (
                                                                <>
                                                                    <EBDisplayIcon
                                                                        classNam={`eb-popup-button-icon eb-popup-button-icon-right`}
                                                                        icon={
                                                                            btnIcon
                                                                        }
                                                                    />
                                                                </>
                                                            )}
                                                    </>
                                                )}
                                                {"icon" === btnType && (
                                                    <>
                                                        <EBDisplayIcon
                                                            classNam={`eb-popup-icon`}
                                                            icon={triggerIcon}
                                                        />
                                                    </>
                                                )}
                                            </a>
                                        </div>
                                    </>
                                )}
                            </div>
                            <div className="eb-popup-overlay"></div>
                            <div className="modal-main-wrap">
                                <div
                                    className={`eb-modal-container eb_popup_${position}`}
                                >
                                    <div className="eb-popup-content">
                                        {displayCloseIcon && (
                                            <>
                                                <div className="eb-popup-close-icon">
                                                    {useCloseIcon ? (
                                                        <span className="dashicons dashicons-no-alt"></span>
                                                    ) : (
                                                        closeBtnText
                                                    )}
                                                </div>
                                            </>
                                        )}
                                        <InnerBlocks.Content />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </BlockProps.Save>
            );
        },
    },
    {
        attributes: { ...attributes },
        supports: {
            align: ["wide", "full"],
        },
        save: ({ attributes }) => {
            const {
                blockId,
                classHook,
                trigger,
                btnText,
                btnIcon,
                iconPosition,
                btnAlignment,
                displayCloseIcon,
                escToExit,
                clickToExit,
                position,
                btnType,
                triggerIcon,
                pageLoadDelay,
                eleIdentifier,
                useCloseIcon,
                closeBtnText,
                autoExit,
                autoExitTime,
                useCookies,
                cookieExpireTime,
            } = attributes;
            const alignmentClass =
                "left" === btnAlignment
                    ? " alignment-left"
                    : "right" === btnAlignment
                    ? " alignment-right"
                    : " alignment-center";
            return (
                <div {...useBlockProps.save()}>
                    <div className="eb-parent-wrapper">
                        <div
                            id={blockId}
                            className={`eb-popup-container ${blockId}`}
                            data-block-id={blockId}
                            data-popup-type={trigger}
                            data-popup-delay={
                                "page_load" == trigger
                                    ? pageLoadDelay
                                    : undefined
                            }
                            data-external-identifier={
                                "external" == trigger
                                    ? eleIdentifier
                                    : undefined
                            }
                            data-close-btn={displayCloseIcon}
                            data-esc-btn={escToExit}
                            data-click-exit={clickToExit}
                            data-auto-exit={autoExit}
                            data-auto-exit-time={autoExitTime}
                            data-use-cookie={useCookies}
                            data-cookie-expire-time={cookieExpireTime}
                        >
                            <div
                                className={`eb-popup-btn-wrapper eb-parent-${blockId} ${classHook}`}
                            >
                                {"btn_click" === trigger && (
                                    <>
                                        <div
                                            className={`eb-popup-button${alignmentClass}`}
                                        >
                                            <a className="eb-popup-button-anchor">
                                                {"button" === btnType && (
                                                    <>
                                                        {btnIcon &&
                                                            "left" ===
                                                                iconPosition && (
                                                                <>
                                                                    <EBDisplayIcon
                                                                        classNam={`eb-popup-button-icon eb-popup-button-icon-left`}
                                                                        icon={
                                                                            btnIcon
                                                                        }
                                                                    />
                                                                </>
                                                            )}
                                                        <RichText.Content
                                                            value={btnText}
                                                            tagName="p"
                                                        />
                                                        {btnIcon &&
                                                            "right" ===
                                                                iconPosition && (
                                                                <>
                                                                    <EBDisplayIcon
                                                                        classNam={`eb-popup-button-icon eb-popup-button-icon-right`}
                                                                        icon={
                                                                            btnIcon
                                                                        }
                                                                    />
                                                                </>
                                                            )}
                                                    </>
                                                )}
                                                {"icon" === btnType && (
                                                    <>
                                                        <EBDisplayIcon
                                                            classNam={`eb-popup-icon`}
                                                            icon={triggerIcon}
                                                        />
                                                    </>
                                                )}
                                            </a>
                                        </div>
                                    </>
                                )}
                            </div>
                            <div className="eb-popup-overlay"></div>
                            <div className="modal-main-wrap">
                                <div
                                    className={`eb-modal-container eb_popup_${position}`}
                                >
                                    <div className="eb-popup-content">
                                        {displayCloseIcon && (
                                            <>
                                                <div className="eb-popup-close-icon">
                                                    {useCloseIcon ? (
                                                        <span className="dashicons dashicons-no-alt"></span>
                                                    ) : (
                                                        closeBtnText
                                                    )}
                                                </div>
                                            </>
                                        )}
                                        <InnerBlocks.Content />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        attributes: { ...attributes },
        supports: {
            align: ["wide", "full"],
        },
        save: ({ attributes }) => {
            const {
                blockId,
                classHook,
                trigger,
                btnText,
                btnIcon,
                iconPosition,
                btnAlignment,
                displayCloseIcon,
                escToExit,
                clickToExit,
                position,
                btnType,
                triggerIcon,
                pageLoadDelay,
                eleIdentifier,
                useCloseIcon,
                closeBtnText,
                autoExit,
                autoExitTime,
                useCookies,
                cookieExpireTime,
            } = attributes;
            const alignmentClass =
                "left" === btnAlignment
                    ? " alignment-left"
                    : "right" === btnAlignment
                    ? " alignment-right"
                    : " alignment-center";
            return (
                <div {...useBlockProps.save()}>
                    <div className="eb-parent-wrapper">
                        <div
                            id={blockId}
                            className={`eb-popup-container ${blockId}`}
                            data-block-id={blockId}
                            data-popup-type={trigger}
                            data-popup-delay={
                                "page_load" == trigger
                                    ? pageLoadDelay
                                    : undefined
                            }
                            data-external-identifier={
                                "external" == trigger
                                    ? eleIdentifier
                                    : undefined
                            }
                            data-close-btn={displayCloseIcon}
                            data-esc-btn={escToExit}
                            data-click-exit={clickToExit}
                            data-auto-exit={autoExit}
                            data-auto-exit-time={autoExitTime}
                            data-use-cookie={useCookies}
                            data-cookie-expire-time={cookieExpireTime}
                        >
                            <div
                                className={`eb-popup-btn-wrapper eb-parent-${blockId} ${classHook}`}
                            >
                                {"btn_click" === trigger && (
                                    <>
                                        <div
                                            className={`eb-popup-button${alignmentClass}`}
                                        >
                                            <a className="eb-popup-button-anchor">
                                                {"button" === btnType && (
                                                    <>
                                                        {btnIcon &&
                                                            "left" ===
                                                                iconPosition && (
                                                                <>
                                                                    <i
                                                                        className={`${btnIcon} eb-popup-button-icon eb-popup-button-icon-left`}
                                                                    ></i>
                                                                </>
                                                            )}
                                                        <RichText.Content
                                                            value={btnText}
                                                            tagName="p"
                                                        />
                                                        {btnIcon &&
                                                            "right" ===
                                                                iconPosition && (
                                                                <>
                                                                    <i
                                                                        className={`${btnIcon} eb-popup-button-icon eb-popup-button-icon-right`}
                                                                    ></i>
                                                                </>
                                                            )}
                                                    </>
                                                )}
                                                {"icon" === btnType && (
                                                    <>
                                                        <i
                                                            className={`${triggerIcon} eb-popup-icon`}
                                                        ></i>
                                                    </>
                                                )}
                                            </a>
                                        </div>
                                    </>
                                )}
                            </div>
                            <div className="eb-popup-overlay"></div>
                            <div className="modal-main-wrap">
                                <div
                                    className={`eb-modal-container eb_popup_${position}`}
                                >
                                    <div className="eb-popup-content">
                                        {displayCloseIcon && (
                                            <>
                                                <div className="eb-popup-close-icon">
                                                    {useCloseIcon ? (
                                                        <span className="dashicons dashicons-no-alt"></span>
                                                    ) : (
                                                        closeBtnText
                                                    )}
                                                </div>
                                            </>
                                        )}
                                        <InnerBlocks.Content />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        attributes: omit({ ...attributes }, [
            "autoExit",
            "autoExitTime",
            "useCloseIcon",
            "useCookies",
            "cookieExpireTime",
        ]),
        supports: {
            align: ["wide", "full"],
        },
        save: ({ attributes }) => {
            const {
                blockId,
                classHook,
                trigger,
                btnText,
                btnIcon,
                iconPosition,
                btnAlignment,
                displayCloseIcon,
                escToExit,
                clickToExit,
                position,
                btnType,
                triggerIcon,
                pageLoadDelay,
                eleIdentifier,
                closeBtnText,
            } = attributes;
            const alignmentClass =
                "left" === btnAlignment
                    ? " alignment-left"
                    : "right" === btnAlignment
                    ? " alignment-right"
                    : " alignment-center";
            return (
                <div {...useBlockProps.save()}>
                    <div className="eb-parent-wrapper">
                        <div
                            id={blockId}
                            className={`eb-popup-container ${blockId}`}
                            data-block-id={blockId}
                            data-popup-type={trigger}
                            data-popup-delay={
                                "page_load" == trigger
                                    ? pageLoadDelay
                                    : undefined
                            }
                            data-external-identifier={
                                "external" == trigger
                                    ? eleIdentifier
                                    : undefined
                            }
                            data-close-btn={displayCloseIcon}
                            data-esc-btn={escToExit}
                            data-click-exit={clickToExit}
                        >
                            <div
                                className={`eb-popup-btn-wrapper eb-parent-${blockId} ${classHook}`}
                            >
                                {"btn_click" === trigger && (
                                    <>
                                        <div
                                            className={`eb-popup-button${alignmentClass}`}
                                        >
                                            <a className="eb-popup-button-anchor">
                                                {"button" === btnType && (
                                                    <>
                                                        {btnIcon &&
                                                            "left" ===
                                                                iconPosition && (
                                                                <>
                                                                    <i
                                                                        className={`${btnIcon} eb-popup-button-icon eb-popup-button-icon-left`}
                                                                    ></i>
                                                                </>
                                                            )}
                                                        <RichText.Content
                                                            value={btnText}
                                                            tagName="p"
                                                        />
                                                        {btnIcon &&
                                                            "right" ===
                                                                iconPosition && (
                                                                <>
                                                                    <i
                                                                        className={`${btnIcon} eb-popup-button-icon eb-popup-button-icon-right`}
                                                                    ></i>
                                                                </>
                                                            )}
                                                    </>
                                                )}
                                                {"icon" === btnType && (
                                                    <>
                                                        <i
                                                            className={`${triggerIcon} eb-popup-icon`}
                                                        ></i>
                                                    </>
                                                )}
                                            </a>
                                        </div>
                                    </>
                                )}
                            </div>
                            <div className="eb-popup-overlay"></div>
                            <div className="modal-main-wrap">
                                <div
                                    className={`eb-modal-container eb_popup_${position}`}
                                >
                                    <div className="eb-popup-content">
                                        {displayCloseIcon && (
                                            <div className="eb-popup-close-icon">
                                                {closeBtnText}
                                            </div>
                                        )}
                                        <InnerBlocks.Content />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        },
    },
];

export default deprecated;
