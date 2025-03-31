import Block from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/block"
import {Heading, InnerBlocks, InputControl, RichText, TextControl, useBlockProps} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";

export default class NumberItem extends Block {
    constructor(props) {
        super(props);
        this.title = "Chiffre clé"
        this.icon = 'performance'
        this.category = 'layout'
        this.attributes = {
            number: {
                type: "string",
                default: "Titre"
            }
        }
        this.supports = {
            typography: {
                fontSize: true
            }
        }
    }

    render(props) {
        const {attributes, setAttributes} = props
        const {number} = attributes
        const blockProps = {...useBlockProps()}
        blockProps.className = "wp-block-dvs-number"
        return <div {...blockProps}>
            <RichText
                tagName="strong"
                value={number}
                placeholder="Titre"
                onChange={(number) => setAttributes({number})}
            />
        </div>
    }

    save(props) {
        const {number} = props.attributes
        const blockProps = useBlockProps.save()
        return <div {...blockProps}>
            <RichText.Content
                tagName="strong"
                value={number}
            />
        </div>
    }
}

NumberItem.register("dvs/number")