window.FAB_METABOX_DESIGN={...window.FAB_METABOX_DESIGN,linkedOptions:{padding:!0,margin:!0},defaultOptions:{...window.FAB_METABOX_DESIGN.defaultOptions,select2:{placeholder:"--choose--"}},init:()=>{window.FAB_METABOX_DESIGN.init_option_animation(),window.FAB_METABOX_DESIGN.init_option_modal_size(),window.FAB_METABOX_DESIGN.init_option_spacing(),window.FAB_METABOX_DESIGN.init_option_layout(),window.FAB_METABOX_DESIGN.init_option_theme(),window.FAB_METABOX_DESIGN.init_option_template(),jQuery(".fab-linked-option").on("click",window.FAB_METABOX_DESIGN.triggerLinkedOption)},init_option_animation:()=>{jQuery(".field_option_animation_element").each(function(){if(jQuery(this).attr("id")==="field_option_animation_fab")return!0;let n=jQuery(this);n.select2({data:window.FAB_PLUGIN.animateCSSAnimation});let o=n.data("selected");o&&(n.val(o),n.trigger("change"))});let e=jQuery("#field_option_animation_fab"),i=[...window.FAB_PLUGIN.animateCSSAnimation];i.splice(1,0,{id:"ripple",text:"Ripple"}),e.select2({data:i});let t=e.data("selected");t&&(e.val(t),e.trigger("change"))},init_option_template:()=>{let e=jQuery("#field_option_design_template_shape");e.select2({placeholder:"--choose--",data:window.FAB_PLUGIN.defaultOptions.template.shape}),e.val(e.data("selected")),e.trigger("change")},init_option_modal_size:()=>{let e=jQuery("#field_option_design_modal_size");e.select2({placeholder:"--choose--",data:window.FAB_METABOX_DESIGN.defaultOptions.size.type}),e.val(e.data("selected")),e.on("select2:select",window.FAB_METABOX_DESIGN.triggerDesignSize),e.trigger("change"),window.FAB_METABOX_DESIGN.triggerDesignSize()},init_option_spacing:()=>{jQuery(".fab_modal_layout_spacing_sizing").each(function(){let i=jQuery(this),t=i.data("selected");t&&(i.val(t),i.trigger("change"))});let e={padding:[],margin:[]};jQuery(".fab-modal-layout-spacing").each(function(){e[jQuery(this).data("layout")].push(jQuery(this).val())}),window.FAB_METABOX_DESIGN.linkedOptions.padding=e.padding.every((i,t,n)=>i===n[0]),window.FAB_METABOX_DESIGN.linkedOptions.margin=e.margin.every((i,t,n)=>i===n[0]),jQuery(".fab-linked-option").each(function(){window.FAB_METABOX_DESIGN.set_linked_spacing_button(this)}),jQuery(".fab-modal-layout-spacing").on("keyup",function(){let i=jQuery(this).data("layout");window.FAB_METABOX_DESIGN.linkedOptions[i]&&jQuery(`.fab-modal-layout-${i}`).val(jQuery(this).val())})},init_option_layout:()=>{let e=jQuery("#field_option_design_modal_layout_id");e.select2({placeholder:"--choose--",data:window.FAB_METABOX_DESIGN.defaultOptions.layout}),e.val(e.data("selected")),e.trigger("change")},init_option_theme:()=>{let e=jQuery("#field_option_design_modal_theme");e.select2({placeholder:"--choose--",data:window.FAB_METABOX_DESIGN.defaultOptions.theme}),e.val(e.data("selected")),e.trigger("change")},triggerDesignSize(){let e=jQuery("#setting_design_custom_size");jQuery("#field_option_design_modal_size").val()==="custom"?e.show():e.hide()},triggerLinkedOption(){let e=jQuery(this).data("layout");window.FAB_METABOX_DESIGN.linkedOptions[e]=!window.FAB_METABOX_DESIGN.linkedOptions[e],window.FAB_METABOX_DESIGN.set_linked_spacing_button(this)},set_linked_spacing_button:e=>{let i=jQuery(e).data("layout"),t=window.FAB_METABOX_DESIGN.linkedOptions[i]?"fa-link":"fa-unlink";jQuery("em",e).removeClass("fa-link fa-unlink"),jQuery("em",e).addClass(t)}};
//# sourceMappingURL=metabox-design.B5uLVGO-.js.map
