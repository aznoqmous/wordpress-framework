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

    render(props) {
        const {attributes, setAttributes} = props

        const blockProps = useBlockProps()
        blockProps.className += " wp-block-dvs-content-block"
        if(props.attributes.hasBorder) blockProps.className += " has-border"

        return <div {...blockProps}>
            <InnerBlocks allowedBlocks={[
                "core/heading",
                "core/paragraph",
                "dvs/button",
                "dvs/number"
            ]}/>
            <InspectorControls>
                <PanelBody title={'Style de bloc'} initialOpen={true}>
                    <CheckboxControl
                        label={"Contour"}
                        checked={attributes.hasBorder}
                        onChange={(value) => setAttributes({hasBorder: value})}
                    >
                    </CheckboxControl>
                </PanelBody>
            </InspectorControls>
        </div>
    }

    save(props) {
        const blockProps = useBlockProps.save()
        if (props?.attributes?.layout?.justifyContent)
            blockProps.className += " is-layout-flex is-content-justification-" + props.attributes.layout.justifyContent
        if(props.attributes.hasBorder) blockProps.className += " has-border"
        return <div {...blockProps}>
            <InnerBlocks.Content/>
        </div>
    }
}

ContentBlock.register("dvs/content-block")