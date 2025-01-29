import Element from "../libs/element"

export default class IconField extends Element {
    build() {
        this.icons = this.selectAll(".icon")
        this.input = this.select("input")
        this.removeButton = this.select('.button-danger')
    }

    bind() {
        this.icons.map(icon => {
            icon.addEventListener('click', ()=>{
                this.icons.map(i => i.classList.toggle('active', i === icon))
                this.input.value = icon.dataset.icon
                this.removeButton.classList.remove('hidden')

            })
        })
        this.removeButton.addEventListener('click', ()=>{
            this.input.value = null
            this.icons.map(i => i.classList.remove('active'))
            this.removeButton.classList.add('hidden')
        })
    }
}