export default class MathUtils {

    static getPolygonArea(ring){
        var s = 0.0;
        for(let i= 0; i < (ring.length-1); i++){
            s += (ring[i][0] * ring[i+1][1] - ring[i+1][0] * ring[i][1]);
        }
        return 0.5 *s;
    }
    static getPolygonCenter(ring){
        var c = [0,0];
        for(let i= 0; i < (ring.length-1); i++){
            c[0] += (ring[i][0] + ring[i+1][0]) * (ring[i][0]*ring[i+1][1] - ring[i+1][0]*ring[i][1]);
            c[1] += (ring[i][1] + ring[i+1][1]) * (ring[i][0]*ring[i+1][1] - ring[i+1][0]*ring[i][1]);
        }
        var a = this.getPolygonArea(ring);
        c[0] /= a * 6;
        c[1] /= a * 6;
        return c;
    }

    static getGeoJsonCenter(geoJson){
        const geometry = geoJson.feature.geometry
        const total = [0, 0]
        geometry.coordinates
            .map(ring => (geometry.type === "Polygon") ? this.getPolygonCenter(ring) : this.getPolygonCenter(ring[0]))
            .map(center => {
                total[0] += center[0]
                total[1] += center[1]
            })
        return [total[1]/geometry.coordinates.length, total[0]/geometry.coordinates.length]
    }


    static clamp(v, min=0, max=1){
        return Math.min(max, Math.max(min, v))
    }
    static lerp(a, b, t){
        t = this.clamp(t)
        return (1-t) * a + t * b
    }
    static randomRange(min, max){
        return this.lerp(min, max, Math.random())
    }
}