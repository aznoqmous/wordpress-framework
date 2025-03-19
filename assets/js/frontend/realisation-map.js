import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";
import Leaflet from 'leaflet'
import "leaflet/dist/leaflet.css"
export default class RealisationMap extends Element {

    build(){
        this.locations = JSON.parse(this.container.dataset.locations)
        this.map = Leaflet.map(this.select('.map-container'))

        Leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);

        this.map.setView([47.1,2.4], 6)
        this.markers = this.locations.map(location => {
            if(Math.random() < 0.3) this.addRealisation({}, location)
            else this.addLocation(location)
        })
    }

    addLocation(position){
        const marker = Leaflet.marker(position).addTo(this.map)
        marker.setIcon(
            Leaflet.divIcon({
                className: "location-point",
                html: "<span></span>"
            })
        )
    }
    addRealisation(realisation, position){
        const marker = Leaflet.marker(position).addTo(this.map)
        marker.setIcon(
            Leaflet.divIcon({
                className: "realisation-point",
                html: "<span></span>"
            })
        )
    }

    bind(){

    }

}