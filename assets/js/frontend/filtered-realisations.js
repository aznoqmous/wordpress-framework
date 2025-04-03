import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class FilteredRealisations extends Element {

    build() {
        this.swiperContainer = this.select(".swiper")
        this.realisationsCount = this.select(".title small")
    }

    bind() {
        this.selectAll('select').map(select => {
            const element = document.createRange().createContextualFragment(`
                <div class="select">
                    <span>Aucun filtre</span>
                    <ul class="selected-container"></ul>
                    <hr>
                    <div class="options">
                        <ul class="options-container">
                            ${Array.from(select.options).map(opt => `<li data-value="${opt.value}">${opt.innerHTML}</li>`).join("")}
                        </ul>
                    </div>
                </div>
            `).children[0]
            element.addEventListener('click', (e) => {
                if (element == e.target) element.classList.toggle('opened')
            })
            select.parentElement.append(element)
            const options = this.selectAll(".options-container li", element)
            const selectedContainer = this.select(".selected-container", element)
            const optionsContainer = this.select(".options-container", element)
            options.map(option => {
                option.addEventListener('click', () => {
                    if (selectedContainer.contains(option)) optionsContainer.append(option)
                    else selectedContainer.append(option)
                    element.classList.toggle('has-value', selectedContainer.children.length)

                    this.update()
                })
            })
        })
    }

    async update() {
        const options = this.selectAll(".selected-container li").map(el => el.dataset.value).join(",")
        const body = new FormData()
        body.append("options", options)
        const realisations = await fetch("/wp-json/api/realisation/list", {
            method: "POST",
            body
        }).then(res => res.text())

        this.swiperContainer.SwiperElement.swiper.removeAllSlides()

        let items = document.createRange().createContextualFragment(realisations)
        items = Array.from(items.children)
        this.swiperContainer.SwiperElement.swiper.appendSlide(items.map(item => `<div class="swiper-slide">${item.outerHTML}</div>`))
        this.realisationsCount.innerHTML = items.length
    }
}