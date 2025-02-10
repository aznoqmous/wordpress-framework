import Block from "../../../../_vendor/addictic/wordpress-framework/assets/js/blocks/components/block"
import {
    Button,
    Heading,
    InnerBlocks,
    InputControl,
    RichText,
    TextControl,
    useBlockProps,
    MediaUpload,
    MediaUploadCheck
} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";

export default class Slider extends Block {
    constructor(props) {
        super(props);
        this.title = "Slider"
        this.icon = 'slides'
        this.category = 'layout'
        this.attributes = {
            medias: {
                type: "string",
                default: ""
            }
        }
    }

    render(props) {
        const {attributes, setAttributes} = props
        let {medias} = props.attributes
        if(medias) medias = JSON.parse(medias)

        const blockProps = {...useBlockProps()}
        blockProps.className = "wp-block-dvs-slider"
        const ALLOWED_MEDIA_TYPES = ["image/*"]
        return <div {...blockProps}>
            <div className="container">
                {medias && medias.map((media, i) => (
                    <figure className={!i ? "active": ""} key={i}>
                        <img src={media.sizes.full.url} alt={media.caption}/>
                        <figcaption>{media.caption}</figcaption>
                    </figure>
                ))}
            </div>
            <MediaUploadCheck>
                <MediaUpload
                    onSelect={(_medias) => setAttributes({medias: JSON.stringify(_medias)})}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    value={medias}
                    multiple={true}
                    render={({open}) => (
                        <Button onClick={open}>Sélectionner des images</Button>
                    )}
                />
            </MediaUploadCheck>
        </div>
    }

    save(props) {
        let {medias} = props.attributes
        if(medias) medias = JSON.parse(medias)
        return <div className="wp-block-dvs-slider">
            <div className="container">
                {medias && medias.map((media, i) => (
                    <figure className={!i ? "active": ""} key={i}>
                        <img src={media.sizes.full.url} alt={media.alt}/>
                        <figcaption>{media.caption}</figcaption>
                    </figure>
                ))}
            </div>
        </div>
    }
}

Slider.register("dvs/slider")