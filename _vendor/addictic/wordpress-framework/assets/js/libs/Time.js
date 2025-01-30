export default class Time {
    static get deltaTime() {
        if(!this.delta) {
            this.lastFrame = 0
            this.tick()
            return 0
        }
        return this.delta
    }

    static tick(){
        this.time = performance.now()
        this.delta = (this.time - this.lastFrame) / 1000
        this.lastFrame = this.time
        requestAnimationFrame(this.tick.bind(this))
    }
}