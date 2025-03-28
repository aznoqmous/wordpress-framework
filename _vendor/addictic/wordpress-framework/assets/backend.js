import "./scss/backend.scss"

import RelationField from "./js/backend/relation-field";
import UploadField from "./js/backend/upload-field";
import IconField from "./js/backend/icon-field";
import ColorField from "./js/backend/color-field";
import ListField from "./js/backend/list-field";
import LocationField from "./js/backend/location-field";
// import "./js/blocks/overrides/group.jsx"
// import "./js/blocks/overrides/cover.jsx"

import "./js/blocks/dynamics/news-featured-list.jsx"
import "./js/blocks/dynamics/news-filtered-list.jsx"
import "./js/blocks/dynamics/news-latest-list.jsx"


RelationField.bind(".relation-field")
UploadField.bind(".upload-field")
IconField.bind(".icon-field")
ColorField.bind(".color-field")
ListField.bind(".list-field")
LocationField.bind(".location-field")

import "./js/backend/post-type-form"

console.log("Wordpress Framework")