import "./scss/frontend.scss"
import {Simulator} from "./js/frontend/simulator";
import NumberItem from "./js/frontend/number-item";
import SiteHeader from "./js/frontend/site-header";
import Slider from "./js/frontend/slider";
import RealisationMap from "./js/frontend/realisation-map";
import SwiperElement from "./js/frontend/SwiperElement";

document.addEventListener("DOMContentLoaded", ()=>{
    Simulator.bind(".simulator")
    NumberItem.bind(".wp-block-dvs-number-item")
    Slider.bind(".wp-block-dvs-slider")
    SiteHeader.bind(".site-header")
    RealisationMap.bind(".realisation-map")
    SwiperElement.bind(".swiper")
})