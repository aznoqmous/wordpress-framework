import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class FilteredRealisations extends Element {

    build() {

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
                })
            })
        })
    }

}