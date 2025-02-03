
export default class Position {
    constructor(lat, lng) {
        this.lat = lat
        this.lng = lng
    }

    toString() {
        return `${this.lat},${this.lng}`
    }

    toArray() {
        return [this.lat, this.lng]
    }

    toUrl() {
        return `${this.lat}%2C${this.lng}`
    }

    static fromString(string) {
        if (!string.length || string.match(/[a-zA-Z]/)) return null;
        const parts = string.split(',').filter(v => v.length).map(v => parseFloat(v))
        return Position.fromArray(parts)
    }

    static fromArray(array) {
        if (array.length < 2) return null;
        return new Position(array[0], array[1])
    }

    static fromLatLng(latLng) {
        return new Position(latLng.lat, latLng.lng)
    }
}