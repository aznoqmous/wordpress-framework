import "../web/framework/backend.js"
import "../web/framework/backend.css"
import "./scss/backend.scss"

import "./js/blocks/dynamics/simulator"
import "./js/blocks/dynamics/testimony"
import "./js/blocks/dynamics/realisation-map"
import "./js/blocks/dynamics/realisations-filtered"
import "./js/blocks/dynamics/resource-list-filtered"
import "./js/blocks/statics/number-item"
import "./js/blocks/statics/slider"
import "./js/blocks/statics/button"
import "./js/blocks/statics/content-block"
import "./js/blocks/statics/icon"

import "./js/blocks/overrides/cover"

import LocationField from "./js/location-field"
import TreeView from "./tree-view";
import SvgSpriteField from "./svg-sprite-field";
LocationField.bind(".location-field")
TreeView.bind(".tree-view")

SvgSpriteField.bind(".svg_sprite_field-field")

/**
 * Admin post page
 */
document.addEventListener("DOMContentLoaded", ()=>{
    const form = document.querySelector(".metabox-location-normal")
    document.body.style.setProperty("--background-color", Object.fromEntries(new FormData(form)).background_color || null)
    document.querySelectorAll('.post-type-page [name="background_color"]').forEach((el) => {
        el.addEventListener("change", (e)=>{
            document.body.style.setProperty("--background-color", Object.fromEntries(new FormData(form)).background_color || null)
        })
    })

    document.body.style.setProperty("--text-color", Object.fromEntries(new FormData(form)).text_color || null)
    document.querySelectorAll('.post-type-page [name="text_color"]').forEach((el) => {
        el.addEventListener("change", (e)=>{
            document.body.style.setProperty("--text-color", Object.fromEntries(new FormData(form)).text_color || null)
        })
    })
})