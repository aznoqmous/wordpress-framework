import {
    InspectorControls,
    PanelBody,
    TextControl,
    Translate,
    useBlockProps
} from "../../backend/wp-bootstrapper";
import DynamicBlock from "../components/dynamic-block";

export default class NewsLatestListBlock extends DynamicBlock {
    constructor(props) {
        super(props);
        this.title = "Dernières actualités"
        this.icon = 'welcome-learn-more'
        this.category = 'layout'

        this.attributes = {
            newsCount: {type: "integer", default: 3},
        }
    }

    editor(props) {
        const __ = wp.i18n.__
        const {attributes, setAttributes} = props
        const {newsCount} = props.attributes
        return <>
            <InspectorControls>
                <PanelBody title={__('Contenu', "ifc")} initialOpen={true}>
                    <TextControl
                        value={newsCount}
                        type="number"
                        label={Translate("Nombre d'éléments à afficher")}
                        onChange={(value) => setAttributes({ newsCount: parseInt(value) })}
                    ></TextControl>
                </PanelBody>
            </InspectorControls>
        </>
    }
}

NewsLatestListBlock.register("ifc/news-latest-list")
