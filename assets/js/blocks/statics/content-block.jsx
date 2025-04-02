import Block from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/block"
import {
    CheckboxControl,
    Heading,
    InnerBlocks,
    InputControl,
    InspectorControls, PanelBody,
    RichText,
    TextControl,
    useBlockProps
} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";
import IconControl from "../components/icon-control";

export default class ContentBlock extends Block {
    constructor(props) {
        super(props);
        this.title = "Bloc de contenu"
        this.icon = 'text'
        this.category = 'layout'

        this.attributes = {
            align: {
                type: 'string',
                default: 'right'
            },
            hasBorder: {
                type: 'boolean',
                default: false
            },
            icon: {
                type: "string",
                default: null
            }
        }

        this.supports = {
            typography: {
                fontSize: true
            },
            color: {
                gradients: true,
                background: true,
                text: true
            },
            layout: {
                allowCustomContentAndWideSize: false,
                allowJustification: true
            }
        }
    }

    editor(props) {
        const {attributes, setAttributes} = props
        return < InspectorControls>
            < PanelBody
                title={'Style de bloc'}
                initialOpen={true}>
                < CheckboxControl
                    label={"Contour"}
                    checked={attributes.hasBorder}
                    onChange={(value)=> setAttributes({hasBorder: value})}
                >
                </CheckboxControl>
                <IconControl name="icons" selectedIcon={attributes.icon}
                             onUpdate={(icon) => setAttributes({icon})}></IconControl>
            </PanelBody>
        </InspectorControls>
    }

    render(props) {
        const {attributes} = props
        const blockProps = useBlockProps()
        blockProps.className += " wp-block-dvs-content-block"
        if (props.attributes.hasBorder) blockProps.className += " has-border"
        if (props.attributes.icon) blockProps.className += " has-icon"

        return <div {...blockProps}>
            {attributes.icon && <svg width="32" height="32" data-icon={attributes.icon}>
                <use xlinkHref={"/icons/icons.svg#" + attributes.icon}></use>
            </svg>}
            <div className="content">
            <InnerBlocks allowedBlocks={[
                "core/heading",
                "core/paragraph",
                "dvs/button",
                "dvs/number",
                "dvs/icon"
            ]}/>
            </div>
        </div>
    }

    save(props) {
        const {attributes} = props
        const blockProps = useBlockProps.save()
        if (props?.attributes?.layout?.justifyContent)
            blockProps.className += " is-layout-flex is-content-justification-" + props.attributes.layout.justifyContent
        if (props.attributes.hasBorder) blockProps.className += " has-border"
        if (props.attributes.icon) blockProps.className += " has-icon"
        return <div {...blockProps}>
            {attributes.icon && <svg width="32" height="32" data-icon={attributes.icon}>
                <use xlinkHref={"/icons/icons.svg#" + attributes.icon}></use>
            </svg>}
            <div className="content">
            <InnerBlocks.Content/>
            </div>
        </div>
    }
}

ContentBlock.register("dvs/content-block")