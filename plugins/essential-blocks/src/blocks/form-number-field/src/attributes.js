import {
    generateDimensionsAttributes,
    generateTypographyAttributes,
    generateBackgroundAttributes,
    generateBorderShadowAttributes,
    generateResponsiveRangeAttributes,
 } from "@essential-blocks/controls";

import {
    WRAPPER_MARGIN,
    WRAPPER_PADDING,
    WRAPPER_BORDER_SHADOW,
    WRAPPER_BG,
    LABEL_MARGIN,
    FIELD_BORDER,
    FIELD_PADDING,
    ICON_SIZE,
} from "./constants";

import {
    LABEL_TYPOGRAPHY,
    FIELD_TEXT_VALIDATION,
    FIELD_TEXT
} from "./constants/typographyPrefixConstants";
import { min } from "lodash";

const attributes = {
    // the following 4 attributes is must required for responsive options and asset generation for frontend
    // responsive control attributes ⬇
    resOption: {
        type: "string",
        default: "Desktop",
    },

    // blockId attribute for making unique className and other uniqueness ⬇
    parentBlockId: {
        type: "string",
    },
    parentBlockPaddingLeft: {
        type: "string",
    },
    parentBlockPaddingUnit: {
        type: "string",
    },
    parentBlockIconSize: {
        type: "string",
    },
    parentIconColor: {
        type: "string",
    },
    blockId: {
        type: "string",
    },
    blockRoot: {
        type: "string",
        default: "essential_block",
    },

    // blockMeta is for keeping all the styles ⬇
    blockMeta: {
        type: "object",
    },
    formStyle: {
        type: "string",
    },
    showLabel: {
        type: "boolean",
        default: true,
    },
    labelText: {
        type: "string",
        default: "Field Title",
    },
    fieldName: {
        type: "string",
    },
    placeholderText: {
        type: "string",
        default: "Enter your number here...",
    },
    defaultValue: {
        type: "string",
    },
    isRequired: {
        type: "boolean",
        default: true,
    },
    validationRules: {
        type: "object",
    },
    validationMessage: {
        type: "string",
        default: "This field is required.",
    },
    numberValidationMessage: {
        type: "string",
        default: "Invalid Number",
    },

    labelColor: {
        type: "string",
    },

    requiredColor: {
        type: "string",
    },
    fieldColor: {
        type: "string",
    },
    fieldPlaceholderColor: {
        type: "string",
    },
    fieldBgColor: {
        type: "string",
    },
    fieldValidationColor: {
        type: "string",
    },
    fieldValidationBorderColor: {
        type: "string",
    },

    isIcon: {
        type: "boolean",
        default: true,
    },
    icon: {
        type: "string",
        default: "fas fa-phone",
    },
    iconColor: {
        type: "string",
    },
    numberValidationType: {
        type: "string",
        default: "",
    },
    maxNumber: {
        type: "number",
        default: "",
    },
    minNumber: {
        type: "number",
        default: "",
    },
    numberLength: {
        type: "number", 
    },
    maxNumberValidationMessage: {
        type: "string",
    },
    minNumberValidationMessage: {
        type: "string",
    },
    numberLengthValidationMessage: {
        type: "string",
    },
    // typography attributes ⬇
    ...generateTypographyAttributes(LABEL_TYPOGRAPHY),
    ...generateTypographyAttributes(FIELD_TEXT_VALIDATION),
    ...generateTypographyAttributes(FIELD_TEXT),

    ...generateDimensionsAttributes(WRAPPER_MARGIN),
    ...generateDimensionsAttributes(WRAPPER_PADDING),
    ...generateBackgroundAttributes(WRAPPER_BG),
    ...generateBorderShadowAttributes(WRAPPER_BORDER_SHADOW),

    ...generateDimensionsAttributes(FIELD_PADDING),
    ...generateBorderShadowAttributes(FIELD_BORDER),
    ...generateDimensionsAttributes(LABEL_MARGIN),
    ...generateResponsiveRangeAttributes(ICON_SIZE),
};

export default attributes;
