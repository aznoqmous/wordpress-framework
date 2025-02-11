import Block from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/block"
import {
    ToolbarButton,
    BlockControls,
    Heading,
    InnerBlocks,
    InputControl,
    RichText,
    TextControl,
    useBlockProps, PanelBody, SelectControl, InspectorControls
} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";
import icons from "../../libs/icons.json"

export default class Button extends Block {
    constructor(props) {
        super(props);
        this.title = "Bouton"
        this.icon = 'button'
        this.category = 'layout'
        this.attributes = {
            content: {
                type: "string",
                default: "Ajouter un texte..."
            },
            blocStyle: {
                type: "string",
                default: ""
            },
        }
        this.supports = {
            align: ["left", "center", "right"],
            color: {
                color: true
            }
        }
    }

    render(props) {
        const {attributes, setAttributes} = props
        const {content} = attributes
        const blockProps = {...useBlockProps()}
        blockProps.className = "wp-block-button-container align" + attributes.align
        return <div {...blockProps}>
            <div className="wp-block-button">
                <div className="button-placeholder">
                    {attributes.blocStyle == "back" && <div dangerouslySetInnerHTML={{__html: icons.back}}></div>}
                    <RichText
                        tagName="strong"
                        value={content}
                        placeholder="Ajouter un texte..."
                        onChange={(content) => setAttributes({content})}
                    />
                    {attributes.blocStyle == "external" && <div dangerouslySetInnerHTML={{__html: icons.external}}></div>}
                </div>
                <InspectorControls>
                    <PanelBody title={'Style de bloc'} initialOpen={true}>
                        <SelectControl
                            label={'Style de bloc'}
                            options={[
                                {label: '-', value: ""},
                                {label: 'Retour', value: 'back'},
                                {label: 'Externe', value: 'external'},
                            ]}
                            value={attributes.blocStyle}
                            onChange={(value) => setAttributes({blocStyle: value})}
                        />
                    </PanelBody>
                </InspectorControls>
            </div>
        </div>
    }

    save(props) {
        const {content} = props.attributes
        const html = document.createRange().createContextualFragment(content).children[0]
        return <div className="wp-block-button-container">
            <div className="wp-block-button">
                {html && (<a href={html.href}>
                    {props.attributes.blocStyle == "back" && <div dangerouslySetInnerHTML={{__html: icons.back}}></div>}
                    <span>{html.textContent}</span>
                    {props.attributes.blocStyle == "external" && <div dangerouslySetInnerHTML={{__html: icons.external}}></div>}
                </a>)}
            </div>
        </div>
    }
}

Button.register("dvs/button")