if (!wp.blockEditor) wp.blockEditor = {
    InspectorControls: "",
    InnerBlocks: "",
    RichText: "",
    useBlockProps: "",
    MediaUpload: "",
    MediaUploadCheck: "",
    BlockControls: ""
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
    Heading: "",
    Popover: "",
    ToolbarButton: ""
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
    MediaUpload,
    MediaUploadCheck,
    useBlockProps,
    BlockControls
} = wp.blockEditor

const {createHigherOrderComponent} = wp.compose

const {
    PanelBody,
    SelectControl,
    TextControl,
    TextareaControl,
    Heading,
    InputControl,
    Button,
    Popover,
    ToolbarButton
} = wp.components

const {
    Fragment,
    cloneElement
} = wp.element;

const ServerSideRender = wp.serverSizeRender

const Translate = (message) => wp.i18n.__(message,
    "ifc")

export {
    BlockControls,
    Button,
    Popover,
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
    MediaUploadCheck,
    createHigherOrderComponent,
    Fragment,
    cloneElement,
    Translate,
    ServerSideRender,
    ToolbarButton
}