/**
 * Add containerStyle top core/block
 */

import {
    createHigherOrderComponent,
    InspectorControls,
    PanelBody, SelectControl,
    useBlockProps
} from "../../backend/wp-bootstrapper";

const __ = wp.i18n.__
const namespace = 'ifc/block-background-attribute'
const targetBlock = "core/cover"
const fieldName = "containerBottomStyle"

wp.hooks.addFilter('blocks.registerBlockType', namespace, (settings, name) => {
    if (name === targetBlock) {
        settings.attributes = {
            ...settings.attributes,
            [fieldName]: {
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

            if(props.name !== targetBlock) return  <BlockEdit {...props} />

            const blockProps = useBlockProps({
                className: attributes[fieldName],
            });

            return (
                <div {...blockProps}>
                    <BlockEdit {...props} />
                    {isSelected &&
                        <InspectorControls>
                            <PanelBody title={__('Styles de blocs', 'my-plugin')} initialOpen={true}>
                                <SelectControl
                                    label={__('Bas du bloc', 'my-plugin')}
                                    options={[
                                        {label: '-', value: ""},
                                        {label: __('Arrondi vers le bas'), value: 'round-toward-bottom'},
                                        {label: __('Arrondi vers le haut'), value: 'round-toward-top'},
                                    ]}
                                    value={attributes[fieldName]}
                                    onChange={(value) => setAttributes({[fieldName]: value})}
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
        const value = attributes[fieldName]
        if(value) extraProps.className += " " + value
        return extraProps
    }
)