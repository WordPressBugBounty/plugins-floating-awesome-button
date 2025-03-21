import{e as pt,r as E,m as mt,i as St,h as Ct,a as $t,b as Y,c as ft,t as It,g as Bt,o as At,w as it,n as Et,d as Ot,f as Z,j as g,k as p,l as _,F as A,p as X,q as F,u as t,s as M,v as Tt,x as P,y as Mt,z as ct,A as B,B as gt,C as st,D as ut,E as o,G as l,H as Nt,I as rt,J as zt,K as Pt}from"../../assets/runtime-dom.esm-bundler-C5WK88vT.js";/*!
 * pinia v3.0.1
 * (c) 2025 Eduardo San Martin Morote
 * @license MIT
 */let bt;const et=a=>bt=a,xt=Symbol();function nt(a){return a&&typeof a=="object"&&Object.prototype.toString.call(a)==="[object Object]"&&typeof a.toJSON!="function"}var W;(function(a){a.direct="direct",a.patchObject="patch object",a.patchFunction="patch function"})(W||(W={}));function Ft(){const a=pt(!0),c=a.run(()=>E({}));let i=[],n=[];const e=mt({install(u){et(e),e._a=u,u.provide(xt,e),u.config.globalProperties.$pinia=e,n.forEach(s=>i.push(s)),n=[]},use(u){return this._a?i.push(u):n.push(u),this},_p:i,_a:null,_e:a,_s:new Map,state:c});return e}const vt=()=>{};function dt(a,c,i,n=vt){a.push(c);const e=()=>{const u=a.indexOf(c);u>-1&&(a.splice(u,1),n())};return!i&&Bt()&&At(e),e}function R(a,...c){a.slice().forEach(i=>{i(...c)})}const Gt=a=>a(),_t=Symbol(),at=Symbol();function lt(a,c){a instanceof Map&&c instanceof Map?c.forEach((i,n)=>a.set(n,i)):a instanceof Set&&c instanceof Set&&c.forEach(a.add,a);for(const i in c){if(!c.hasOwnProperty(i))continue;const n=c[i],e=a[i];nt(e)&&nt(n)&&a.hasOwnProperty(i)&&!Y(n)&&!ft(n)?a[i]=lt(e,n):a[i]=n}return a}const jt=Symbol();function Lt(a){return!nt(a)||!a.hasOwnProperty(jt)}const{assign:V}=Object;function Vt(a){return!!(Y(a)&&a.effect)}function qt(a,c,i,n){const{state:e,actions:u,getters:s}=c,y=i.state.value[a];let f;function m(){y||(i.state.value[a]=e?e():{});const C=Ot(i.state.value[a]);return V(C,u,Object.keys(s||{}).reduce((T,$)=>(T[$]=mt(Z(()=>{et(i);const S=i._s.get(a);return s[$].call(S,S)})),T),{}))}return f=yt(a,m,c,i,n,!0),f}function yt(a,c,i={},n,e,u){let s;const y=V({actions:{}},i),f={deep:!0};let m,C,T=[],$=[],S;const I=n.state.value[a];!u&&!I&&(n.state.value[a]={}),E({});let h;function G(x){let b;m=C=!1,typeof x=="function"?(x(n.state.value[a]),b={type:W.patchFunction,storeId:a,events:S}):(lt(n.state.value[a],x),b={type:W.patchObject,payload:x,storeId:a,events:S});const N=h=Symbol();Et().then(()=>{h===N&&(m=!0)}),C=!0,R(T,b,n.state.value[a])}const v=u?function(){const{state:b}=i,N=b?b():{};this.$patch(D=>{V(D,N)})}:vt;function q(){s.stop(),T=[],$=[],n._s.delete(a)}const j=(x,b="")=>{if(_t in x)return x[at]=b,x;const N=function(){et(n);const D=Array.from(arguments),H=[],ot=[];function kt(z){H.push(z)}function wt(z){ot.push(z)}R($,{args:D,name:N[at],store:L,after:kt,onError:wt});let K;try{K=x.apply(this&&this.$id===a?this:L,D)}catch(z){throw R(ot,z),z}return K instanceof Promise?K.then(z=>(R(H,z),z)).catch(z=>(R(ot,z),Promise.reject(z))):(R(H,K),K)};return N[_t]=!0,N[at]=b,N},J={_p:n,$id:a,$onAction:dt.bind(null,$),$patch:G,$reset:v,$subscribe(x,b={}){const N=dt(T,x,b.detached,()=>D()),D=s.run(()=>it(()=>n.state.value[a],H=>{(b.flush==="sync"?C:m)&&x({storeId:a,type:W.direct,events:S},H)},V({},f,b)));return N},$dispose:q},L=$t(J);n._s.set(a,L);const U=(n._a&&n._a.runWithContext||Gt)(()=>n._e.run(()=>(s=pt()).run(()=>c({action:j}))));for(const x in U){const b=U[x];if(Y(b)&&!Vt(b)||ft(b))u||(I&&Lt(b)&&(Y(b)?b.value=I[x]:lt(b,I[x])),n.state.value[a][x]=b);else if(typeof b=="function"){const N=j(b,x);U[x]=N,y.actions[x]=b}}return V(L,U),V(It(L),U),Object.defineProperty(L,"$state",{get:()=>n.state.value[a],set:x=>{G(b=>{V(b,x)})}}),n._p.forEach(x=>{V(L,s.run(()=>x({store:L,app:n._a,pinia:n,options:y})))}),I&&u&&i.hydrate&&i.hydrate(L.$state,I),m=!0,C=!0,L}/*! #__NO_SIDE_EFFECTS__ */function Dt(a,c,i){let n;const e=typeof c=="function";n=e?i:c;function u(s,y){const f=Ct();return s=s||(f?St(xt,null):null),s&&et(s),s=bt,s._s.has(a)||(e?yt(a,c,n,s):qt(a,n,s)),s._s.get(a)}return u.$id=a,u}const Rt={class:"fab-container"},Xt={class:"grid grid-cols-12"},Ut={class:"col-span-2"},Ht=["onClick"],Kt={class:"col-span-10 border-l-4 border-primary-600 bg-grid-gray-100 bg-gray-50 option-tab-content",style:{"min-height":"30rem"}},Wt={class:"border border-black/5 px-6 py-4"},Qt={class:"text-lg pb-4 mb-4 border-b border-gray-200"},Jt={__name:"OptionTab",props:{tabs:{type:Array,default:()=>[]}},setup(a){const c=a,{fab_animation:i}=window.FAB_PLUGIN.options,n=E(0),e=E(c.tabs.map(()=>({button:{active:!1}}))),u=s=>{n.value=s,e.value[s].button.active=!1,setTimeout(()=>{e.value[s].button.active=!0},100)};return(s,y)=>(p(),g("div",Rt,[_("div",Xt,[_("div",Ut,[(p(!0),g(A,null,X(a.tabs,(f,m)=>(p(),g("div",{key:m,class:F(["cursor-pointer flex flex-row items-center h-12 px-4 text-gray-400 bg-gray-100 hover:bg-primary-600 hover:text-white fab-option-navigation",{"fab-current-option-navigation":n.value===m}]),onClick:C=>u(m)},[y[0]||(y[0]=_("span",{class:"flex items-center justify-center text-lg"},[_("svg",{xmlns:"http://www.w3.org/2000/svg",class:"h-6 w-6",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor"},[_("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"})])],-1)),_("span",{class:F(["ml-3 animate__animated",e.value[m].button.active?`animate__${t(i).elements.tab}`:""])},M(f.name),3)],10,Ht))),128))]),_("div",Kt,[_("div",Wt,[(p(!0),g(A,null,X(a.tabs,(f,m)=>(p(),g("div",{key:m,class:F(["animate__animated",`animate__${t(i).elements.content}`]),style:Tt({display:n.value===m?"":"none"})},[_("div",Qt,M(f.name),1),(p(),P(Mt(f.component)))],6))),128))])])])]))}},Yt={class:"font-medium text-gray-600 pt-2 flex justify-between"},Zt=["for","innerHTML"],te={class:"col-span-4"},ee={class:"flex"},oe={key:0,class:"text-gray-400 mt-2 field-info"},ae=["innerHTML"],r={__name:"Setting",props:{id:{type:String,required:!0},text:{type:String,required:!0},containerClass:{type:String,default:"grid grid-cols-5 gap-4 py-4 fab-option-container-"},info:{type:String,default:""}},setup(a){return(c,i)=>(p(),g("div",{class:F(a.containerClass)},[_("div",Yt,[_("label",{for:"field_"+a.id,innerHTML:a.text},null,8,Zt),ct(c.$slots,"tooltip")]),_("div",te,[_("div",ee,[ct(c.$slots,"default")]),a.info?(p(),g("div",oe,[_("em",{innerHTML:a.info},null,8,ae)])):B("",!0)])],2))}},ne={class:"py-4 my-4 border-b border-gray-200"},le={class:"text-lg"},ie={key:0,class:"text-gray-400"},se=["innerHTML"],Q={__name:"Heading",props:{text:{type:String,default:""},info:{type:String,default:""}},setup(a){return(c,i)=>(p(),g("div",ne,[_("span",le,M(a.text),1),a.info?(p(),g("div",ie,[_("em",{innerHTML:a.info},null,8,se)])):B("",!0)]))}},ue=["id","name","data-selected"],ce=["value"],w={__name:"Select",props:{id:{type:String,default:""},value:{type:String,default:""},name:{type:String,default:""},inputClass:{type:String,default:"select2"},selectOptions:{type:Array,default:()=>[]}},setup(a){return(c,i)=>(p(),g("select",{id:`field_${a.id}`,name:a.name,class:F(a.inputClass),"data-selected":a.value},[(p(!0),g(A,null,X(a.selectOptions,n=>(p(),g("option",{key:n.id,value:n.id},M(n.text),9,ce))),128))],10,ue))}},re=["for"],de={class:"relative"},_e=["id","data-option","checked"],pe=["name","id","value"],me={class:"pl-2",style:{"padding-top":"2px"}},O={__name:"Switch",props:{id:{type:String,default:""},value:{type:[Number,Boolean,String],default:0},name:{type:String,default:""},inputClass:{type:String,default:"flex"},label:{type:Object,default:()=>({})}},emits:["update:value"],setup(a,{emit:c}){const i=a,n=c,e=f=>typeof f!="string"?!1:!isNaN(f)&&!isNaN(parseFloat(f)),u=f=>{let m=e(f)?parseInt(f):f;return m===1||m===!0||m==="true"?1:0},s=E(u(i.value));it(()=>i.value,f=>{s.value=u(f)});const y=f=>{const m=f.target.checked?1:0;s.value=m,n("update:value",m)};return(f,m)=>(p(),g("div",{class:F(a.inputClass)},[_("label",{for:`switch_${a.id}`,class:"flex cursor-pointer"},[_("div",de,[_("input",{type:"checkbox",id:`switch_${a.id}`,class:"option_settings switch sr-only","data-option":`field_${a.id}`,checked:s.value,onChange:y},null,40,_e),m[0]||(m[0]=_("div",{class:"fab absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"},null,-1)),m[1]||(m[1]=_("div",{class:"block bg-gray-300 w-10 h-6 rounded-full"},null,-1))])],8,re),_("input",{type:"hidden",name:a.name,id:`field_${a.id}`,value:s.value},null,8,pe),_("span",me,M(a.label.text),1)],2))}},fe=["id","name","value","required","placeholder"],k={__name:"Text",props:{id:{type:String,default:""},value:{type:String,default:""},name:{type:String,default:""},required:{type:Boolean,default:void 0},placeholder:{type:String,default:void 0},inputClass:{type:String,default:"border border-gray-200 py-2 px-3 text-grey-darkest w-full"}},emits:["click","update:value"],setup(a,{emit:c}){const i=c,n=()=>{i("click")},e=u=>{i("update:value",u.target.value)};return(u,s)=>(p(),g("input",{type:"text",id:`field_${a.id}`,name:a.name,class:F(a.inputClass),value:a.value,required:a.required,placeholder:a.placeholder,onClick:n,onInput:e},null,42,fe))}},ge={class:"modal-content-fab-icon-picker"},be={class:"title-fab-icon-picker mb-2"},xe=["placeholder"],ve={class:"fab-icon-picker"},ye=["onClick"],he={class:"modal-action-fab-icon-picker mt-2"},ke={__name:"IconPicker",props:{selectedIcon:{type:String,default:""}},emits:["select","delete","cancel"],setup(a,{expose:c,emit:i}){const n=a,e=i,u=E(!1),s=E(""),y=E([]),f=E(n.selectedIcon),{labels:m}=window.FAB_METABOX_DESIGN,C=Z(()=>y.value.filter(v=>v.toLowerCase().includes(s.value.toLowerCase())));gt(async()=>{try{const{path:v}=window.FAB_PLUGIN,{plugin_url:q}=v,J=await(await fetch(`${q}/assets/build/json/fontAwesomeIcons.json`)).json();y.value=J}catch(v){console.error("Error loading icons:",v)}});const T=v=>{f.value=v,e("select",{icon:f.value,selected:!1})},$=()=>{s.value="",u.value=!0},S=()=>{u.value=!1},I=()=>{e("delete"),S()},h=()=>{e("cancel"),S()},G=()=>{e("select",{icon:f.value,selected:!0}),S()};return c({openModal:$}),(v,q)=>(p(),g("div",{class:F(["modal-fab-icon-picker",{show:u.value}])},[_("div",ge,[_("p",be,M(t(m).button.icon.picker.title),1),st(_("input",{class:"fab-icon-picker-search mb-2 p-2 w-full",type:"text",placeholder:t(m).button.icon.picker.search_placeholder,"onUpdate:modelValue":q[0]||(q[0]=j=>s.value=j)},null,8,xe),[[ut,s.value]]),_("div",ve,[(p(!0),g(A,null,X(C.value,j=>(p(),g("span",{key:j,class:F(["fab-icon",j,{selected:j===f.value}]),onClick:J=>T(j)},null,10,ye))),128))]),_("div",he,[_("button",{onClick:I,class:"button button-link-delete mr-2"},M(t(m).button.icon.picker.delete),1),_("button",{onClick:h,class:"button button-cancel mr-2"},M(t(m).button.icon.picker.cancel),1),_("button",{onClick:G,class:"button button-primary"},M(t(m).button.icon.picker.select),1)])])],2))}},d={__name:"Tooltip",props:{text:{type:String,default:""},position:{type:String,default:"top"},tooltipClass:{type:String,default:"fab-bg-black fab-text-white fab-text-xs fab-rounded fab-py-1 fab-px-2 fab-absolute fab-z-10"}},setup(a){const c=E(!1),i=()=>{c.value=!0},n=()=>{c.value=!1};return(e,u)=>(p(),g("div",{class:"fab-relative fab-inline-block",onMouseenter:i,onMouseleave:n},[u[0]||(u[0]=_("i",{class:"fas fa-question-circle fab-cursor-pointer"},null,-1)),c.value?(p(),g("div",{key:0,class:F(["fab-tooltip",`fab-${a.position}`,a.tooltipClass])},M(a.text),3)):B("",!0)],32))}},we={__name:"Button",setup(a){const{premium:c}=window.FAB_PLUGIN,{fab_design:i}=window.FAB_PLUGIN.options,{fab:n}=window.FAB_METABOX_DESIGN.data,{labels:e}=window.FAB_METABOX_DESIGN,u=E(n.icon_class),s=E(n.icon_class),y=E(null);n.template.shape=n.template.shape||i.template.shape;const f=$=>{u.value=$.icon,n.icon_class=u.value,$.selected&&(s.value=u.value)},m=()=>{u.value="",n.icon_class=u.value,s.value=u.value},C=()=>{u.value=s.value,n.icon_class=u.value},T=()=>{y.value.openModal()};return($,S)=>(p(),g(A,null,[o(r,{id:"option_design_template_color",text:t(e).button.color.text},{tooltip:l(()=>[o(d,{text:t(e).button.color.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"option_design_template_color",name:"fab_design_template[color]",inputClass:"colorpicker",value:t(n).template.color},null,8,["value"])]),_:1},8,["text"]),t(i).template.name==="shape"?(p(),P(r,{key:0,id:"option_design_template_shape",text:t(e).button.shape.text,info:t(e).button.shape.info},{tooltip:l(()=>[o(d,{text:t(e).button.shape.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"option_design_template_shape",name:"fab_design_template[shape]",inputClass:"field_option_design_template_shape select2",value:t(n).template.shape},null,8,["value"])]),_:1},8,["text","info"])):B("",!0),t(c)?(p(),P(r,{key:1,text:t(e).button.responsive.text},{tooltip:l(()=>[o(d,{text:t(e).button.responsive.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"design_responsive_mobile",name:"fab_design_responsive[device][mobile]",label:{text:t(e).button.responsive.switchs.mobile},value:t(n).responsive.device.mobile},null,8,["label","value"]),o(O,{id:"design_responsive_tablet",name:"fab_design_responsive[device][tablet]",label:{text:t(e).button.responsive.switchs.tablet},inputClass:"flex pl-6",value:t(n).responsive.device.tablet},null,8,["label","value"]),o(O,{id:"design_responsive_desktop",name:"fab_design_responsive[device][desktop]",label:{text:t(e).button.responsive.switchs.desktop},inputClass:"flex pl-6",value:t(n).responsive.device.desktop},null,8,["label","value"])]),_:1},8,["text"])):B("",!0),o(r,{text:t(e).button.standalone.text,info:t(e).button.standalone.info},{tooltip:l(()=>[o(d,{text:t(e).button.standalone.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_design_standalone",name:"fab_design_standalone",label:{text:t(e).button.standalone.enable},value:t(n).standalone},null,8,["label","value"])]),_:1},8,["text","info"]),t(c)?(p(),P(r,{key:2,id:"option_setting_hotkey",text:t(e).button.hotkey.text,info:t(e).button.hotkey.info},{tooltip:l(()=>[o(d,{text:t(e).button.hotkey.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_hotkey",name:"fab_setting_hotkey",inputClass:"select2",value:t(n).hotkey},null,8,["value"])]),_:1},8,["text","info"])):B("",!0),o(Q,{text:t(e).button.icon.text,info:t(e).button.icon.info},null,8,["text","info"]),o(r,{id:"option_design_icon_class",text:t(e).button.icon.class},{tooltip:l(()=>[o(d,{text:t(e).button.icon.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"option_design_icon_class",name:"fab_design_icon_class",value:t(n).icon_class,onClick:T},null,8,["value"]),o(ke,{ref_key:"iconPickerRef",ref:y,selectedIcon:u.value,"onUpdate:selectedIcon":S[0]||(S[0]=I=>u.value=I),onSelect:f,onDelete:m,onCancel:C},null,8,["selectedIcon"])]),_:1},8,["text"]),o(r,{id:"option_design_icon_color",text:t(e).button.icon.color},{tooltip:l(()=>[o(d,{text:t(e).button.icon.tooltip_color},null,8,["text"])]),default:l(()=>[o(k,{id:"option_design_icon_color",name:"fab_design_template[icon][color]",inputClass:"colorpicker",value:t(n).template.icon.color},null,8,["value"])]),_:1},8,["text"]),o(Q,{text:t(e).button.tooltip.text,info:t(e).button.tooltip.info},null,8,["text","info"]),o(r,{text:t(e).button.tooltip.always_display},{tooltip:l(()=>[o(d,{text:t(e).button.tooltip.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"option_design_tooltip_alwaysdisplay",name:"fab_design_tooltip[alwaysdisplay]",label:{text:t(e).button.tooltip.enable},value:t(n).tooltip.alwaysdisplay},null,8,["label","value"])]),_:1},8,["text"]),o(r,{id:"option_design_tooltip_font_color",text:t(e).button.tooltip.font_color},{tooltip:l(()=>[o(d,{text:t(e).button.tooltip.tooltip_font_color},null,8,["text"])]),default:l(()=>[o(k,{id:"option_design_tooltip_font_color",name:"fab_design_tooltip[font][color]",inputClass:"colorpicker",value:t(n).tooltip.font.color},null,8,["value"])]),_:1},8,["text"])],64))}},Se={__name:"Cookie",setup(a){const{fab:c}=window.FAB_METABOX_TRIGGER.data,{labels:i}=window.FAB_METABOX_TRIGGER;return(n,e)=>(p(),P(r,{id:"field_option_trigger_expiration",text:t(i).cookie.expiration.text},{tooltip:l(()=>[o(d,{text:t(i).cookie.expiration.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"field_option_trigger_expiration",name:"fab_trigger[cookie][expiration]",value:t(c).trigger.cookie.expiration,placeholder:"30"},null,8,["value"])]),_:1},8,["text"]))}},ht=Dt("metaboxSettingModalStore",{state:()=>({layout:{content:{margin:{top:"0",right:"0",bottom:"0",left:"0",sizing:"rem"},padding:{top:"1",right:"1",bottom:"1",left:"1",sizing:"rem"}},overlay:{color:"",opacity:"0.5"}},dataAttributes:{}}),getters:{},actions:{}}),Ce=["id","name","required","placeholder","step"],tt={__name:"Number",props:{id:{type:String,default:""},direction:{type:String,default:""},type:{type:String,default:""},value:{type:String,default:""},name:{type:String,default:""},step:{type:[String,Number],default:"any"},required:{type:Boolean,default:void 0},placeholder:{type:String,default:void 0},inputClass:{type:String,default:"border border-gray-200 py-2 px-3 text-grey-darkest w-full"},data:{type:Object,default:()=>({})}},setup(a){const c=a,i=ht(),n=Z(()=>c.type==="padding"?i.layout.content.padding[c.direction]:i.layout.content.margin[c.direction]),e=Z(()=>c.data?Object.keys(c.data).reduce((u,s)=>(u[`data-${s}`]=c.data[s],u),{}):{});return(u,s)=>st((p(),g("input",Nt({type:"number",id:`field_${a.id}`,name:a.name,"onUpdate:modelValue":s[0]||(s[0]=y=>n.value=y),required:a.required,placeholder:a.placeholder,step:a.step,class:a.inputClass},e.value),null,16,Ce)),[[ut,n.value]])}},$e=["id","name","min","max","step","required","placeholder"],Ie={class:"pl-4 mt-2"},Be={__name:"Range",props:{id:{type:String,default:""},modelValue:{type:[String,Number],default:0},name:{type:String,default:""},min:{type:[String,Number],default:0},max:{type:[String,Number],default:1},step:{type:[String,Number],default:.1},inputClass:{type:String,default:"slider"}},setup(a){const c=a,i=E(c.modelValue);return it(()=>c.modelValue,n=>{i.value=n}),(n,e)=>(p(),g(A,null,[st(_("input",{type:"range",id:`field_${a.id}`,name:a.name,min:a.min,max:a.max,step:a.step,required:n.required,placeholder:n.placeholder,"onUpdate:modelValue":e[0]||(e[0]=u=>i.value=u),class:F(["mt-4",a.inputClass])},null,10,$e),[[ut,i.value]]),_("div",Ie,[e[1]||(e[1]=rt(" (")),_("span",null,M(i.value),1),e[2]||(e[2]=rt(") "))])],64))}},Ae={id:"setting_design_custom_size"},Ee={class:"ml-4 w-20"},Oe={class:"ml-4 w-20"},Te={__name:"Modal",setup(a){const{premium:c}=window.FAB_PLUGIN,{fab_design:i}=window.FAB_PLUGIN.options,{fab:n}=window.FAB_METABOX_DESIGN.data,{labels:e}=window.FAB_METABOX_DESIGN,{navigation:u,layout:s,theme:y}=n.modal,f=ht(),m=["auth_login","auth_logout","modal","search","modal_widget","widget"],C=E(m.includes(n.type)),T=[{id:"px",text:"PX"},{id:"em",text:"EM"},{id:"%",text:"%"},{id:"rem",text:"REM"},{id:"vw",text:"VW"},{id:"vh",text:"VH"}],$=S=>{C.value=m.includes(S.detail.type)};return gt(()=>{window.addEventListener("settingTypeChanged",$),f.layout=s,setTimeout(()=>{window.FAB_METABOX_DESIGN.init_option_animation(),window.FAB_METABOX_DESIGN.init_option_modal_size(),window.FAB_METABOX_DESIGN.init_option_spacing(),window.FAB_METABOX_DESIGN.init_option_layout(),window.FAB_METABOX_DESIGN.init_option_theme(),window.FAB_METABOX_DESIGN.init_option_template(),jQuery(".fab-container.metabox-design .colorpicker").wpColorPicker()},80)}),zt(()=>{window.removeEventListener("settingTypeChanged",$)}),(S,I)=>C.value?(p(),g(A,{key:0},[t(c)?(p(),P(r,{key:0,id:"option_design_modal_theme",text:t(e).modal.theme.text},{tooltip:l(()=>[o(d,{text:t(e).modal.theme.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"option_design_modal_theme",name:"fab_modal_theme[id]",inputClass:"select2 field_option_design_modal_theme",value:t(y).id},null,8,["value"])]),_:1},8,["text"])):B("",!0),o(r,{id:"option_design_modal_layout_id",text:t(e).modal.layout.text},{tooltip:l(()=>[o(d,{text:t(e).modal.layout.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"option_design_modal_layout_id",name:"fab_modal_layout[id]",inputClass:"select2 field_option_design_modal_layout_id",value:t(s).id},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"option_design_modal_size",text:t(e).modal.size.text},{tooltip:l(()=>[o(d,{text:t(e).modal.size.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"option_design_modal_size",name:"fab_design_size_type",inputClass:"select2 field_option_design_modal_size",value:t(n).size.type},null,8,["value"])]),_:1},8,["text"]),_("div",Ae,[o(r,{id:"option_design_modal_size",text:t(e).modal.custom_size.text},{tooltip:l(()=>[o(d,{text:t(e).modal.custom_size.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"option_design_modal_size",name:"fab_design_size_custom",placeholder:t(e).modal.custom_size.placeholder,value:t(n).size.custom},null,8,["placeholder","value"])]),_:1},8,["text"])]),o(r,{text:t(e).modal.navigation.text},{tooltip:l(()=>[o(d,{text:t(e).modal.navigation.tooltip},null,8,["text"])]),default:l(()=>{var h;return[o(O,{id:"fab_modal_navigation_backgroundDismiss",name:"fab_modal_navigation[backgroundDismiss]",label:{text:"backgroundDismiss"},value:t(u).backgroundDismiss},null,8,["value"]),o(O,{id:"fab_modal_navigation_buttons_maximize",name:"fab_modal_navigation[buttons][maximize]",label:{text:"Maximize"},inputClass:"flex pl-6",value:(h=t(u).buttons)==null?void 0:h.maximize},null,8,["value"]),o(O,{id:"fab_modal_navigation_draggable",name:"fab_modal_navigation[draggable]",label:{text:"Draggable"},inputClass:"flex pl-6",value:t(u).draggable},null,8,["value"]),o(O,{id:"fab_modal_navigation_escapeKey",name:"fab_modal_navigation[escapeKey]",label:{text:"escapeKey"},inputClass:"flex pl-6",value:t(u).escapeKey},null,8,["value"])]}),_:1},8,["text"]),o(r,{id:"modal_layout_background_color",text:t(e).modal.background_color.text},{tooltip:l(()=>[o(d,{text:t(e).modal.background_color.tooltip},null,8,["text"])]),default:l(()=>{var h;return[o(k,{id:"modal_layout_background_color",name:"fab_modal_layout[background][color]",inputClass:"colorpicker",value:(h=t(s).background)==null?void 0:h.color},null,8,["value"])]}),_:1},8,["text"]),t(c)?(p(),g(A,{key:1},[o(Q,{text:t(e).modal.animation.text,info:t(e).modal.animation.info},null,8,["text","info"]),o(r,{id:"option_animation_modal_in",text:t(e).modal.animation.in.text},{tooltip:l(()=>[o(d,{text:t(e).modal.animation.in.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"option_animation_modal_in",name:"fab_design_animation[modal][in]",inputClass:"field_option_animation_element select2",value:t(n).animation.modal.in},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"option_animation_modal_out",text:t(e).modal.animation.out.text},{tooltip:l(()=>[o(d,{text:t(e).modal.animation.out.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"option_animation_modal_out",name:"fab_design_animation[modal][out]",inputClass:"field_option_animation_element select2",value:t(n).animation.modal.out},null,8,["value"])]),_:1},8,["text"])],64)):B("",!0),o(Q,{text:t(e).modal.overlay.text,info:t(e).modal.overlay.info},null,8,["text","info"]),o(r,{id:"modal_layout_overlay_color",text:t(e).modal.overlay.background_color.text},{tooltip:l(()=>[o(d,{text:t(e).modal.overlay.background_color.tooltip},null,8,["text"])]),default:l(()=>{var h;return[o(k,{id:"modal_layout_overlay_color",name:"fab_modal_layout[overlay][color]",inputClass:"colorpicker",value:(h=t(s).overlay)==null?void 0:h.color},null,8,["value"])]}),_:1},8,["text"]),o(r,{id:"fab_modal_layout_overlay_opacity",text:t(e).modal.overlay.opacity.text},{tooltip:l(()=>[o(d,{text:t(e).modal.overlay.opacity.tooltip},null,8,["text"])]),default:l(()=>[o(Be,{id:"fab_modal_layout_overlay_opacity",name:"fab_modal_layout[overlay][opacity]",min:"0",max:"1",step:"0.1",modelValue:t(s).overlay.opacity,"onUpdate:modelValue":I[0]||(I[0]=h=>t(s).overlay.opacity=h)},null,8,["modelValue"])]),_:1},8,["text"]),o(Q,{text:t(e).modal.spacing.text,info:t(e).modal.spacing.info},null,8,["text","info"]),o(r,{text:t(e).modal.spacing.padding.text},{tooltip:l(()=>[o(d,{text:t(e).modal.spacing.padding.tooltip},null,8,["text"])]),default:l(()=>{var h,G;return[(p(),g(A,null,X(["top","right","bottom","left"],v=>o(tt,{key:v,direction:v,type:"padding",id:`fab_modal_layout_content_padding_${v}`,name:`fab_modal_layout[content][padding][${v}]`,inputClass:`border border-gray-200 p-2 text-grey-darkest w-20 ${v!=="top"?"ml-4":""} fab-modal-layout-spacing fab-modal-layout-padding`,value:t(f).layout.content.padding[v],data:{layout:"padding"}},null,8,["direction","id","name","inputClass","value"])),64)),_("div",Ee,[o(w,{id:"fab_modal_layout_content_padding_sizing",name:"fab_modal_layout[content][padding][sizing]",inputClass:"select2 fab_modal_layout_spacing_sizing",value:(G=(h=t(s).content)==null?void 0:h.padding)==null?void 0:G.sizing,selectOptions:T},null,8,["value"])]),I[1]||(I[1]=_("div",{class:"pt-2.5 px-6 ml-4 bg-primary-600 text-white rounded-md cursor-pointer fab-linked-option hover:shadow-md","data-layout":"padding"},[_("em",{class:"fas fa-link"})],-1))]}),_:1},8,["text"]),o(r,{text:t(e).modal.spacing.margin.text},{tooltip:l(()=>[o(d,{text:t(e).modal.spacing.margin.tooltip},null,8,["text"])]),default:l(()=>{var h,G;return[(p(),g(A,null,X(["top","right","bottom","left"],v=>o(tt,{key:v,direction:v,type:"margin",id:`fab_modal_layout_content_margin_${v}`,name:`fab_modal_layout[content][margin][${v}]`,inputClass:`border border-gray-200 p-2 text-grey-darkest w-20 ${v!=="top"?"ml-4":""} fab-modal-layout-spacing fab-modal-layout-margin`,value:t(f).layout.content.margin[v],data:{layout:"margin"}},null,8,["direction","id","name","inputClass","value"])),64)),_("div",Oe,[o(w,{id:"fab_modal_layout_content_margin_sizing",name:"fab_modal_layout[content][margin][sizing]",inputClass:"select2 fab_modal_layout_spacing_sizing",value:(G=(h=t(s).content)==null?void 0:h.margin)==null?void 0:G.sizing,selectOptions:T},null,8,["value"])]),I[2]||(I[2]=_("div",{class:"pt-2.5 px-6 ml-4 bg-primary-600 text-white rounded-md cursor-pointer fab-linked-option hover:shadow-md","data-layout":"margin"},[_("em",{class:"fas fa-link"})],-1))]}),_:1},8,["text"])],64)):(p(),g(A,{key:1},[_("p",null,M(t(e).modal.no_modal.text),1),_("i",null,M(t(e).modal.no_modal.info),1)],64))}},Me={__name:"Setting",setup(a){const{premium:c}=window.FAB_PLUGIN,{fab_design:i}=window.FAB_PLUGIN.options,{fab:n}=window.FAB_METABOX_DESIGN.data,{labels:e,data:u}=window.FAB_METABOX_SETTING,s=E(n.type),y=[{id:"top",text:"Top"},{id:"bottom",text:"Bottom"}],f=[{id:"left",text:"Left"},{id:"center",text:"Center"},{id:"right",text:"Right"}];n.template.shape=n.template.shape||i.template.shape;const m=()=>{const C=jQuery("#field_fab_setting_type").val();s.value=C,(s.value==="toast"||s.value==="scarcity_toast")&&setTimeout(()=>{jQuery(".colorpicker").wpColorPicker()},80),window.dispatchEvent(new CustomEvent("settingTypeChanged",{detail:{type:C}}))};return(C,T)=>(p(),g(A,null,[o(r,{id:"fab_setting_type",text:t(e).setting.type.text},{tooltip:l(()=>[o(d,{text:t(e).setting.type.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_type",name:"fab_setting_type",inputClass:"select2",value:t(n).type},null,8,["value"]),_("div",{id:"fab_setting_type_update",class:"hidden",onClick:m})]),_:1},8,["text"]),["link","anchor_link"].includes(s.value)?(p(),g(A,{key:0},[o(r,{id:"fab_setting_link",text:t(e).setting.link.text,info:t(e).setting.link.info},{tooltip:l(()=>[o(d,{text:t(e).setting.link.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_link",name:"fab_setting_link",value:t(n).link,required:!0},null,8,["value"])]),_:1},8,["text","info"]),s.value!=="anchor_link"?(p(),P(r,{key:0,id:"fab_setting_link_behavior",text:t(e).setting.anchor_link.text},{tooltip:l(()=>[o(d,{text:t(e).setting.anchor_link.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_setting_link_behavior",name:"fab_setting_link_behavior",label:{text:""},value:t(n).linkBehavior},null,8,["value"])]),_:1},8,["text"])):B("",!0)],64)):B("",!0),s.value==="print"?(p(),P(r,{key:1,id:"fab_setting_print_target",text:t(e).setting.print.text,info:t(e).setting.print.info},{tooltip:l(()=>[o(d,{text:t(e).setting.print.tooltip},null,8,["text"])]),default:l(()=>{var $,S;return[o(k,{id:"fab_setting_print_target",name:"fab_setting_print_target",value:((S=($=t(n).extraOptions)==null?void 0:$.print)==null?void 0:S.target)||"body",required:!0},null,8,["value"])]}),_:1},8,["text","info"])):B("",!0),s.value==="toast"?(p(),g(A,{key:2},[o(r,{id:"fab_setting_duration_toast",text:t(e).setting.toast.duration.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.duration.tooltip},null,8,["text"])]),default:l(()=>[o(tt,{id:"fab_setting_duration_toast",name:"fab_setting_toast[duration]",value:t(n).toast.duration,placeholder:"3000"},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_text_button_toast",text:t(e).setting.toast.text_button.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.text_button.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_text_button_toast",name:"fab_setting_toast[text_button]",value:t(n).toast.text_button,placeholder:""},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_url_button_toast",text:t(e).setting.toast.url_button.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.url_button.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_url_button_toast",name:"fab_setting_toast[url_button]",value:t(n).toast.url_button,placeholder:"https://artistudio.xyz"},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_window_toast",text:t(e).setting.toast.window.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.window.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_setting_window_toast",name:"fab_setting_toast[window]",label:{text:""},value:t(n).toast.window},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_closeable_toast",text:t(e).setting.toast.closeable.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.closeable.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_setting_closeable_toast",name:"fab_setting_toast[closeable]",label:{text:""},value:t(n).toast.closeable},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_remember_on_click_toast",text:t(e).setting.toast.remember_on_click.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.remember_on_click.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_setting_remember_on_click_toast",name:"fab_setting_toast[remember_on_click]",label:{text:""},value:t(n).toast.remember_on_click},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_gravity_toast",text:t(e).setting.toast.gravity.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.gravity.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_gravity_toast",name:"fab_setting_toast[gravity]",inputClass:"select2 fab_modal_layout_spacing_sizing",value:t(n).toast.gravity,selectOptions:y},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_position_toast",text:t(e).setting.toast.position.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.position.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_position_toast",name:"fab_setting_toast[position]",inputClass:"select2 fab_modal_layout_spacing_sizing",value:t(n).toast.position,selectOptions:f},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_background_toast",text:t(e).setting.toast.background.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.background.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_background_toast",name:"fab_setting_toast[background]",inputClass:"colorpicker",value:t(n).toast.background},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_text_color_toast",text:t(e).setting.toast.text_color.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.text_color.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_text_color_toast",name:"fab_setting_toast[text_color]",inputClass:"colorpicker",value:t(n).toast.text_color},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_bar_color_toast",text:t(e).setting.toast.bar_color.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.bar_color.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_bar_color_toast",name:"fab_setting_toast[bar_color]",inputClass:"colorpicker",value:t(n).toast.bar_color},null,8,["value"])]),_:1},8,["text"])],64)):B("",!0),s.value==="featured_product"?(p(),P(r,{key:3,id:"fab_setting_featured_product",text:t(e).setting.woocommerce.featured_product.text},{tooltip:l(()=>[o(d,{text:t(e).setting.woocommerce.featured_product.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_featured_product",name:"fab_setting_woocommerce_featured_product",inputClass:"select2",value:`${t(u).fab.woocommerce.featured_product.id}-${t(u).fab.woocommerce.featured_product.text}`},null,8,["value"])]),_:1},8,["text"])):B("",!0),s.value==="apply_coupon"?(p(),P(r,{key:4,id:"fab_setting_apply_coupon",text:t(e).setting.woocommerce.apply_coupon.text},{tooltip:l(()=>[o(d,{text:t(e).setting.woocommerce.apply_coupon.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_apply_coupon",name:"fab_setting_woocommerce_apply_coupon",inputClass:"select2",value:`${t(u).fab.woocommerce.apply_coupon.id}-${t(u).fab.woocommerce.apply_coupon.text}`},null,8,["value"])]),_:1},8,["text"])):B("",!0),s.value==="quick_purchase"?(p(),P(r,{key:5,id:"fab_setting_quick_purchase",text:t(e).setting.woocommerce.quick_purchase.text},{tooltip:l(()=>[o(d,{text:t(e).setting.woocommerce.quick_purchase.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_quick_purchase",name:"fab_setting_woocommerce_quick_purchase",inputClass:"select2 fab_option_settings",value:t(u).fab.woocommerce.quick_purchase,selectOptions:t(e).setting.woocommerce.quick_purchase.options},null,8,["value","selectOptions"])]),_:1},8,["text"])):B("",!0),s.value==="scarcity_toast"?(p(),g(A,{key:6},[o(r,{id:"fab_setting_scarcity_toast_product_container",text:t(e).setting.woocommerce.scarcity_toast.products.text},{tooltip:l(()=>[o(d,{text:t(e).setting.woocommerce.scarcity_toast.products.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_scarcity_toast_product",name:"fab_setting_woocommerce_scarcity_toast_product",inputClass:"select2",value:`${t(u).fab.woocommerce.scarcity_toast.products.id}-${t(u).fab.woocommerce.scarcity_toast.products.text}`},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_scarcity_toast_product_action_container",text:t(e).setting.woocommerce.scarcity_toast.actions.text},{tooltip:l(()=>[o(d,{text:t(e).setting.woocommerce.scarcity_toast.actions.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_scarcity_toast_product_action",name:"fab_setting_woocommerce_scarcity_toast_action",inputClass:"select2 fab_option_settings",value:t(u).fab.woocommerce.scarcity_toast.actions,selectOptions:t(e).setting.woocommerce.scarcity_toast.actions.options},null,8,["value","selectOptions"])]),_:1},8,["text"]),o(r,{id:"fab_setting_duration_toast",text:t(e).setting.toast.duration.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.duration.tooltip},null,8,["text"])]),default:l(()=>[o(tt,{id:"fab_setting_duration_toast",name:"fab_setting_toast[duration]",value:t(n).toast.duration,placeholder:"3000"},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_text_button_toast",text:t(e).setting.toast.text_button.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.text_button.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_text_button_toast",name:"fab_setting_toast[text_button]",value:t(n).toast.text_button,placeholder:""},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_window_toast",text:t(e).setting.toast.window.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.window.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_setting_window_toast",name:"fab_setting_toast[window]",label:{text:""},value:t(n).toast.window},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_closeable_toast",text:t(e).setting.toast.closeable.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.closeable.tooltip},null,8,["text"])]),default:l(()=>[o(O,{id:"fab_setting_closeable_toast",name:"fab_setting_toast[closeable]",label:{text:""},value:t(n).toast.closeable},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_gravity_toast",text:t(e).setting.toast.gravity.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.gravity.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_gravity_toast",name:"fab_setting_toast[gravity]",inputClass:"select2 fab_modal_layout_spacing_sizing",value:t(n).toast.gravity,selectOptions:y},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_position_toast",text:t(e).setting.toast.position.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.position.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"fab_setting_position_toast",name:"fab_setting_toast[position]",inputClass:"select2 fab_modal_layout_spacing_sizing",value:t(n).toast.position,selectOptions:f},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_background_toast",text:t(e).setting.toast.background.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.background.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_background_toast",name:"fab_setting_toast[background]",inputClass:"colorpicker",value:t(n).toast.background},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_text_color_toast",text:t(e).setting.toast.text_color.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.text_color.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_text_color_toast",name:"fab_setting_toast[text_color]",inputClass:"colorpicker",value:t(n).toast.text_color},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"fab_setting_bar_color_toast",text:t(e).setting.toast.bar_color.text},{tooltip:l(()=>[o(d,{text:t(e).setting.toast.bar_color.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"fab_setting_bar_color_toast",name:"fab_setting_toast[bar_color]",inputClass:"colorpicker",value:t(n).toast.bar_color},null,8,["value"])]),_:1},8,["text"])],64)):B("",!0)],64))}},Ne={__name:"Trigger",setup(a){const{fab:c}=window.FAB_METABOX_TRIGGER.data,{labels:i}=window.FAB_METABOX_TRIGGER;return(n,e)=>(p(),g(A,null,[o(r,{id:"field_option_trigger_type",text:t(i).trigger.type.text},{tooltip:l(()=>[o(d,{text:t(i).trigger.type.tooltip},null,8,["text"])]),default:l(()=>[o(w,{id:"field_option_trigger_type",name:"fab_trigger[type]",inputClass:"field_option_trigger_type_option select2",value:t(c).trigger.type},null,8,["value"])]),_:1},8,["text"]),o(r,{id:"field_option_trigger_delay",text:t(i).trigger.delay.text},{tooltip:l(()=>[o(d,{text:t(i).trigger.delay.tooltip},null,8,["text"])]),default:l(()=>[o(k,{id:"field_option_trigger_delay",name:"fab_trigger[delay]",value:t(c).trigger.delay,placeholder:"1000ms"},null,8,["value"])]),_:1},8,["text"])],64))}},ze={class:"fab-container metabox-setting"},Pe={__name:"App",setup(a){const c=[{key:"setting",name:"Setting",component:Me},{key:"button",name:"Button",component:we},{key:"modal",name:"Modal",component:Te},{key:"trigger",name:"Trigger",component:Ne},{key:"cookie",name:"Cookie",component:Se}];return(i,n)=>(p(),g("div",ze,[o(Jt,{tabs:c})]))}},Fe=Pt(Pe);Fe.use(Ft()).mount("#fab-metabox-setting-content");
