import "./scss/frontend.scss"
import {Simulator} from "./js/frontend/simulator";
import NumberItem from "./js/frontend/number-item";

console.log("hello")

document.addEventListener('DOMContentLoaded', ()=>{
    Simulator.bind(".simulator")
    NumberItem.bind(".wp-block-dvs-number-item")
})