export default class Parallax {
    constructor(container, opts = {}) {
        if (container.Parallax) return;
        container.Parallax = this;
        this.container = container;
        this.opts = Object.assign(
            {
                pivot: "center",
            },
            opts,
        );
        this.offsetY = this.opts.offsetY || 0
        this._updateRect();
        this._update();
        this._loop();
    }


    _updateRect() {
        this.rect = this.container.getBoundingClientRect();
    }

    _update() {
        this._updateRect();
        this.container.style.setProperty(
            "--parallax",
            (-this.pivot / this.rect.height).toFixed(4),
        );
    }

    _loop() {
        this._update();
        requestAnimationFrame(this._loop.bind(this));
    }

    get pivot() {
        switch (this.opts.pivot) {
            case "top":
                return this.rect.top - this.offsetY;
            case "middle":
            case "center":
                return this.rect.top + this.rect.height / 2 - window.innerHeight / 2;
            case "bottom":
                return this.rect.bottom - window.innerHeight;
        }
        return 0;
    }

    static bind(selector, opts = {}) {
        return [...document.querySelectorAll(selector)].map(
            (el) => new this(el, opts),
        );
    }
}
