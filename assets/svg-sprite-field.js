import Element from "../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class SvgSpriteField extends Element {
    build() {
        this.inputFile = this.select('input[type="file"]')
        this.iconsContainer = this.select(".icons")
        this.deleteButton = this.select('button.delete')
        this.bindIcons()
    }

    bind() {
        this.inputFile.addEventListener('input', async () => {
            const files = Array.from(this.inputFile.files)

            let content = ""
            for (let file of files) {
                const iconHTML = await this.readFile(file)
                const icon = document.createRange().createContextualFragment(iconHTML).children[0]
                if (!icon.id) icon.id = file.name.replace(/\.svg/, "")
                content += icon.outerHTML
            }

            const body = new FormData()
            body.append("icons", content)

            let result = await fetch(`/wp-json/api/sprite_icon/${this.inputFile.dataset.name}/add`, {
                method: "POST",
                body
            }).then(res => res.text())
            result = result.replace(/\\n/g, "")
            result = result.replace(/\\/g, "")

            const icons = Array.from(document.createRange().createContextualFragment(result).children)

            this.iconsContainer.innerHTML = icons.map(icon => `<li><figure>${icon.outerHTML}</figure><legend>${icon.dataset.icon}</legend></li>`).join("")
            this.bindIcons()
        })

        this.deleteButton.addEventListener('click', (e)=>{
            e.preventDefault()
            this.container.classList.toggle("delete-mode")
        })
    }

    get isDeleteMode(){
        return this.container.classList.contains("delete-mode")
    }

    bindIcons(){
        Array.from(this.iconsContainer.children).map(icon => {
            const iconId = icon.querySelector('[data-icon]').dataset.icon
            icon.addEventListener('click', ()=>{
                if(!this.isDeleteMode) return;
                icon.remove()
                this.removeIcon(iconId)
            })
        })
    }

    async removeIcon(iconId) {
        await fetch(`/wp-json/api/sprite_icon/${this.inputFile.dataset.name}/remove/${iconId}`, {
            method: "POST"
        })
    }

    async readFile(file) {
        return new Promise(res => {
            let content = ""
            const fr = new FileReader()
            fr.onloadend = () => res(content)
            fr.onload = (e) => {
                content += fr.result
            }
            fr.readAsText(file)
        })
    }
}