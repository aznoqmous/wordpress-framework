import {
    createHigherOrderComponent,
    InspectorControls,
    PanelBody, SelectControl,
    useBlockProps
} from "../../../../_vendor/addictic/wordpress-framework/assets/js/backend/wp-bootstrapper";

const __ = wp.i18n.__
const namespace = 'dvs/cover-attributes'
const targetBlock = "core/cover"

wp.hooks.addFilter('blocks.registerBlockType', namespace, (settings, name) => {
    if (name === targetBlock) {
        settings.attributes = {
            ...settings.attributes,
            blocStyle: {
                type: "string",
                default: ""
            },
            alignment: {
                type: "string",
                default: ""
            }
        }
    }
    return settings
})

wp.hooks.addFilter(
    'editor.BlockEdit',
    namespace,
    createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            const {attributes, setAttributes, isSelected} = props;

            if (props.name !== targetBlock) return <BlockEdit {...props} />

            const blockProps = useBlockProps({
                className: attributes.blocStyle + " " + attributes.alignment,
            });

            return (
                <div {...blockProps}>
                    <BlockEdit {...props} />
                    {isSelected &&
                        <InspectorControls>
                            <PanelBody title={__('Style de bloc', 'my-plugin')} initialOpen={true}>
                                <SelectControl
                                    label={__('Style de bloc', 'my-plugin')}
                                    options={[
                                        {label: '-', value: ""},
                                        {label: __('Glassmorphisme'), value: 'glassmorphism'},
                                    ]}
                                    value={attributes.blocStyle}
                                    onChange={(value) => setAttributes({blocStyle: value})}
                                />
                                <SelectControl
                                    label={__('Alignement', 'my-plugin')}
                                    options={[
                                        {label: '-', value: ""},
                                        {label: __('Haut'), value: 'align-start'},
                                        {label: __('Centre'), value: 'align-center'},
                                        {label: __('Bas'), value: 'align-end'},
                                    ]}
                                    value={attributes.alignment}
                                    onChange={(value) => setAttributes({alignment: value})}
                                />
                            </PanelBody>
                        </InspectorControls>
                    }
                </div>
            );
        };
    }, 'coverAdvancedControls')
);

wp.hooks.addFilter(
    'blocks.getSaveContent.extraProps',
    namespace,
    (extraProps, blockType, attributes) => {
        if (attributes.blocStyle) extraProps.className += " " + attributes.blocStyle
        if (attributes.alignment) extraProps.className += " " + attributes.alignment
        return extraProps
    }
)