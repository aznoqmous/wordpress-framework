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
import IconControl from "../components/icon-control";

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
            icon: {
                type: "string",
                default: null
            }
        }
        this.supports = {
            align: ["left", "center", "right"],
            color: {
                color: true
            }
        }
    }

    editor(props) {
        const {attributes, setAttributes} = props
        return <InspectorControls>
            <PanelBody title={'Style de bloc'} initialOpen={true}>
                <IconControl name="icons" selectedIcon={attributes.icon}
                             onUpdate={(icon) => setAttributes({icon})}></IconControl>
            </PanelBody>
        </InspectorControls>
    }

    render(props) {
        const {attributes, setAttributes} = props
        const {content} = attributes
        const blockProps = {...useBlockProps()}
        blockProps.className = "wp-block-button-container align" + attributes.align
        return <div {...blockProps}>
            <div className="wp-block-button">
                <div className="button-placeholder">
                    <RichText
                        tagName="strong"
                        value={content}
                        placeholder="Ajouter un texte..."
                        onChange={(content) => setAttributes({content})}
                    />
                    {attributes.icon && <svg width="32" height="32" data-icon={attributes.icon}>
                        <use xlinkHref={"/icons/icons.svg#" + attributes.icon}></use>
                    </svg>}
                </div>
            </div>
        </div>
    }

    save(props) {
        const {attributes} = props
        const html = document.createRange().createContextualFragment(attributes.content).children[0]
        return <div className="wp-block-button-container">
            <div className="wp-block-button">
                {html && (<a href={html.href}>
                    <span>{html.textContent}</span>
                    {attributes.icon && <svg width="32" height="32" data-icon={attributes.icon}>
                        <use xlinkHref={"/icons/icons.svg#" + attributes.icon}></use>
                    </svg>}
                </a>)}
                {!html && (attributes.content)}
            </div>
        </div>
    }
}

Button.register("dvs/button")