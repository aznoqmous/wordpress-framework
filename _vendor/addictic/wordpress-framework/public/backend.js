import{r as v}from"./vendor.js";class a extends EventTarget{constructor(e,t={}){super(),!e[this.constructor.name]&&(this.container=e,this.container[this.constructor.name]=this,this.opts=t,this.build(),this.bind(),this.start())}build(){}bind(){}start(){}select(e,t=null){return t=t||this.container,t.querySelector(e)}selectAll(e,t=null){return t=t||this.container,Array.from(t.querySelectorAll(e))}create(e="div",t={},s=null){const i=document.createElement(e);for(const r in t)i[r]=t[r];return s&&s.append(i),i}async post(e,t={}){const s=new FormData;for(let i in t)s.append(i,t[i]);return await fetch(`/wp-json${e}`,{method:"POST","Content-Type":"application/json",body:s}).then(i=>i.json())}async sleep(e=0){return new Promise(t=>setTimeout(t,e))}static bind(e,t={},s=!1){return s&&new MutationObserver(r=>{Array.from(document.querySelectorAll(e)).map(l=>new this(l,t))}).observe(document.body,{childList:!0,subtree:!0}),Array.from(document.querySelectorAll(e)).map(i=>new this(i,t))}}class w extends a{bind(){this.update(),this.items.map(t=>this.bindItem(t)),new MutationObserver(t=>{for(const s of t)s.addedNodes.forEach(i=>this.bindItem(i))}).observe(this.container,{childList:!0})}update(){this.items=Array.from(this.container.children)}bindItem(e){if(e.classList.contains("clone")||e._dragbound)return;e._dragbound=!0,this.startY=0,this.offsetY=0,this.update(),e.addEventListener("mousedown",s=>{if(e.parentElement!==this.container)return;this.startY=s.pageY,this.offsetY=this.container.getBoundingClientRect().top-e.getBoundingClientRect().top;const i=this.startY-s.pageY+this.offsetY;this.container.addEventListener("mousemove",t),this.dragClone=e.cloneNode(!0),this.container.append(this.dragClone),this.dragClone.classList.add("clone"),this.dragClone.style.top=`${-i}px`,setTimeout(()=>e.classList.add("dragged"),100)});const t=s=>{const i=this.startY-s.pageY+this.offsetY;this.dragClone.style.top=`${-i}px`;const r=e.getBoundingClientRect();s.clientY<r.top&&(this.container.insertBefore(e,e.previousSibling),this.update(),this.dispatchEvent(new CustomEvent("dragchange"))),s.clientY>r.bottom&&(this.container.insertBefore(e.nextSibling,e),this.update(),this.dispatchEvent(new CustomEvent("dragchange")))};window.addEventListener("mouseup",s=>{this.startY=null,this.dragClone&&(this.dragClone.remove(),this.container.removeEventListener("mousemove",t),e.classList.remove("dragged"),setTimeout(()=>e.classList.remove("dragged"),100))})}}class c extends a{build(){this.selected=this.select(".selected"),this.searchInput=this.select(".search"),this.options=this.select(".options"),this.hiddenInput=this.select(".hidden-field"),this.id=this.hiddenInput.name.replace(/[\[\]]/g,"_").replace(/__/g,"_")}get isMultiple(){return this.select(".container").classList.contains("multiple")}get isSortable(){return this.select(".container").classList.contains("sortable")}get selectedItems(){return Array.from(this.selected.children)}get selectedIds(){return this.selectedItems.map(e=>e.dataset.id)}bind(){Array.from(this.selected.children).map(e=>this.bindItem(e)),Array.from(this.options.children).map(e=>this.bindItem(e)),this.searchInput.addEventListener("input",()=>{this._search(this.searchInput.value)}),window.addEventListener("click",e=>{this.container.classList.toggle("focused",this.container.contains(e.target))}),this.isSortable&&new w(this.selected).addEventListener("dragchange",()=>this.updateField())}static async search(e,t,s=null,i={}){if(this.abortController)try{this.abortController.abort("")}catch{}return this.abortController=new AbortController,await fetch("/wp-json/api/relation/search",{headers:{"Content-Type":"application/json"},method:"POST",body:JSON.stringify({query:t,model:e,exclude:s,...i}),signal:this.abortController.signal}).then(r=>r.json())}async _search(e){const t=this.selectedIds;this.searchInput.dataset.excludeId&&t.push(this.searchInput.dataset.excludeId),this.container.classList.add("loading"),this.clear();const s=await c.search(this.searchInput.dataset.model,e,t,this.container.dataset);this.container.classList.remove("loading"),this.clear(),s&&s.map(i=>this.addOptionItem(i))}clear(){this.options.innerHTML=""}addOptionItem(e){if(this.selectedIds.includes(e.id))return;const t=document.createElement("li");t.innerHTML=e._html||e.title||e.name,t.dataset.id=e.source_language_id||e.id,this.bindItem(t),this.options.append(t)}bindItem(e){e.style.viewTransitionName=`${this.id}${e.dataset.id}`,e.addEventListener("click",()=>{document.startViewTransition?document.startViewTransition(()=>{e.parentElement===this.options?this.addSelected(e):this.removeSelected(e),this.updateField()}):(e.parentElement===this.options?this.addSelected(e):this.removeSelected(e),this.updateField())})}addSelected(e){this.isMultiple||this.selectedItems.map(t=>this.removeSelected(t)),this.selected.append(e)}removeSelected(e){this.options.append(e)}updateField(){this.isMultiple?this.hiddenInput.value=this.selectedIds.length?JSON.stringify(this.selectedIds):null:this.hiddenInput.value=this.selectedIds.length?this.selectedIds[0]:null,this.hiddenInput.dispatchEvent(new CustomEvent("input"))}}class g extends a{build(){this.button=this.select('input[type="button"]'),this.hiddenInput=this.select('input[type="hidden"]'),this.previewContainer=this.select(".preview-container")}updatePreview(e){this.previewContainer.innerHTML="",e.filter(t=>t&&t.url).map(t=>{const s=this.create("figure",{},this.previewContainer);t.type=="image"&&this.create("img",{src:t.url},s),this.create("span",{innerHTML:t.filename},s)})}bind(){this.button.addEventListener("click",e=>{let t;if(e.preventDefault(),t){t.uploader.param("post_id",this.hiddenInput.value),t.open();return}wp.media.model.settings.post.id=this.hiddenInput.value,t=wp.media.frames.file_frame=wp.media({title:this.isMultiple?"Choisir des fichiers":"Choisir un fichier",button:{text:this.isMultiple?"Choisir des fichiers":"Choisir un fichier"},library:{type:this.container.dataset.filetype},multiple:this.isMultiple}),t.on("open",()=>{const s=t.state().get("selection");this.hiddenInput.value.split(",").forEach(function(r){const l=wp.media.attachment(r);l.fetch(),s.add(l?[l]:[])})}),t.on("select",()=>{const s=Array.from(t.state().get("selection")).map(i=>i.attributes);this.hiddenInput.value=s.map(i=>i.id).filter(i=>i).join(","),this.updatePreview(s)}),t.open()})}get isMultiple(){return!!this.container.dataset.multiple}}class f extends a{build(){this.icons=this.selectAll(".icon"),this.input=this.select("input"),this.removeButton=this.select(".button-danger")}bind(){this.icons.map(e=>{e.addEventListener("click",()=>{this.icons.map(t=>t.classList.toggle("active",t===e)),this.input.value=e.dataset.icon,this.removeButton.classList.remove("hidden")})}),this.removeButton.addEventListener("click",()=>{this.input.value=null,this.icons.map(e=>e.classList.remove("active")),this.removeButton.classList.add("hidden")})}}class b extends a{build(){this.selectAll(".color").map(e=>{const t=e.querySelector("input"),s=this.getLightness(t.value);e.classList.toggle("light",s>.5)}),this.hiddenInput=this.select("input.hidden")}bind(){this.selectAll(".color input").map(e=>{e.addEventListener("click",t=>{t.preventDefault(),setTimeout(()=>{e.checked=!e.checked,e.checked||(this.hiddenInput.checked=!0)})})})}getColorRgb(e){const t=document.createElement("canvas");t.width=1,t.height=1;const s=t.getContext("2d");return s.fillStyle=e,s.fillRect(0,0,1,1),s.getImageData(0,0,1,1).data}getLightness(e){const t=this.getColorRgb(e);return(t[0]+t[1]+t[2])/3/255}}class d extends a{build(){this.fieldsContainer=this.select(".entities"),this.addButton=this.fieldsContainer.parentElement.querySelector(":scope > .list-add"),this.template=this.select("template"),this.selector=this.container.dataset.name.replace(/\[/g,"\\[").replace(/\]/g,"\\]"),this.hiddenInput=document.createElement("input"),this.hiddenInput.name=this.container.dataset.name,this.hiddenInput.type="hidden",this.update()}get entities(){return Array.from(this.fieldsContainer.children)}bind(){this.addButton.addEventListener("click",()=>{this.fieldsContainer.append(...document.createRange().createContextualFragment(this.template.innerHTML).children),this.update()})}bindEntity(e){e.querySelector(":scope > .list-remove").addEventListener("click",t=>{e.remove(),this.update()})}update(){this.entities.map((e,t)=>{e.querySelectorAll("[for]").forEach(s=>{s.setAttribute("for",this.replaceId(s.getAttribute("for"),t))}),e.querySelectorAll("[name]").forEach(s=>{s.name=this.replaceName(s.name,t),s.id=this.replaceId(s.id,t)}),e.dataset.index=t,e.style.viewTransitionName=this.container.dataset.name+"-"+t}),c.bind(".relation-field"),g.bind(".upload-field"),f.bind(".icon-field"),b.bind(".color-field"),d.bind(".list-field"),this.entities.map(e=>this.bindEntity(e)),this.entities.length?this.hiddenInput.remove():this.container.append(this.hiddenInput)}replaceId(e,t){return e.replace(new RegExp(`${this.selector}\\[(\\d|INDEX)\\]`,"g"),`${this.container.dataset.name}[${t}]`)}replaceName(e,t){return e.replace(new RegExp(`${this.selector}\\[(\\d|INDEX)\\]`,"g"),`${this.container.dataset.name}[${t}]`)}}class y extends a{build(){this.field=this.select("input"),console.log(this)}bind(){}}wp.blockEditor||(wp.blockEditor={InspectorControls:"",InnerBlocks:"",RichText:"",useBlockProps:"",MediaUpload:"",MediaUploadCheck:"",BlockControls:""});wp.compose||(wp.compose={createHigherOrderComponent:()=>()=>{}});wp.components||(wp.components={PanelBody:"",SelectControl:"",TextControl:"",InputControl:"",TextareaControl:"",MediaUpload:"",Heading:"",Popover:"",ToolbarButton:""});wp.element||(wp.element={Fragment:"",cloneElement:""});wp.serverSizeRender||(wp.serverSizeRender=()=>{});const{InspectorControls:h,InnerBlocks:T,RichText:B,MediaUpload:A,MediaUploadCheck:F,useBlockProps:C,BlockControls:M}=wp.blockEditor,{createHigherOrderComponent:N}=wp.compose,{PanelBody:u,SelectControl:O,TextControl:p,TextareaControl:D,Heading:P,InputControl:_,Button:Y,Popover:q,ToolbarButton:$}=wp.components,{Fragment:H,cloneElement:j}=wp.element;wp.serverSizeRender;const o=n=>wp.i18n.__(n,"ifc");class E extends v.Component{constructor(e){super(e),this.state={html:""}}componentDidMount(){this.update()}componentDidUpdate(e,t,s){this.props.attributes!==e.attributes&&this.update()}async update(){const e=new FormData;e.append("name",this.props.block),e.append("attributes",JSON.stringify(this.props.attributes));const t=await fetch("/wp-json/api/block/render",{method:"POST",body:e}).then(s=>s.json());this.setState({html:t})}render(){return React.createElement(React.Fragment,null,this.state.html?React.createElement("div",{dangerouslySetInnerHTML:{__html:this.state.html}}):"Chargement...")}}class I{constructor(e){this.loaded=!1,this.blockName=e}static register(e){const t=new this(e),s=JSON.parse(JSON.stringify(t));wp.blocks&&wp.blocks.registerBlockType(e,{...s,edit:i=>t.edit(i),save:i=>t.save(i)})}ready(e){}editor(e){return React.createElement(React.Fragment,null)}render(e){return React.createElement(React.Fragment,null)}edit(e){return this.loaded||(this.ready(e),this.loaded=!0),React.createElement(React.Fragment,null,this.render(e),this.editor(e))}save(e){return this.render(e)}}class m extends I{static register(e){const t=new this(e),s=JSON.parse(JSON.stringify(t));wp.blocks&&wp.blocks.registerBlockType(e,{...s,edit:i=>t.edit(i),save:()=>null})}render(e){const t=C(),{attributes:s}=e;return React.createElement("div",{...t},React.createElement(E,{block:this.blockName,attributes:s}))}}class L extends m{constructor(e){super(e),this.title="Actualités mises en avant",this.icon="welcome-learn-more",this.category="layout",this.attributes={newsCount:{type:"integer",default:3}}}editor(e){const{attributes:t,setAttributes:s}=e,{newsCount:i}=e.attributes;return React.createElement(React.Fragment,null,React.createElement(h,null,React.createElement(u,{title:o("Contenu"),initialOpen:!0},React.createElement(p,{value:i,type:"number",label:o("Nombre d'éléments à afficher"),onChange:r=>s({newsCount:parseInt(r)})}))))}}L.register("wordpress-framework/news-featured-list");class k extends m{constructor(e){super(e),this.title="Liste d'actualités filtrées",this.icon="welcome-learn-more",this.category="layout",this.attributes={newsCount:{type:"integer",default:3}}}editor(e){const{attributes:t,setAttributes:s}=e,{newsCount:i}=e.attributes;return React.createElement(React.Fragment,null,React.createElement(h,null,React.createElement(u,{title:o("Contenu"),initialOpen:!0},React.createElement(p,{value:i,type:"number",label:o("Nombre d'éléments à afficher"),onChange:r=>s({newsCount:parseInt(r)})}))))}}k.register("wordpress-framework/news-filtered-list");class S extends m{constructor(e){super(e),this.title="Dernières actualités",this.icon="welcome-learn-more",this.category="layout",this.attributes={newsCount:{type:"integer",default:3}}}editor(e){const{attributes:t,setAttributes:s}=e,{newsCount:i}=e.attributes;return React.createElement(React.Fragment,null,React.createElement(h,null,React.createElement(u,{title:o("Contenu"),initialOpen:!0},React.createElement(p,{value:i,type:"number",label:o("Nombre d'éléments à afficher"),onChange:r=>s({newsCount:parseInt(r)})}))))}}S.register("wordpress-framework/news-latest-list");const R=async(n=0)=>new Promise(e=>setTimeout(e,n));document.addEventListener("DOMContentLoaded",async()=>{await R(100);const n=document.querySelector('form[name="post"],form.metabox-location-normal');if(!n)return;const e=Array.from(document.querySelectorAll(".editor-post-publish-button")),t=()=>Array.from(document.querySelectorAll(".editor-post-publish-button")).map(s=>{s.classList.toggle("disabled",!n.checkValidity())});n.append(document.createRange().createContextualFragment("<input type='submit' style='opacity: 0; pointer-events: none;'>")),t(),n.querySelectorAll("input,select,textarea").forEach(s=>{s.addEventListener("input",()=>t())}),n.addEventListener("keyup",()=>{t()}),e.map(s=>s.addEventListener("click",i=>{s.classList.contains("disabled")&&(i.stopImmediatePropagation(),i.preventDefault(),n.reportValidity())}))});c.bind(".relation-field");g.bind(".upload-field");f.bind(".icon-field");b.bind(".color-field");d.bind(".list-field");y.bind(".location-field");console.log("Wordpress Framework");
