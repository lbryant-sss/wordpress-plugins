"use strict";Object.defineProperty(exports,"__esModule",{value:!0}),exports.AttributesBlockEdit=AttributesBlockEdit;const element_1=require("@wordpress/element"),block_templates_1=require("@woocommerce/block-templates"),i18n_1=require("@wordpress/i18n"),tracks_1=require("@woocommerce/tracks"),core_data_1=require("@wordpress/core-data"),attribute_control_1=require("../../../components/attribute-control"),use_product_attributes_1=require("../../../hooks/use-product-attributes");function AttributesBlockEdit({attributes:t,context:{isInSelectedTab:e}}){const[r,o]=(0,core_data_1.useEntityProp)("postType","product","attributes"),c=(0,core_data_1.useEntityId)("postType","product"),_=(0,block_templates_1.useWooBlockProps)(t),{attributes:a,fetchAttributes:i,handleChange:d}=(0,use_product_attributes_1.useProductAttributes)({allAttributes:r,onChange:o,productId:c});return(0,element_1.useEffect)((()=>{e&&i()}),[r,e]),(0,element_1.createElement)("div",{..._},(0,element_1.createElement)(attribute_control_1.AttributeControl,{value:a,disabledAttributeIds:r.filter((t=>!!t.variation)).map((t=>t.id)),uiStrings:{disabledAttributeMessage:(0,i18n_1.__)("Already used in Variations","woocommerce")},onAdd:()=>{(0,tracks_1.recordEvent)("product_add_attributes_modal_add_button_click")},onChange:d,onNewModalCancel:()=>{(0,tracks_1.recordEvent)("product_add_attributes_modal_cancel_button_click")},onNewModalOpen:()=>{a.length?(0,tracks_1.recordEvent)("product_add_attribute_button"):(0,tracks_1.recordEvent)("product_add_first_attribute_button_click")},onAddAnother:()=>{(0,tracks_1.recordEvent)("product_add_attributes_modal_add_another_attribute_button_click")},onRemoveItem:()=>{(0,tracks_1.recordEvent)("product_add_attributes_modal_remove_attribute_button_click")},onRemove:()=>(0,tracks_1.recordEvent)("product_remove_attribute_confirmation_confirm_click"),onRemoveCancel:()=>(0,tracks_1.recordEvent)("product_remove_attribute_confirmation_cancel_click"),termsAutoSelection:"first",defaultVisibility:!0}))}