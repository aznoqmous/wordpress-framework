import Element from "../libs/element";
import Drag from "../libs/drag";

export default class RelationField extends Element {

    build() {
        this.selected = this.select('.selected')
        this.searchInput = this.select('.search')
        this.options = this.select('.options')
        this.hiddenInput = this.select('.hidden-field')
        this.id = this.hiddenInput.name.replace(/[\[\]]/g, "_").replace(/__/g, "_")
    }

    get isMultiple() {
        return this.select(".container").classList.contains('multiple')
    }

    get isSortable() {
        return this.select(".container").classList.contains('sortable')
    }

    get selectedItems() {
        return Array.from(this.selected.children)
    }

    get selectedIds() {
        return this.selectedItems.map(el => el.dataset.id)
    }

    bind() {
        Array.from(this.selected.children).map(item => this.bindItem(item))
        Array.from(this.options.children).map(item => this.bindItem(item))
        this.searchInput.addEventListener('input', () => {
            this._search(this.searchInput.value)
        })
        window.addEventListener('click', (e) => {
            this.container.classList.toggle('focused', this.container.contains(e.target))
        })

        if (this.isSortable) {
            const drag = new Drag(this.selected)
            drag.addEventListener('dragchange', () => this.updateField())
        }
    }

    static async search(model, query, exclude = null, options={}) {
        if (this.abortController) {
            try {
                this.abortController.abort("")
            } catch (e) {
            }
        }
        this.abortController = new AbortController()

        return await fetch("/wp-json/api/relation/search", {
            headers: {
                "Content-Type": "application/json"
            },
            method: "POST",
            body: JSON.stringify({
                query,
                model,
                exclude,
                ...options
            }),
            signal: this.abortController.signal
        })
        .then(res => res.json())
    }

    async _search(query) {
        const exclude = this.selectedIds
        if (this.searchInput.dataset.excludeId) exclude.push(this.searchInput.dataset.excludeId)

        this.container.classList.add('loading')
        this.clear()

        const items = await RelationField.search(this.searchInput.dataset.model, query, exclude, this.container.dataset)

        this.container.classList.remove('loading')
        this.clear()

        if (items)
            items.map(item => this.addOptionItem(item))

    }

    clear() {
        this.options.innerHTML = ""
    }

    addOptionItem(item) {
        if (this.selectedIds.includes(item.id)) return;
        const element = document.createElement('li')
        element.innerHTML = item._html || item.title || item.name
        element.dataset.id = item.source_language_id || item.id
        this.bindItem(element)
        this.options.append(element)
    }

    bindItem(element) {
        element.style.viewTransitionName = `${this.id}${element.dataset.id}`
        element.addEventListener('click', () => {
            if(document.startViewTransition){
                document.startViewTransition(()=>{
                    if (element.parentElement === this.options)
                        this.addSelected(element)
                    else
                        this.removeSelected(element)
                    this.updateField()
                })
            }
            else {
                if (element.parentElement === this.options)
                    this.addSelected(element)
                else
                    this.removeSelected(element)
                this.updateField()
            }
        })
    }

    addSelected(element) {
        if (!this.isMultiple) this.selectedItems.map(item => this.removeSelected(item))
        this.selected.append(element)
    }

    removeSelected(element) {
        this.options.append(element)
    }

    updateField() {
        if (this.isMultiple)
            this.hiddenInput.value = this.selectedIds.length ? JSON.stringify(this.selectedIds) : null
        else
            this.hiddenInput.value = this.selectedIds.length ? this.selectedIds[0] : null
        this.hiddenInput.dispatchEvent(new CustomEvent('input'))

    }
}