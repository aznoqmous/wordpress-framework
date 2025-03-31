import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class Popups extends Element {

    build() {
        this.loadState()
        this.popups = this.selectAll(".single-popup")

        this.popups.map(popup => this.bindPopup(popup))

        for (let popup of this.popups) {
            if (this.isActive(popup.dataset.id)) {
                setTimeout(()=> {
                    popup.classList.add("loaded")
                    popup.classList.add("active")
                    document.body.classList.add("popup-open")
                }, 1000)
                break;
            }
        }
    }

    bindPopup(popup) {
        popup.closeButton = this.select(".close-button", popup)
        popup.closeButton.addEventListener("click", () => {
            popup.classList.remove("active")
            document.body.classList.remove("popup-open")
            this.setState(popup.dataset.id, true)
        })
    }

    isActive(popupId) {
        return !this.states[popupId]
    }

    loadState() {
        try {
            this.states = JSON.parse(localStorage.getItem("popups")) || {}
        }
        catch(e){
            this.states = {}
        }
    }

    setState(popup, state = true) {
        this.states[popup] = state
        localStorage.setItem("popups", JSON.stringify(this.states))
    }
}