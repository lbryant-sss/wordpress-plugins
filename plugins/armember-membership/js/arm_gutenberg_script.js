!function(){arm_membership_shortcode=armember_block_admin.arm_block_esc_html.membership_shortcodes,arm_restrict_content_shortcode=armember_block_admin.arm_block_esc_html.restrict_content_shortcode,arm_armember_block_restriction=armember_block_admin.arm_block_esc_html.armember_block_restriction,arm_all_membership_plan=armember_block_admin.all_membership_plans,arm_gutenberg_block_restriction_feature=armember_block_admin.arm_gutenberg_block_restriction_feature;wp.i18n.__;var n=wp.element.createElement,o={};const e=wp.blocks["registerBlockType"],c=wp.element["Fragment"],{InspectorControls:i,InnerBlocks:m,useBlockProps:s}=wp.blockEditor,{PanelBody:l,RadioControl:b,ToggleControl:_}=wp.components;var r=n("svg",{width:20,height:20,viewBox:"-3 -1 23 20.22",style:{fill:"#005aee"}},n("path",{d:"M4.407,20.231 C2.002,14.225 3.926,9.833 3.926,9.833 C8.781,0.839 22.999,10.111 22.999,10.111 C5.011,3.480 4.407,20.231 4.407,20.231 ZM3.520,6.918 C1.576,6.918 -0.000,5.368 -0.000,3.455 C-0.000,1.543 1.576,-0.007 3.520,-0.007 C5.464,-0.007 7.039,1.543 7.039,3.455 C7.039,5.368 5.464,6.918 3.520,6.918 Z"}));e("armember/armember-shortcode",{title:arm_membership_shortcode.block_title,icon:r,category:"armember",keywords:arm_membership_shortcode.keywords,attributes:{ArmShortcode:{type:"string",default:""},content:{source:"html",selector:"h2"}},html:!0,insert:function(e){arm_open_form_shortcode_popup()},edit:function(r){window.arm_props_selected="1",window.arm_props=r;var e=jQuery("#block-"+window.arm_props.clientId).find(".wp-block-armember-armember-shortcode").val(),t=jQuery("#block-"+window.arm_props.clientId).find(".wp-block-armember-armember-shortcode").length;if("armember/armember-shortcode"==r.name){if(""==e||null==e||"undefined"==e||0==t){if(!r.isSelected)return n("textarea",{className:"wp-block-armember-armember-shortcode",value:r.attributes.ArmShortcode,style:o,onChange:function(e){r.setAttributes({ArmShortcode:jQuery("#block-"+window.arm_props.clientId).find(".wp-block-armember-armember-shortcode").val()})}},r.attributes.ArmShortcode);arm_open_form_shortcode_popup()}return n("textarea",{className:"wp-block-armember-armember-shortcode",value:r.attributes.ArmShortcode,style:o,onChange:function(e){r.setAttributes({ArmShortcode:jQuery("#block-"+window.arm_props.clientId).find(".wp-block-armember-armember-shortcode").val()})}},r.attributes.ArmShortcode)}},save:function(e){return void 0!==window.arm_props&&null!==window.arm_props&&1==jQuery("#block-"+window.arm_props.clientId).find(".editor-block-list__block-html-textarea").is(":visible")&&(e.attributes.ArmShortcode=jQuery("#block-"+window.arm_props.clientId).find(".editor-block-list__block-html-textarea").val()),e.attributes.ArmShortcode}}),e("armember/armember-restrict-content",{title:arm_restrict_content_shortcode.block_title,icon:r,category:"armember",keywords:arm_restrict_content_shortcode.keywords,attributes:{ArmRestrictContent:{type:"string",default:""},content:{source:"html",selector:"h2"}},html:!0,insert:function(e){arm_open_restriction_shortcode_popup()},edit:function(r){window.arm_props_selected="2",window.arm_restrict_content_props=r;var e=jQuery("#block-"+window.arm_restrict_content_props.clientId).find(".wp-block-armember-armember-restrict-content-textarea").val(),t=jQuery("#block-"+window.arm_restrict_content_props.clientId).find(".wp-block-armember-armember-restrict-content-textarea").length;if("armember/armember-restrict-content"==r.name){if(""==e||null==e||"undefined"==e||0==t){if(!r.isSelected)return n("textarea",{className:"wp-block-armember-armember-restrict-content-textarea",value:r.attributes.ArmRestrictContent,style:o,onChange:function(e){r.setAttributes({ArmRestrictContent:jQuery("#block-"+window.arm_restrict_content_props.clientId).find(".wp-block-armember-armember-restrict-content-textarea").val()})}},r.attributes.ArmRestrictContent);arm_open_restriction_shortcode_popup()}return n("textarea",{className:"wp-block-armember-armember-restrict-content-textarea",value:r.attributes.ArmRestrictContent,style:o,onChange:function(e){r.setAttributes({ArmRestrictContent:jQuery("#block-"+window.arm_restrict_content_props.clientId).find(".wp-block-armember-armember-restrict-content-textarea").val()})}},r.attributes.ArmRestrictContent)}},save:function(e){return void 0!==window.arm_restrict_content_props&&null!==window.arm_restrict_content_props&&1==jQuery("#block-"+window.arm_restrict_content_props.clientId).find(".editor-block-list__block-html-textarea").is(":visible")&&(e.attributes.ArmRestrictContent=jQuery("#block-"+window.arm_restrict_content_props.clientId).find(".editor-block-list__block-html-textarea").val()),e.attributes.ArmRestrictContent}}),1==arm_gutenberg_block_restriction_feature&&e("armember/armember-block-restriction",{title:arm_armember_block_restriction.block_title,description:arm_armember_block_restriction.description,icon:r,category:"armember",keywords:arm_armember_block_restriction.keywords,attributes:{plans:{type:"array",default:[]},uid:{type:"string",default:""},allowed_access:{type:"string",default:"show"}},edit:function(e){const{attributes:{plans:t,allowed_access:r},setAttributes:o}=e;var e=s(),a=arm_all_membership_plan.map(function(r){return[n(_,{key:r.value,label:r.label,checked:t.some(e=>e==r.value),onChange:function(e){if(e&&!t.some(e=>e==r.value)){const e=t.slice();e.push(r.value+""),o({plans:e})}else if(!e&&t.some(e=>e==r.value)){const e=t.filter(e=>e!=r.value);o({plans:e})}}})]});return[n(c,{key:"fragment"},n("div",{className:"armember-block-restrict-membership-element"},n(i,{key:"inspector"},n(l,{title:arm_armember_block_restriction.block_title,className:"armember-block-restrict-membership-element-panel"},n("p",null,n("strong",null,arm_armember_block_restriction.restriction_type.type)),n("div",{className:"armswitch-radio"},n(b,{selected:r,options:[{label:arm_armember_block_restriction.restriction_type.show,value:"show"},{label:arm_armember_block_restriction.restriction_type.hide,value:"hide"}],onChange:e=>o({allowed_access:e})})),n("p",null,n("strong",null,arm_armember_block_restriction.membership_plan)),n("div",{className:"armember-block-inspector-scrollable"},n("div",{className:"armswitch-checkbox"},n("div",{className:"armember-block-plan-help"},arm_armember_block_restriction.arm_armember_block_restriction),a)))),n("span",{className:"armember-block-title"},arm_armember_block_restriction.block_title),n("div",e,n(m,{templateLock:!1}))))]},save:function(e){var r=s.save();return n("div",r,n(m.Content,e.attributes.plans,e.attributes.allowed_access),n(m.Content,null))}})}((window.wp.blocks,window.wp.components,window.wp.i18n,window.wp.element,window.wp.editor,window.wp.blockEditor));