import Block from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/block";
import {
    InspectorControls,
    PanelBody,
    SelectControl
} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";
import IconControl from "../components/icon-control";

export default class Icon extends Block {
    constructor(props) {
        super(props);
        this.title = "Icône"
        this.icon = 'info-outline'
        this.category = 'layout'

        this.attributes = {
            icon: {
                type: "string",
                default: null
            }
        }

        this.supports = {
            align: ["left", "center", "right"],
            color: {
                background: false
            }
        }
    }

    editor(props){
        const {attributes, setAttributes} = props

        return <InspectorControls>
            <PanelBody title={'Style de bloc'} initialOpen={true}>
                <IconControl name="icons" selectedIcon={attributes.icon} onUpdate={(icon)=> setAttributes({icon})}></IconControl>
            </PanelBody>
        </InspectorControls>
    }

    render(props) {
        const {attributes, setAttributes} = props
        return <svg width="32" height="32" data-icon={attributes.icon}>
            <use xlinkHref={"/icons/icons.svg#" + attributes.icon}></use>
        </svg>
    }
}

Icon.register("dvs/icon")