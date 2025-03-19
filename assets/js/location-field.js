import Element from "../../_vendor/addictic/wordpress-framework/assets/js/libs/element"

// TODO : move to wordpress-framework bundle
export default class LocationField extends Element {
    build() {
        this.input = this.select('[type="hidden"]')
        this.searchInput = this.select("input.search")
        this.resultsContainer = this.select(".options")
    }

    bind() {
        this.searchInput.addEventListener('input', ()=>{
            this.resultsContainer.innerHTML = ""
            if(this.searchTimeout) clearTimeout(this.searchTimeout)
            this.searchTimeout = setTimeout(()=> this.search(this.searchInput.value), 500)
        })
        this.searchInput.addEventListener('focusin', ()=> this.container.classList.add('open'))
        this.searchInput.addEventListener('focusout', ()=> setTimeout(()=> this.container.classList.remove('open'), 100))
    }

    get type(){
        return this.searchInput.dataset.type
    }

    async search(query) {

        const searchParams = new URLSearchParams()
        if(this.type) searchParams.set('type', this.type)
        searchParams.set("q", query.replace(/ /, "+"))

        const results = await fetch(`https://api-adresse.data.gouv.fr/search/?${searchParams.toString()}`).then(res => res.json())
        if (!results.features) return null;
        this.resultsContainer.innerHTML = ""
        this.resultsContainer.classList.remove('hidden')
        results.features.map(feature => {
            const element = document.createElement('div')
            element.textContent = feature.properties.label
            this.resultsContainer.append(element)

            element.addEventListener('click', () => {
                this.resultsContainer.classList.add('hidden')
                this.searchInput.value = feature.properties.label
                this.input.value = JSON.stringify({
                    lat: feature.geometry.coordinates[1],
                    lng: feature.geometry.coordinates[0],
                    label: feature.properties.label,
                    postcode: feature.properties.postcode,
                    citycode: feature.properties.citycode,
                })
            })
        })
    }
}