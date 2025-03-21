var app=function(){"use strict";function t(){}function e(t){return t()}function n(){return Object.create(null)}function o(t){t.forEach(e)}function r(t){return"function"==typeof t}function s(t,e){return t!=t?e==e:t!==e||t&&"object"==typeof t||"function"==typeof t}let i,c;function a(t,e){t.appendChild(e)}function u(t,e,n){t.insertBefore(e,n||null)}function f(t){t.parentNode&&t.parentNode.removeChild(t)}function d(t){return document.createElement(t)}function l(){return t=" ",document.createTextNode(t);var t}function p(t,e,n,o){return t.addEventListener(e,n,o),()=>t.removeEventListener(e,n,o)}function h(t,e,n){null==n?t.removeAttribute(e):t.getAttribute(e)!==n&&t.setAttribute(e,n)}function g(t,e,n){t.classList[n?"add":"remove"](e)}class m{constructor(t=!1){this.is_svg=!1,this.is_svg=t,this.e=this.n=null}c(t){this.h(t)}m(t,e,n=null){var o;this.e||(this.is_svg?this.e=(o=e.nodeName,document.createElementNS("http://www.w3.org/2000/svg",o)):this.e=d(11===e.nodeType?"TEMPLATE":e.nodeName),this.t="TEMPLATE"!==e.tagName?e:e.content,this.c(t)),this.i(n)}h(t){this.e.innerHTML=t,this.n=Array.from("TEMPLATE"===this.e.nodeName?this.e.content.childNodes:this.e.childNodes)}i(t){for(let e=0;e<this.n.length;e+=1)u(this.t,this.n[e],t)}p(t){this.d(),this.h(t),this.i(this.a)}d(){this.n.forEach(f)}}function $(t){c=t}function b(t){(function(){if(!c)throw new Error("Function called outside component initialization");return c})().$$.on_mount.push(t)}const _=[],y=[];let E=[];const w=[],v=Promise.resolve();let x=!1;function A(t){E.push(t)}const L=new Set;let T=0;function k(){if(0!==T)return;const t=c;do{try{for(;T<_.length;){const t=_[T];T++,$(t),O(t.$$)}}catch(t){throw _.length=0,T=0,t}for($(null),_.length=0,T=0;y.length;)y.pop()();for(let t=0;t<E.length;t+=1){const e=E[t];L.has(e)||(L.add(e),e())}E.length=0}while(_.length);for(;w.length;)w.pop()();x=!1,L.clear(),$(t)}function O(t){if(null!==t.fragment){t.update(),o(t.before_update);const e=t.dirty;t.dirty=[-1],t.fragment&&t.fragment.p(t.ctx,e),t.after_update.forEach(A)}}const P=new Set;function B(t,e){t&&t.i&&(P.delete(t),t.i(e))}function M(t,n,s,i){const{fragment:c,after_update:a}=t.$$;c&&c.m(n,s),i||A((()=>{const n=t.$$.on_mount.map(e).filter(r);t.$$.on_destroy?t.$$.on_destroy.push(...n):o(n),t.$$.on_mount=[]})),a.forEach(A)}function N(t,e){const n=t.$$;null!==n.fragment&&(!function(t){const e=[],n=[];E.forEach((o=>-1===t.indexOf(o)?e.push(o):n.push(o))),n.forEach((t=>t())),E=e}(n.after_update),o(n.on_destroy),n.fragment&&n.fragment.d(e),n.on_destroy=n.fragment=null,n.ctx=[])}function S(t,e){-1===t.$$.dirty[0]&&(_.push(t),x||(x=!0,v.then(k)),t.$$.dirty.fill(0)),t.$$.dirty[e/31|0]|=1<<e%31}function j(e,r,s,i,a,u,d,l=[-1]){const p=c;$(e);const h=e.$$={fragment:null,ctx:[],props:u,update:t,not_equal:a,bound:n(),on_mount:[],on_destroy:[],on_disconnect:[],before_update:[],after_update:[],context:new Map(r.context||(p?p.$$.context:[])),callbacks:n(),dirty:l,skip_bound:!1,root:r.target||p.$$.root};d&&d(h.root);let g=!1;if(h.ctx=s?s(e,r.props||{},((t,n,...o)=>{const r=o.length?o[0]:n;return h.ctx&&a(h.ctx[t],h.ctx[t]=r)&&(!h.skip_bound&&h.bound[t]&&h.bound[t](r),g&&S(e,t)),n})):[],h.update(),g=!0,o(h.before_update),h.fragment=!!i&&i(h.ctx),r.target){if(r.hydrate){const t=function(t){return Array.from(t.childNodes)}(r.target);h.fragment&&h.fragment.l(t),t.forEach(f)}else h.fragment&&h.fragment.c();r.intro&&B(e.$$.fragment),M(e,r.target,r.anchor,r.customElement),k()}$(p)}class F{$destroy(){N(this,1),this.$destroy=t}$on(e,n){if(!r(n))return t;const o=this.$$.callbacks[e]||(this.$$.callbacks[e]=[]);return o.push(n),()=>{const t=o.indexOf(n);-1!==t&&o.splice(t,1)}}$set(t){var e;this.$$set&&(e=t,0!==Object.keys(e).length)&&(this.$$.skip_bound=!0,this.$$set(t),this.$$.skip_bound=!1)}}const C=[];function U(e){let n,r,s,c,$,b,_,y,E,w,v,x,A,L,T;return{c(){var t,o;n=d("div"),r=d("div"),s=d("span"),s.textContent="×",c=l(),$=d("div"),b=d("img"),y=l(),E=d("h2"),E.textContent=`${e[2]}`,w=l(),v=new m(!1),x=l(),A=d("div"),h(s,"class","fab-upgrade-close-btn"),h(b,"class","fab-upgrade-logo"),t=b.src,o=_=e[1],i||(i=document.createElement("a")),i.href=o,t!==i.href&&h(b,"src",_),h(b,"alt","fab-logo"),h($,"class","fab-upgrade-title-container"),v.a=x,h(r,"class","modal-fab-upgrade-content"),h(n,"class","modal-fab-upgrade"),g(n,"modal-fab-upgrade-open","modal-fab-upgrade-open"===e[0])},m(t,o){u(t,n,o),a(n,r),a(r,s),a(r,c),a(r,$),a($,b),a($,y),a($,E),a(r,w),v.m(e[3],r),a(r,x),a(r,A),A.innerHTML=e[4],L||(T=[p(s,"click",e[7]),p(s,"keydown",X)],L=!0)},p(t,[e]){1&e&&g(n,"modal-fab-upgrade-open","modal-fab-upgrade-open"===t[0])},i:t,o:t,d(t){t&&f(n),L=!1,o(T)}}}const X=()=>{};function D(t,e,n){let{isOpen:o}=e,r="",s=window.FAB_METABOX_UPSELL.logo,i=window.FAB_METABOX_UPSELL.title,c=window.FAB_METABOX_UPSELL.content,a=window.FAB_METABOX_UPSELL.button;function u(){o.set(!1)}return t.$$set=t=>{"isOpen"in t&&n(6,o=t.isOpen)},t.$$.update=()=>{64&t.$$.dirty&&o.subscribe((t=>{n(0,r=t?"modal-fab-upgrade-open":"")}))},[r,s,i,c,a,u,o,()=>u()]}class Q extends F{constructor(t){super(),j(this,t,D,U,s,{isOpen:6})}}function z(e){let n,o;return n=new Q({props:{isOpen:e[0]}}),{c(){var t;(t=n.$$.fragment)&&t.c()},m(t,e){M(n,t,e),o=!0},p:t,i(t){o||(B(n.$$.fragment,t),o=!0)},o(t){!function(t,e,n,o){if(t&&t.o){if(P.has(t))return;P.add(t),(void 0).c.push((()=>{P.delete(t),o&&(n&&t.d(1),o())})),t.o(e)}else o&&o()}(n.$$.fragment,t),o=!1},d(t){N(n,t)}}}function H(e){let n=function(e,n=t){let o;const r=new Set;function i(t){if(s(e,t)&&(e=t,o)){const t=!C.length;for(const t of r)t[1](),C.push(t,e);if(t){for(let t=0;t<C.length;t+=2)C[t][0](C[t+1]);C.length=0}}}return{set:i,update:function(t){i(t(e))},subscribe:function(s,c=t){const a=[s,c];return r.add(a),1===r.size&&(o=n(i)||t),s(e),()=>{r.delete(a),0===r.size&&o&&(o(),o=null)}}}}(!1);const o=window.FAB_METABOX_UPSELL.upsells||[];return b((async()=>{setTimeout((()=>{jQuery.each(o,(function(t,e){const o=jQuery("#"+e.metabox_id).find(".inside"),r=o.find(".select2"),s=o.find("button"),i=o.find("input");jQuery(".fab-location-rule-group-item").addClass("disable-sort"),r.on("select2:opening",(function(t){t.preventDefault(),t.stopPropagation(),n.set(!0)})),s.prop("disabled",!0),s.on("click",(function(t){t.preventDefault(),t.stopPropagation(),n.set(!0)})),i.on("keydown keypress keyup",(function(t){t.preventDefault(),t.stopPropagation(),n.set(!0)}))})),jQuery("#fab-metabox-settings .select2").on("select2:selecting",(function(t){const e=t.params.args.data;e&&e.id.includes("upsell")&&(t.preventDefault(),t.stopPropagation(),n.set(!0))}))}),80)})),[n]}return new class extends F{constructor(t){super(),j(this,t,H,z,s,{})}}({target:document.body})}();
