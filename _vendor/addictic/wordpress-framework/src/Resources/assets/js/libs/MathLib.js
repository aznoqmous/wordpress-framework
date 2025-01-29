export default class MathLib {
    static clamp(value, min, max) {
        return Math.min(Math.max(value, min), max)
    }

    static lerp(a, b, t){
        t = Math.min(1, t)
        return b * t + (1 - t) * a
    }

    static randomRange(min, max){
        return this.lerp(min, max, Math.random())
    }
}
