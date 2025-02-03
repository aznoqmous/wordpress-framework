import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";
import MathUtils from "../libs/math-utils";

export default class NumberItem extends Element {

    build() {

        this.finalContent = this.select('strong').innerText
        this.content = this.finalContent


        this.targetNumbers = ParsedNumber.multipleFromString(this.content)

        /* add decimals if too few increments */
        this.targetNumbers
            .filter(number => !number.decimalsCount)
            .map(number => {
                number.decimalsCount = number.increments < 5 ? 2 : 0
            })

        this.targetNumbers.map(number => this.content = this.content.replace(number.input, "<b></b>"))

        this.targetElement = this.select('strong')
        this.targetElement.innerHTML = this.content
        this.targetElements = this.selectAll('strong b')
        this.duration = 4000

    }

    bind() {
        const observer = new IntersectionObserver((entries) => {
            for (let entry of entries) {
                if (entry.isIntersecting) this.animate()
            }
        })
        observer.observe(this.container)
    }

    animate() {
        this.startTime = Date.now()
        if (this.timeout) clearTimeout(this.timeout)
        this.loop()
    }

    get life() {
        return (Date.now() - this.startTime) / this.duration
    }

    loop() {
        if (this.life < 1) {
            // this.setHtml(Math.floor(this.life * this.targetNumber))
            this.targetNumbers.map(number => {
                let value = (this.life * number.float).toFixed(number.decimalsCount)
                if(number.hasSpaces) value = ParsedNumber.addSpaces(value)
                this.targetElements[0].innerHTML = value
            })

            this.timeout = setTimeout(() => this.loop())
        } else this.setHtml(this.finalContent)
    }

    setHtml(value) {
        this.targetElement.innerHTML = value || ""
    }
}

class ParsedNumber {
    constructor(number) {
        this.input = number
        this.string = number.replace(/[^0-9\. ]/, "").trim()
        this.flatString = this.string.replace(/ /, "")
        this.integer = parseInt(this.flatString)
        this.float = parseFloat(this.flatString)
        this.isFloat = this.integer !== this.float
        this.decimals = parseInt((this.float + "").split(".")[1]) || null
        this.decimalsCount = this.decimals ? (this.decimals + "").length : 0
        this.increments = this.float * Math.pow(10, this.decimalsCount)

        this.hasSpaces = !!this.input.match(" ")
    }

    static addSpaces(value){
        if(!this.spaceFormator) this.spaceFormator = new Intl.NumberFormat("fr")
        return this.spaceFormator.format(value)
    }

    static multipleFromString(string) {
        return string.match(/[0-9\.\ ]*/g).filter(val => val.length).map(val => new ParsedNumber(val.trim())).filter(v => v.float)
    }
}