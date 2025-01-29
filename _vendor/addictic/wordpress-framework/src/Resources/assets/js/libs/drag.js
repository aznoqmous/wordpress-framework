import Element from "./element";

export default class Drag extends Element {
    bind() {
        this.update()
        this.items.map(item => this.bindItem(item))

        const observer = new MutationObserver((entries) => {
            for (const entry of entries) {
                entry.addedNodes.forEach(item => this.bindItem(item))
            }
        })
        observer.observe(this.container, {
            childList: true
        })
    }

    update() {
        this.items = Array.from(this.container.children)
    }

    bindItem(item) {
        if(item.classList.contains('clone')) return;
        if (item._dragbound) return
        item._dragbound = true

        this.startY = 0
        this.offsetY = 0
        this.update()
        item.addEventListener('mousedown', (e) => {
            if (item.parentElement !== this.container) return;
            this.startY = e.pageY
            this.offsetY = this.container.getBoundingClientRect().top - item.getBoundingClientRect().top
            const y = this.startY - e.pageY + this.offsetY
            this.container.addEventListener('mousemove', mouseMove)
            this.dragClone = item.cloneNode(true)
            this.container.append(this.dragClone)
            this.dragClone.classList.add('clone')
            this.dragClone.style.top = `${-y}px`
            setTimeout(() => item.classList.add('dragged'), 100)
        })

        const mouseMove = (e) => {
            const y = this.startY - e.pageY + this.offsetY
            this.dragClone.style.top = `${-y}px`

            const rect = item.getBoundingClientRect()
            if (e.clientY < rect.top) {
                this.container.insertBefore(item, item.previousSibling)
                this.update()
                this.dispatchEvent(new CustomEvent('dragchange'))
            }
            if (e.clientY > rect.bottom) {
                this.container.insertBefore(item.nextSibling, item)
                this.update()
                this.dispatchEvent(new CustomEvent('dragchange'))
            }
        }

        window.addEventListener('mouseup', (e) => {
            this.startY = null
            if(!this.dragClone) return;
            this.dragClone.remove()
            this.container.removeEventListener('mousemove', mouseMove)
            item.classList.remove("dragged")
            setTimeout(() => item.classList.remove('dragged'), 100)
        })

    }
}