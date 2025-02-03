/**
 * Add containerStyle top core/block
 */
import {createHigherOrderComponent} from "@wordpress/compose";
import {Fragment} from "react";
import {InspectorControls, PanelBody, SelectControl, useBlockProps} from "../../backend/wp-bootstrapper";

const __ = wp.i18n.__
const namespace = 'dvs/block-background-attribute'
const targetBlock = "core/group"
const {addFilter} = wp.hooks

/** Add custom attribute */
addFilter('blocks.registerBlockType', namespace, (settings, name) => {
    if (name === targetBlock) {
        settings.attributes = {
            ...settings.attributes,
            containerBottomStyle: {
                type: "string",
                default: ""
            },
            background: {
                type: "string",
                default: ""
            }
        }
    }
    return settings
})

/** Modify component render behaviour */
addFilter(
    'editor.BlockEdit',
    namespace,
    createHigherOrderComponent((BlockEdit)=> (props) => {
        const {attributes, setAttributes, isSelected} = props;
        if(props.name !== targetBlock) return  <BlockEdit {...props} />
        const blockProps = useBlockProps({
            className: attributes.containerBottomStyle + " " + attributes.background,
        });
        return (
            <div {...blockProps}>
                <BlockEdit {...props} />
                {isSelected && <>
                    <InspectorControls>
                        <PanelBody title={__('Styles de blocs', 'my-plugin')} initialOpen={true}>
                            <SelectControl
                                label={__('Arrière-plan', 'my-plugin')}
                                options={[
                                    {label: '-', value: ""},
                                    {label: __("Forme DVS"), value: 'dvs-shape'},
                                ]}
                                value={attributes.background}
                                onChange={(value) => setAttributes({background: value})}
                            />
                        </PanelBody>
                    </InspectorControls>
                </>
                }
            </div>
        )
    }, 'coverAdvancedControls')

);

/** Handle component save */
addFilter(
    'blocks.getSaveContent.extraProps',
    namespace,
    (extraProps, blockType, attributes) => {
        if(attributes.background) extraProps.className += " " + attributes.background
        return extraProps
    }
)
