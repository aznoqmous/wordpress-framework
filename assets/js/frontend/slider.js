import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";

export default class Slider extends Element {
    build(){
        this.index = 0
        this.slides = this.selectAll("figure")
        this.updateSlides()
    }

    bind() {
        this.slides.map((slide,i) => {
           slide.addEventListener('click', ()=> {
               this.index = i
               this.updateSlides()
           })
        })
    }

    getIndex(index){
        if(index < 0) index = this.slides.length - (Math.abs(index) % this.slides.length)
        return index % this.slides.length
    }

    updateSlides(){
        this.slides.map(slide => {
            slide.classList.remove('prev')
            slide.classList.remove('active')
            slide.classList.remove('next')
        })
        const prevIndex = this.getIndex(this.index-1)
        const currentIndex = this.getIndex(this.index)
        const nextIndex = this.getIndex(this.index+1)
        this.slides[prevIndex].classList.add('prev')
        this.slides[currentIndex].classList.add('active')
        this.slides[nextIndex].classList.add('next')
    }
}