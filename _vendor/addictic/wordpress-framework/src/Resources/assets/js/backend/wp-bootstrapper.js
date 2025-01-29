if (!wp.blockEditor) wp.blockEditor = {
    InspectorControls: "",
    InnerBlocks: "",
    RichText: "",
    useBlockProps: ""
}
if (!wp.compose) wp.compose = {
    createHigherOrderComponent: () => () => {
    }
}
if (!wp.components) wp.components = {
    PanelBody: "",
    SelectControl: "",
    TextControl: "",
    InputControl: "",
    TextareaControl: "",
    MediaUpload: "",
    Heading: ""
}
if (!wp.element) wp.element = {
    Fragment: "",
    cloneElement: ""
}
if(!wp.serverSizeRender) wp.serverSizeRender = ()=>{}
const {
    InspectorControls,
    InnerBlocks,
    RichText,
    useBlockProps
} = wp.blockEditor
const {createHigherOrderComponent} = wp.compose
const {
    PanelBody,
    SelectControl,
    TextControl,
    TextareaControl,
    MediaUpload,
    Heading,
    InputControl
} = wp.components
const {
    Fragment,
    cloneElement
} = wp.element;

const ServerSideRender = wp.serverSizeRender

const Translate = (message) => wp.i18n.__(message,
    "ifc")

export {
    InspectorControls,
    InnerBlocks,
    Heading,
    SelectControl,
    InputControl,
    RichText,
    useBlockProps,
    PanelBody,
    TextControl,
    TextareaControl,
    MediaUpload,
    createHigherOrderComponent,
    Fragment,
    cloneElement,
    Translate,
    ServerSideRender
}