import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";
import Leaflet from 'leaflet'
import "leaflet/dist/leaflet.css"
import regions from "../regions.json"
import turf from "@turf/turf"
export default class RealisationMap extends Element {

    build(){
        this.locations = JSON.parse(this.container.dataset.locations)
        this.map = Leaflet.map(this.select('.map-container'))

        // Leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);

        this.map.setView([47.1,2.4], 6)
        this.markers = this.locations.map(location => {
            return this.addLocation(location)
        })
        this.realisations = JSON.parse(this.container.dataset.realisations)
        this.realisations.map(realisation => this.addRealisation(realisation))

        this.regionsGeoJson = Leaflet.geoJSON(regions, {
            style: {
                weight: 2,
                opacity: 1,
                fillOpacity: 0,
                strokeOpacity: 1,
                color: "var(--color-dark-blue-2)",
            },
            className: "region"
        })

        console.log(this.markers)

        Object.values(this.regionsGeoJson._layers).map(layer => {
            // layer.polygon = turf.polygonize(layer.feature.geometry.coordinates)
            // layer.realisations = this.realisations.filter(realisation => this.isPointInsideRegion(realisation, layer.))
            layer.markers = this.markers.filter(marker => layer.contains(marker.latLng))

            setTimeout(()=> {
                layer._path.addEventListener('mouseenter', ()=>{
                    console.log(layer)
                })
            }, 100)
        })
        this.regionsGeoJson.addTo(this.map)
    }

    addLocation(position){
        const marker = Leaflet.marker(position).addTo(this.map)
        return marker.setIcon(
            Leaflet.divIcon({
                className: "location-point",
                html: "<span></span>"
            })
        )
    }
    addRealisation(realisation){
        if(!realisation.latitude || !realisation.longitude) return;
        const marker = Leaflet.marker([realisation.latitude, realisation.longitude]).addTo(this.map)
        marker.setIcon(
            Leaflet.divIcon({
                className: "realisation-point",
                html: "<span></span>"
            })
        )
    }

    bind(){

    }

    // function isPointInside(lat, lng) {
    //     var point = turf.point([lng, lat]); // Turf expects [lng, lat] order
    //     var polygon = turf.polygon(geojsonFeature.geometry.coordinates);
    //     return turf.booleanPointInPolygon(point, polygon);
    // }
}