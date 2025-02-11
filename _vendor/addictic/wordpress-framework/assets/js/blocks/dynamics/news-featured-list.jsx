import {
    InspectorControls,
    PanelBody,
    TextControl,
    Translate,
    useBlockProps
} from "../../backend/wp-bootstrapper";
import DynamicBlock from "../components/dynamic-block";

export default class NewsFeaturedListBlock extends DynamicBlock {
    constructor(props) {
        super(props);
        this.title = "Actualités mises en avant"
        this.icon = 'welcome-learn-more'
        this.category = 'layout'

        this.attributes = {
            newsCount: {type: "integer", default: 3},
        }
    }

    editor(props) {
        const {attributes, setAttributes} = props
        const {newsCount} = props.attributes
        return <>
            <InspectorControls>
                <PanelBody title={Translate('Contenu', "ifc")} initialOpen={true}>
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

NewsFeaturedListBlock.register("wordpress-framework/news-featured-list")
