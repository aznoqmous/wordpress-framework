import Element from "../libs/element"

export default class ColorField extends Element {
    build() {
        this.selectAll(".color").map(color => {
            const input = color.querySelector("input")
            const lightness = this.getLightness(input.value)
            color.classList.toggle("light", lightness > 0.5)
        })
        this.hiddenInput = this.select('input.hidden')
    }

    bind() {
        this.selectAll(".color input").map(input => {
            input.addEventListener("click", (e)=> {
                e.preventDefault()
                setTimeout(()=>{
                    input.checked = !input.checked
                    if(!input.checked) this.hiddenInput.checked = true
                })
            })
        })
    }

    getColorRgb(color) {
        const canvas = document.createElement('canvas')
        canvas.width = 1
        canvas.height = 1
        const ctx = canvas.getContext("2d")
        ctx.fillStyle = color
        ctx.fillRect(0, 0, 1, 1)
        return ctx.getImageData(0, 0, 1, 1).data
    }

    getLightness(color) {
        const data = this.getColorRgb(color)
        return (data[0] + data[1] + data[2]) / 3 / 255
    }
}