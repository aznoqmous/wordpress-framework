import Leaflet from 'leaflet';
import Position from './position';
import "leaflet/dist/leaflet.css"
import HtmlHelper from './html-helper';

// const tileLayerUrl = "https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png"
// const tileLayerUrl = "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
// const tileLayerUrl = "https://tile.openstreetmap.org/{z}/{x}/{y}.png"
// const tileLayerUrl = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"

export default class Map extends EventTarget {

    constructor(container, opts={}){
        super()
        this.container = container
        this.leafletMap = Leaflet.map(this.container)
        this.opts = Object.assign({
            //tileLayerUrl: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            //tileLayerUrl: "http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
            // tileLayerUrl: "https://igngp.geoapi.fr/tile.php/{layer}/{z}/{x}/{y}.png",
            tileLayerUrl: "https://igngp.geoapi.fr/tile.php/ortho/{z}/{x}/{y}.png",
            startPosition: new Position(47.1,2.4),
            startZoom: 6,
            minZoom: 6,
            maxZoom: 15
        }, opts)

        this.areas = []
        this.currentArea = null
        this.index = 0

        this.init()
        this.build()
        this.bind()
    }

    init(){
        this.tileLayer = Leaflet.tileLayer(this.opts.tileLayerUrl, {
            minZoom: this.opts.minZoom,
            maxZoom: this.opts.maxZoom,
        })
        this.tileLayer.addTo(this.leafletMap)
        this.leafletMap.setView(this.opts.startPosition.toArray(), this.opts.startZoom)
    }
    build(){
        this.controlsContainer = HtmlHelper.create("div", "controls-container")
        this.addAreaButton = HtmlHelper.create("button", "add", { textContent: "Add area" })
        this.controlsContainer.append(this.addAreaButton)
        this.container.append(this.controlsContainer)
    }

    bind(){
        this.leafletMap.addEventListener('click', (e)=>{
            if(e.originalEvent.target != this.container || this.isDraggingMarker) return;
            this.addPoint(e.latlng)
        })
        this.addAreaButton.addEventListener('click', ()=>{
            this.select(this.addArea())
        })

        window.addEventListener('keyup', (e)=>{
            if(e.key == "Delete" && this.currentArea) {
                this.removeArea(this.currentArea)
            }
        })
    }

    addPoint(position){
        if(!this.currentArea || this.currentArea.points.length >= 4) {
            this.select(this.addArea())
        }
        this.currentArea.addPoint(position)
    }

    addArea(){
        const newArea = new Area(this, {
            color: `hsl(${this.index*80},80%,60%)`
        })
        this.areas.push(newArea)
        this.areas.map((p,i)=> p.name = "Area " + i)
        this.index++
        return newArea
    }

    removeArea(area){
        area.remove()
        this.areas.splice(this.areas.indexOf(area), 1)
        if(area == this.currentArea) this.currentArea = null
    }

    select(polygon){
        this.currentArea = polygon
        this.areas.map(p => p.setActive(p == polygon))
    }
}


class Area {
    constructor(map, opts={}){
        this.opts = Object.assign({
            color: "#fff"
        }, opts)
        this.map = map
        this.leafletMap = map.leafletMap
        this.points = []
        this.markers = []

        this.polygon = Leaflet.polygon(this.points, {
            fillColor: this.opts.color,
            color: this.opts.color
        })
        this.polygon.addTo(this.leafletMap)
        this.buildControls()
        this.bind()
    }

    buildControls(){
        this.controls = HtmlHelper.create("div", "area-control")
        this.controls.style.setProperty("--color", this.opts.color)
        this.label = HtmlHelper.create("strong")
        this.areaLabel = HtmlHelper.create("span")
        this.controls.append(this.label, this.areaLabel)
        this.map.controlsContainer.append(this.controls)
    }

    set name(value){
        this.label.textContent = value
    }

    bind(){
        this.polygon.addEventListener("click", ()=> this.map.select(this))
        this.controls.addEventListener("click", ()=> this.map.select(this))
    }

    setActive(state=true){
        this.markers.map(m => {
            if(state) m.addTo(this.leafletMap)
            else m.removeFrom(this.leafletMap)
        })
    }

    addPoint(position){
        this.addMarker(position)
        this.points.push(position)
        this.polygon.setLatLngs(this.points)
        this.updateMarkers()
    }
    removePoint(point){
        this.points.splice(this.points.indexOf(point), 1)
        if(this.points.length <= 0) return this.map.removeArea(this)
        this.polygon.setLatLngs(this.points)
        this.updateMarkers()
    }

    remove(){
        this.polygon.removeFrom(this.leafletMap)
        this.markers.map(marker => marker.removeFrom(this.leafletMap))
        this.controls.remove()
    }

    addMarker(position){
        const marker = Leaflet.marker(position).addTo(this.leafletMap).addTo(this.leafletMap)
        const icon = Leaflet.divIcon({
            className: "area-point",
            html: `<span style="--color: ${this.opts.color}"></span>`
        })
        marker.setIcon(icon)

        this.markers.push(marker)
        marker.addEventListener('click', ()=>{
            if(this.preventDelete) return;
            const index = this.markers.indexOf(marker)
            this.removePoint(this.points[index])
        })
        let map = this.leafletMap
        const removeEvent = ()=>{
            map.dragging.enable()
            map.isDraggingMarker = false
            this.updateMarkers()
            this.leafletMap.removeEventListener('mousemove', markerMove)
            this.leafletMap.removeEventListener('mouseup', removeEvent)
            setTimeout(()=> this.preventDelete = false, 100)
        }
        const markerMove = (e)=>{
            this.preventDelete = true
            marker.setLatLng(e.latlng)
            this.points[this.markers.indexOf(marker)] = e.latlng
            this.polygon.setLatLngs(this.points)
            this.updateArea()
        }
        marker.addEventListener('mousedown', (e)=>{
            map.dragging.disable()
            map.isDraggingMarker = false
            this.leafletMap.addEventListener('mousemove', markerMove)
            this.leafletMap.addEventListener('mouseup', removeEvent)
        })
    }

    updateMarkers(){
        // reorder points
        const center = this.center
        this.points.map(p => {
            p.angle = Math.atan2(p.lat - center.lat, p.lng - center.lng)
        })
        this.points = this.points.sort((a,b)=> a.angle - b.angle)
        this.polygon.setLatLngs(this.points)

        this.markers.map((marker, i)=>{
            if(this.points[i])
                marker.setLatLng(this.points[i])
            else
                marker.removeFrom(this.leafletMap)
        })
        this.markers = this.markers.slice(0, this.points.length)
        this.updateArea()
    }

    updateArea(){
        const area = `${Math.round(this.area)}m²`
        this.areaLabel.textContent = area

        if(this.points.length && !this.areaTooltip) {
            this.areaTooltip = Leaflet.tooltip({direction: "top", permanent: true})
        }

        this.areaTooltip.setOpacity(this.points.length > 3 ? 1 : 0)
        this.areaTooltip.setContent(area)
        this.polygon.bindTooltip(this.areaTooltip)

        this.map.dispatchEvent(new CustomEvent('updateArea', {
            detail: {area: this}
        }))
    }

    get area(){

        // Earth's radius in meters
        const R = 6378137;

        // number of points
        const pointsCount = this.points.length;

        if (pointsCount < 3) return 0;

        let area = 0;

        for (let i = 0; i < pointsCount; i++) {
            let p1 = this.points[i];
            let p2 = this.points[(i + 1) % pointsCount];

            let lat1 = p1.lat * Math.PI / 180;
            let lon1 = p1.lng * Math.PI / 180;
            let lat2 = p2.lat * Math.PI / 180;
            let lon2 = p2.lng * Math.PI / 180;

            area += (lon2 - lon1) * (2 + Math.sin(lat1) + Math.sin(lat2));
        }

        area = (Math.abs(area) * (R * R)) / 2;

        return area;
    }

    get center(){
        return this.polygon.getBounds().getCenter()
    }
}