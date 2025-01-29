import Element from "../libs/element"
import RelationField from "./relation-field";
import UploadField from "./upload-field";
import IconField from "./icon-field";
import ColorField from "./color-field";

export default class ListField extends Element {
    build() {
        this.fieldsContainer = this.select('.entities')
        this.addButton = this.fieldsContainer.parentElement.querySelector(":scope > .list-add")
        this.template = this.select('template')
        this.selector = this.container.dataset.name
            .replace(/\[/g, "\\\[")
            .replace(/\]/g, "\\\]")

        // save an empty string if 0 entities
        this.hiddenInput = document.createElement('input')
        this.hiddenInput.name = this.container.dataset.name
        this.hiddenInput.type = "hidden"

        this.update()
    }

    get entities() {
        return Array.from(this.fieldsContainer.children)
    }

    bind() {
        this.addButton.addEventListener('click', () => {
            this.fieldsContainer.append(...document.createRange().createContextualFragment(this.template.innerHTML).children)
            this.update()
        })
    }

    bindEntity(entity) {
        entity.querySelector(':scope > .list-remove').addEventListener('click', (e) => {
            entity.remove()
            this.update()
        })
    }

    update() {
        this.entities.map((entity, i) => {
            entity.querySelectorAll("[for]").forEach(label => {
                label.setAttribute("for", this.replaceId(label.getAttribute('for'), i))
            })
            entity.querySelectorAll("[name]").forEach(input => {
                input.name = this.replaceName(input.name, i)
                input.id = this.replaceId(input.id, i)
            })
            entity.dataset.index = i
            entity.style.viewTransitionName = this.container.dataset.name + "-" + i
        })
        RelationField.bind(".relation-field")
        UploadField.bind(".upload-field")
        IconField.bind(".icon-field")
        ColorField.bind(".color-field")
        ListField.bind(".list-field")
        this.entities.map(entity => this.bindEntity(entity))

        if(!this.entities.length) this.container.append(this.hiddenInput)
        else this.hiddenInput.remove()
    }

    replaceId(strInput, index) {
        return strInput.replace(new RegExp(`${this.selector}\\[(\\d|INDEX)\\]`, "g"), `${this.container.dataset.name}[${index}]`)
    }

    replaceName(strInput, index) {
        return strInput.replace(new RegExp(`${this.selector}\\[(\\d|INDEX)\\]`, "g"), `${this.container.dataset.name}[${index}]`)
    }
}