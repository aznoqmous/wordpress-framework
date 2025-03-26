import Block from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/block"
import {Heading, InnerBlocks, InputControl, RichText, TextControl, useBlockProps} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";

export default class ContentBlock extends Block {
    constructor(props) {
        super(props);
        this.title = "Bloc de contenu"
        this.icon = 'text'
        this.category = 'layout'
        this.attributes = {
        }
        this.supports = {
            typography: {
                fontSize: true
            },
            color: {
                gradients: true,
                background: true,
                text: true
            }
        }
    }

    render(props) {
        const {attributes, setAttributes} = props
        const {title} = attributes
        const blockProps = {...useBlockProps()}
        blockProps.className = "wp-block-dvs-content-block"
        return <div {...blockProps}>
            <InnerBlocks allowedBlocks={[
                "core/heading",
                "core/paragraph",
                "dvs/button",
                "dvs/number"
            ]}/>
        </div>
    }

    save(props) {
        const {title} = props.attributes
        return <div className="wp-block-dvs-content-block">
            <InnerBlocks.Content/>
        </div>
    }
}

ContentBlock.register("dvs/content-block")