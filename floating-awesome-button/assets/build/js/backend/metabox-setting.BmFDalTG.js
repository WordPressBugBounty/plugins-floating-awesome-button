window.FAB_METABOX_SETTING={...window.FAB_METABOX_SETTING,defaultOptions:{...window.FAB_METABOX_SETTING.defaultOptions,select2:{placeholder:"--choose--"}},init:()=>{jQuery(".fab-container.metabox-setting .select2").select2(),jQuery(".fab-container.metabox-setting .colorpicker").wpColorPicker(),window.FAB_METABOX_SETTING.initTypeOptions(),window.FAB_METABOX_SETTING.initHotkeyOptions(),window.FAB_METABOX_SETTING.initOptionSettings(),jQuery(document).ready(function(e){window.FAB_METABOX_SETTING.initWooCommerceFeaturedProduct(),window.FAB_METABOX_SETTING.initWooCommerceApplyCoupon(),jQuery("#field_fab_setting_type").on("change.select2",window.FAB_METABOX_SETTING.triggerTypeChange)})},initTypeOptions:()=>{let e=jQuery("#field_fab_setting_type");if(e){let i=[];e.data("select2")&&e.select2("destroy"),i=window.FAB_METABOX_SETTING.defaultOptions.types.map(t=>({text:t.text,children:t.children.map(o=>({id:o.id,text:o.text,disabled:o.is_enable===!1}))})),e.select2({placeholder:"--choose--",data:i}),e.data("selected")&&(e.val(e.data("selected")),e.trigger("change"))}},initHotkeyOptions:()=>{let e=jQuery("#field_fab_setting_hotkey");if(e){let i=[{id:"none",text:"None"}];e.data("select2")&&e.select2("destroy");for(let t=1;t<=12;t++)i.push({id:`f${t}`,text:`F${t}`});for(let t=1;t<=12;t++)i.push({id:`shift+f${t}`,text:`Shift+F${t}`});for(let t=1;t<=12;t++)i.push({id:`alt+f${t}`,text:`Alt+F${t}`});let a=["esc","tab","space","return","backspace","insert","home","del","end","pageup","pagedown","left","up","right","down"];a.forEach(t=>{i.push({id:`ctrl+${t}`,text:`Ctrl+${t}`})}),a.forEach(t=>{i.push({id:`shift+${t}`,text:`Shift+${t}`})}),a.forEach(t=>{i.push({id:`alt+${t}`,text:`Alt+${t}`})});for(let t=65;t<=90;t++)i.push({id:`ctrl+${String.fromCharCode(t)}`,text:`Ctrl+${String.fromCharCode(t)}`});for(let t=65;t<=90;t++)i.push({id:`shift+${String.fromCharCode(t)}`,text:`Shift+${String.fromCharCode(t)}`});for(let t=65;t<=90;t++)i.push({id:`alt+${String.fromCharCode(t)}`,text:`Alt+${String.fromCharCode(t)}`});e.select2({placeholder:"--choose--",data:i}),e.data("selected")&&(e.val(e.data("selected")),e.trigger("change"))}},initOptionSettings:()=>{let e=jQuery(".fab_option_settings");e&&(e.data("select2")&&e.select2("destroy"),e.select2(),e.data("selected")&&(e.val(e.data("selected")),e.trigger("change")))},initWooCommerceFeaturedProduct:()=>{let e=jQuery("#field_fab_setting_featured_product, #field_fab_setting_scarcity_toast_product");if(e){e.data("select2")&&e.select2("destroy"),jQuery("#field_fab_setting_featured_product, #field_fab_setting_scarcity_toast_product").select2({placeholder:"--choose--",allowClear:!0,minimumInputLength:2,ajax:{url:"/wp-admin/admin-ajax.php",dataType:"json",delay:250,data:function(a){return{action:"fab_search_products",q:a.term}},processResults:function(a){return{results:a.map(function(t){return{id:t.id,text:t.text}})}},cache:!0}});const i=jQuery("#field_fab_setting_featured_product, #field_fab_setting_scarcity_toast_product").data("selected");if(i){const[a,...t]=i.split("-"),o=t.join("-").trim();jQuery("#field_fab_setting_featured_product, #field_fab_setting_scarcity_toast_product").select2("trigger","select",{data:{id:a,text:o}}),jQuery("#field_fab_setting_featured_product, #field_fab_setting_scarcity_toast_product").val(a).trigger("change")}}},initWooCommerceApplyCoupon:()=>{let e=jQuery("#field_fab_setting_apply_coupon");if(e){e.data("select2")&&e.select2("destroy"),jQuery("#field_fab_setting_apply_coupon").select2({placeholder:"--choose--",allowClear:!0,minimumInputLength:2,ajax:{url:"/wp-admin/admin-ajax.php",dataType:"json",delay:250,data:function(a){return{action:"fab_search_coupons",q:a.term}},processResults:function(a){return{results:a.map(function(t){return{id:t.id,text:t.text}})}},cache:!0}});const i=jQuery("#field_fab_setting_apply_coupon").data("selected");if(i){const[a,...t]=i.split("-"),o=t.join("-").trim();jQuery("#field_fab_setting_apply_coupon").select2("trigger","select",{data:{id:a,text:o}}),jQuery("#field_fab_setting_apply_coupon").val(a).trigger("change")}}},triggerTypeChange(e){window.FAB_METABOX_SETTING.data.fab.type=e.target.value,jQuery("#fab_setting_type_update").click(),jQuery(document).ready(function(i){window.FAB_METABOX_SETTING.initOptionSettings(),e.target.value==="featured_product"||e.target.value==="scarcity_toast"?window.FAB_METABOX_SETTING.initWooCommerceFeaturedProduct():e.target.value==="apply_coupon"&&window.FAB_METABOX_SETTING.initWooCommerceApplyCoupon()})}};
//# sourceMappingURL=metabox-setting.BmFDalTG.js.map
