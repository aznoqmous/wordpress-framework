import MathLib from "./MathLib";
import Time from "./Time";

export default class CustomSlider extends EventTarget {

    constructor(container) {
        super()
        this.container = container
        this.items = Array.from(this.container.children)
        this.activeItem = this.items[0]
        this.selectedItem = this.items[0]
        this.activeIndex = 0
        this.init()
        this.bind()
        this.play()
    }

    bind() {

        this.container.addEventListener('mousedown', (e) => {
            this.handleMouseDown(e.clientX, e.clientY)
        })
        this.container.addEventListener('mousemove', (e) => {
            this.handleMouseMove(e.clientX, e.clientY)
        })
        this.container.addEventListener('mouseleave', () => {
            this.handleMouseUp()
        })
        this.container.addEventListener('mouseup', (e) => {
            this.handleMouseUp()
        })

        this.container.addEventListener('touchstart', (e) => {
            this.handleMouseDown(e.touches[0].clientX, e.touches[0].clientY)
        })
        this.container.addEventListener('touchmove', (e) => {
            this.handleMouseMove(e.touches[0].clientX, e.touches[0].clientY)
        })
        this.container.addEventListener('touchend', () => {
            this.handleMouseUp()
        })

        this.items.map(item => {
            item.addEventListener('click', (e) => {
                if (this.cancelClick) {
                    e.preventDefault()
                    return;
                }
                this.setActive(item)
            })
        })
    }

    handleMouseDown(x, y) {
        this.stop()
        this.cancelClick = false
        this.startDrag = {
            x: x - this.offsetX,
            y: y
        }
    }

    handleMouseMove(x, y) {
        if (!this.startDrag) return;
        this.cancelClick = true
        this.offsetX = x - this.startDrag.x
        this.applyOffset()
        this.update()
    }

    handleMouseUp(x, y) {
        this.play()
        this.startDrag = null
    }

    init() {
        this.rect = this.container.parentElement.getBoundingClientRect()
        this.items.map(item => item.rect = item.getBoundingClientRect())
        this.watchUpdate = false
        this.offsetX = 0
        this.applyOffset()
    }

    update() {
        this.rect = this.container.parentElement.getBoundingClientRect()
        this.items.map(item => item.rect = item.getBoundingClientRect())

        const activeSize = 1.6
        const regularSize = 1

        this.items.map(item => {
            item.direction = item.rect.left - this.rect.left
            item.distance = Math.abs(item.direction)
            const ratio = item.distance / item.rect.width
            item.style.setProperty("--size", MathLib.lerp(activeSize, regularSize, ratio));
        })

        const sortedItems = Array.from(this.items).sort((a, b) => a.distance - b.distance)
        sortedItems.map((item, i) => item.classList.toggle('active', !i))

        this.activeItem = sortedItems[0]
        if (this.activeItem !== this.lastActiveItem) {
            this.activeIndex = this.items.indexOf(this.activeItem)
            this.dispatchEvent(new CustomEvent('change'))
        }
        this.lastActiveItem = this.activeItem

        this.items.map((item, i) => {
            item.classList.toggle("hidden", (i - this.activeIndex) < -1)
        })

        /* navigate to active item */
        if (this.watchUpdate) {
            if (!this.selectedItem) this.selectedItem = this.activeItem
            if (this.selectedItem.distance > 1) {
                this.offsetX = MathLib.lerp(this.offsetX, this.offsetX - this.selectedItem.direction, Time.deltaTime * 5)
                this.applyOffset()
            } else {
                this.selectedItem = null
                this.stop()
            }
        }

        if (this.watchUpdate) requestAnimationFrame(this.update.bind(this))
    }

    applyOffset() {
        this.offsetX = MathLib.clamp(this.offsetX, -this.rect.width, this.activeItem.rect.width)
        this.container.style.transform = `translate(${this.offsetX}px, 0)`
    }

    play() {
        if (this.watchUpdate) return;
        this.watchUpdate = true
        this.update()
    }

    stop() {
        this.selectedItem = null
        this.watchUpdate = false
    }

    setActive(item) {
        this.selectedItem = item
        this.play()
    }

    next() {
        const index = this.activeIndex + 1
        const newItem = this.items[index]
        if (newItem) this.setActive(newItem)
    }

    prev() {
        const index = this.activeIndex - 1
        const newItem = this.items[index]
        if (newItem) this.setActive(newItem)
    }

}