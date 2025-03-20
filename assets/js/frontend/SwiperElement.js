import Element from "../../../_vendor/addictic/wordpress-framework/assets/js/libs/element";
import Swiper from 'swiper/bundle'
import 'swiper/css'
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import {Navigation, Pagination} from 'swiper/modules';

export default class SwiperElement extends Element {

    bind() {
        this.swiper = new Swiper(this.container, {
            modules: [Pagination],
            pagination: {
                el: '.swiper-pagination',
            },
            slidesPerView: 1,
            autoplay: {
                delay: 5000,
            },
            spaceBetween: 20
        })
    }

}