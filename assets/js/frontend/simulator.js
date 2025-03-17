import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";
import areas from "../../areas.json"
import Map from "../libs/map"
import MathUtils from "../libs/math-utils";

export class Simulator extends Element {
    bind() {
        this.calculator = JSON.parse(this.container.dataset.calculator)
        this.searchTime = 500
        this.searchTimeout = null

        this.currentStep = 0

        this.data = {
            area: 0,
            sunshineFactor: 0,
            parkingPotentialKwc: 0,
            areaPotentialKwc: 0,
            estimatedCost: 0
        }

        this.bindNavigation()
        this.bindAddressInput()
        this.bindMap()
    }

    bindNavigation() {
        this.steps = this.selectAll(".steps li")
        this.contents = this.selectAll(".steps-container li")
        this.nextButton = this.select('.next')
        this.prevButton = this.select('.prev')

        this.nextButton.addEventListener('click', () => this.next())
        this.prevButton.addEventListener('click', () => this.prev())
    }

    bindAddressInput() {
        this.searchInput = this.select("input#address")
        this.resultsContainer = this.select(".address-options")
        this.resultsWrapper = this.select(".address-options-wrapper")

        this.searchInput.addEventListener('input', () => {
            if (this.searchInput.value < 8) return;
            if (this.searchTimeout) clearTimeout(this.searchTimeout)
            this.searchTimeout = setTimeout(async () => {
                this.search(this.searchInput.value)
            }, this.searchTime)
        })

        this.searchInput.addEventListener('focus', () => {
            this.resultsWrapper.classList.remove('hidden')
        })
        this.searchInput.addEventListener('focusout', () => {
            setTimeout(() => this.resultsWrapper.classList.add('hidden'), 100)
        })
    }

    bindMap() {
        this.mapToggle = this.select('.map-toggle')
        this.mapToggleButton = this.select('.map-toggle button')
        this.mapToggleButton.addEventListener('click', ()=> {
            this.mapContainer.classList.add('active')
            this.mapToggle.remove()
            this.map = new Map(this.mapContainer, {
                //tileLayerUrl: "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                maxZoom: 19
            })
            this.map.select(this.map.addArea("#36A9E1"))
            this.map.leafletMap.setView([this.addressFeature.geometry.coordinates[1], this.addressFeature.geometry.coordinates[0]], 19)
        })

        this.mapContainer = this.select(".map-container")

        this.mapContainer.addEventListener('mousedown', ()=>{
            this.mapContainer.classList.add('interacted')
        })

        this.map.addEventListener('updateArea', (e) => {
            this.data.area = this.map.areas.reduce((a, b) => a + b.area, 0)
            this.render()
        })
    }

    async search(query) {
        const results = await fetch(`https://api-adresse.data.gouv.fr/search/?type=housenumber&q=${query.replace(/ /, "+")}`).then(res => res.json())
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
                const dept = parseInt(feature.properties.postcode.slice(0, 2))
                const department = this.findDepartment(dept)
                this.data.sunshineFactor = department.value
                this.addressFeature = feature
            })
        })
    }

    findDepartment(department) {
        return areas.find(area => department == area.department)
    }

    render() {
        this.select(".simulator-results").classList.toggle("active", (this.data.area && this.data.sunshineFactor))
        if (!(this.data.area && this.data.sunshineFactor)) return
        this.data.parkingLots = this.data.area / this.calculator.parking_space_area
        this.data.parkingPotentialKwc = this.data.parkingLots * this.calculator.parking_space_area * this.calculator.m_squared_to_kwc
        this.data.areaPotentialKwc = this.data.area * this.calculator.m_squared_to_kwc

        this.data.estimatedCost = this.data.areaPotentialKwc * 1000
        for (let price of this.calculator.price_ranges) {
            if (this.data.parkingPotentialKwc >= parseInt(price.kwc_min)) this.data.estimatedCost *= parseFloat(price.cost)
        }

        this.data.yearlyKwh = this.data.parkingPotentialKwc * this.data.sunshineFactor
        const factor = 0.996
        this.data.totalProduction = this.data.yearlyKwh * (1 - (Math.pow(factor, this.calculator.environmental_reference_period))) / (1 - factor) / 1000

        Object.keys(this.data).map(key => {
            const element = this.select(".simulator-results ." + key)
            if (!element) return;
            element.innerHTML = this.data[key].toFixed(2)
        })
    }

    next() {
        this.currentStep = MathUtils.clamp(this.currentStep + 1, 0, this.steps.length - 1)
        this.updateStep()
    }

    prev() {
        this.currentStep = MathUtils.clamp(this.currentStep - 1, 0, this.steps.length - 1)
        this.updateStep()
    }

    updateStep() {
        this.steps.map((step, i) => step.classList.toggle('active', i <= this.currentStep))
        this.contents.map((content, i) => content.classList.toggle('active', i === this.currentStep))
    }
}