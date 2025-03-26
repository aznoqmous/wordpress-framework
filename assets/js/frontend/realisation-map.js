import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";
import Leaflet from 'leaflet'
import "leaflet/dist/leaflet.css"
import regions from "../regions.json"

import * as turf from "@turf/turf"
import "leaflet-active-area"

export default class RealisationMap extends Element {

    build() {
        this.locations = this.getJsonData(this.container.dataset.locations)
        this.map = Leaflet.map(this.select('.map-container'), {
            maxZoom: 6,
            minZoom: 6,
            startZoom: 6
        })
        this.map.setView([47.1, 2.4], 6)
        // console.log(this.map.setActiveArea)
        this.map.setActiveArea("active-area", true, true)
        // Leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);


        if(!this.locations) return;
        this.markers = this.locations.map(location => {
            return this.addLocation(location)
        })

        this.realisations = this.getJsonData(this.container.dataset.realisations)
        if(!this.realisations) return;
        this.realisations.map(realisation => this.addRealisation(realisation))

        this.regions = []
        this.regions = regions.features.map(region => {
            const regionGeoJson = Leaflet.geoJSON(region, {
                style: {
                    weight: 4,
                    opacity: 1,
                    fillOpacity: 0,
                    strokeOpacity: 1,
                    color: "var(--color-dark-blue-2)"
                },
                className: "region"
            }).addTo(this.map)

            region.markers = this.markers.filter(marker => turf.booleanPointInPolygon(marker.point, region))

            this.lastRegion = null
            const layers = Object.values(regionGeoJson._layers)
            regionGeoJson.path = layers[0]._path

            regionGeoJson.path.addEventListener('click', () => {

                this.regions.map(region => region.path.classList.toggle("active", region == regionGeoJson))

                this.map.setView(layers[0].getCenter())
                if (this.lastRegion != region) {
                    this.markers.map(marker => {
                        marker.state = false
                        marker._icon.classList.remove('active')
                    })
                }
                this.lastRegion = region
                region.markers.map(marker => {
                    marker.state = true
                    setTimeout(() => marker._icon.classList.toggle('active', marker.state), Math.random() * 500)
                })
            })

            return regionGeoJson
        })
        // const load = async()=> {
        //     const data = await this.loadGeoJSON(regions, (feature, layer)=> {
        //         // console.log(feature, layer)
        //         const polygon = turf.polygon(layer.feature.geometry.coordinates)
        //     })
        //     console.log(regions)
        //     this.regions = regions.features.map(async region => {
        //         setTimeout(()=>{
        //             console.log(region.geometry.coordinates)
        //             const polygon = turf.polygon(region.geometry.coordinates)
        //         }, 1000)
        //     })
        // }
        // load()


        // this.regionsGeoJson = Leaflet.geoJSON(regions, {
        //     style: {
        //         weight: 2,
        //         opacity: 1,
        //         fillOpacity: 0,
        //         strokeOpacity: 1,
        //         color: "var(--color-dark-blue-2)",
        //     },
        //     className: "region"
        // })
        // setTimeout(() => {
        //     Object.values(this.regionsGeoJson._layers).map(layer => {
        //         try {
        //
        //             layer.polygon = turf.polygon(layer.feature.geometry.coordinates)
        //             // layer.realisations = this.realisations.filter(realisation => this.isPointInsideRegion(realisation, layer.))
        //             layer.markers = this.markers.filter(marker => turf.booleanPointInPolygon(marker.point, layer.polygon))
        //
        //             layer._path.addEventListener('mouseenter', () => {
        //                 console.log(layer.markers.length)
        //             })
        //         }
        //         catch(e){
        //             console.error(layer)
        //         }
        //     })
        // }, 1000)
        // this.regionsGeoJson.addTo(this.map)
    }

    async loadPolygon(coordinates) {
        return new Promise(res => {
            const load = () => {
                try {
                    const polygon = turf.polygon(coordinates)
                    return res(polygon)
                } catch (e) {
                    setTimeout(() => load(), 1000)
                }
            }
            load()
        })
    }

    getJsonData(data){
        try {
            return JSON.parse(data)
        }
        catch(e) {
            return null
        }
    }

    addLocation(position) {
        const marker = Leaflet.marker(position).addTo(this.map)
        marker.point = [position[1], position[0]]
        return marker.setIcon(
            Leaflet.divIcon({
                className: "location-point",
                html: "<span></span>"
            })
        )
    }

    addRealisation(realisation) {
        if (!realisation.latitude || !realisation.longitude) return;
        const marker = Leaflet.marker([realisation.latitude, realisation.longitude]).addTo(this.map)
        marker.setIcon(
            Leaflet.divIcon({
                className: "realisation-point",
                html: "<span></span>"
            })
        )
    }

    bind() {

    }

    inside(point, vs) {
        // ray-casting algorithm based on
        // https://wrf.ecse.rpi.edu/Research/Short_Notes/pnpoly.html

        var x = point[0], y = point[1];

        var inside = false;
        for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
            var xi = vs[i][0], yi = vs[i][1];
            var xj = vs[j][0], yj = vs[j][1];

            var intersect = ((yi > y) != (yj > y))
                && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }

        return inside;
    }

    insideLayer(point, layer) {
        return layer.geometry.coordinates.some(coords => this.inside(point, coords))
    }

    // function isPointInside(lat, lng) {
    //     var point = turf.point([lng, lat]); // Turf expects [lng, lat] order
    //     var polygon = turf.polygon(geojsonFeature.geometry.coordinates);
    //     return turf.booleanPointInPolygon(point, polygon);
    // }

    refresh(){
        this.map.setActiveArea("active-area", true, true)
    }
}