export default class Element extends EventTarget {
    constructor(container, opts = {}) {
        super();
        if (container[this.constructor.name]) return;
        this.container = container;
        this.container[this.constructor.name] = this;
        this.opts = opts;

        this.build();
        this.bind();
        this.start();
    }

    build() {
    }

    bind() {
    }

    start() {
    }

    select(selector, container = null) {
        container = container || this.container
        return container.querySelector(selector);
    }

    selectAll(selector, container = null) {
        container = container || this.container
        return Array.from(container.querySelectorAll(selector));
    }

    create(tagName = "div", attributes = {}, parent = null) {
        const element = document.createElement(tagName);
        for (const key in attributes) element[key] = attributes[key];
        if (parent) parent.append(element);
        return element;
    }

    async post(url, data={}){

        const body = new FormData()
        for(let key in data) body.append(key, data[key])
        return await fetch(`/wp-json${url}`, {
            method: "POST",
            "Content-Type": "application/json",
            body,
        }).then(res => res.json())
    }

    async sleep(time = 0) {
        return new Promise(res => setTimeout(res, time))
    }

    static bind(selector, opts = {}, observe=false) {
        if(observe){
            const observer = new MutationObserver((entries)=>{
                Array.from(document.querySelectorAll(selector)).map(
                    (el) => new this(el, opts),
                )
            })
            observer.observe(document.body, {
                childList: true,
                subtree: true
            })
        }
        return Array.from(document.querySelectorAll(selector)).map(
            (el) => new this(el, opts),
        );
    }
}
