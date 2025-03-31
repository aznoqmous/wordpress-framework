import Element from "../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class TreeView extends Element {
    build() {
        this.items = this.selectAll(".tree-view-item")

        this.items.map(item => {
            this.bindItem(item)
        })

        this.updateClasses()
    }

    bind() {
        this.select(".cancel").addEventListener("click", () => {
            this.selectItem(null)
        })
    }

    bindItem(item) {
        const insideIcon = this.select(".inside", item)
        const afterIcon = this.select(".after", item)
        const moveIcon = this.select(".move", item)
        moveIcon.addEventListener('click', () => {
            this.selectItem(item)
        })
        insideIcon.addEventListener("click", () => {
            this.startViewTransition(()=> this.insertInside(this.selectedItem, item))
        })
        afterIcon.addEventListener("click", () => {
            this.startViewTransition(()=> this.insertAfter(this.selectedItem, item))
        })

        item.childrenContainer = this.select('ul', item)
        item.style.viewTransitionName = `post-${item.dataset.id}`
    }

    insertInside(item, target) {
        if(target.childrenContainer.children.length) target.childrenContainer.insertBefore(item, target.childrenContainer.children[0])
        else target.childrenContainer.append(item)
        this.selectItem(null)
        this.update()
    }

    insertAfter(item, target) {
        if (target.nextSibling) target.parentElement.insertBefore(item, target.nextSibling)
        else target.parentElement.append(item)
        this.selectItem(null)
        this.update()
    }

    selectItem(item) {
        if (this.selectedItem) this.selectedItem.classList.remove("active")
        this.selectedItem = item
        if (item) item.classList.add('active')
        this.container.classList.toggle('selected', !!item)
    }

    async update() {
        const tree = this.items.map(item => {
            const parent = item.parentElement.parentElement
            return {
                item: parseInt(item.dataset.id),
                post_parent: parseInt(parent.dataset.id) || 0,
                menu_order: Array.from(item.parentElement.children).indexOf(item)
            }
        })

        this.updateClasses()

        await fetch("/wp-json/api/post/tree_view_update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                tree
            })
        })
    }

    startViewTransition(cb) {
        if (document.startViewTransition) document.startViewTransition(() => cb())
        else cb()
    }

    updateClasses() {
        this.selectAll(".tree-view-item").map((item, i) => item.classList.toggle('even', i % 2))
    }
}
