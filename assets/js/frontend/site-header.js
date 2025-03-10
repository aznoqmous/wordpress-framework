import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class SiteHeader extends Element {
    bind(){
        window.addEventListener("scroll", ()=>{
            this.container.classList.toggle("top", !window.scrollY)
        })
        this.container.classList.toggle("light", !!document.querySelector("#main > .wp-block-cover:first-child"))

        this.select('.burger').addEventListener('touchstart', (e)=>{
            e.preventDefault()
            document.body.classList.toggle('menu-open')
        })
    }
}