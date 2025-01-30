/**
 * Add containerStyle top core/block
 */
import {createHigherOrderComponent} from "@wordpress/compose";
import {Fragment} from "react";
import {InspectorControls, PanelBody, SelectControl, useBlockProps} from "../../backend/wp-bootstrapper";

const __ = wp.i18n.__
const namespace = 'ifc/block-background-attribute'
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
            beeHive: {
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
            className: attributes.containerBottomStyle + " " + attributes.beeHive,
        });
        return (
            <div {...blockProps}>
                <BlockEdit {...props} />
                {isSelected && <>
                    <InspectorControls>
                        <PanelBody title={__('Styles de blocs', 'my-plugin')} initialOpen={true}>
                            <SelectControl
                                label={__('Bas du bloc', 'my-plugin')}
                                options={[
                                    {label: '-', value: ""},
                                    {label: __('Arrondi vers le bas'), value: 'round-toward-bottom'},
                                    {label: __('Arrondi vers le haut'), value: 'round-toward-top'},
                                ]}
                                value={attributes.containerBottomStyle}
                                onChange={(value) => setAttributes({containerBottomStyle: value})}
                            />
                            <SelectControl
                                label={__('Nid d\'abeilles', 'my-plugin')}
                                options={[
                                    {label: '-', value: ""},
                                    {label: __("Nid d'abeilles à gauche"), value: 'bee-hive-left'},
                                    {label: __("Nid d'abeilles à droite"), value: 'bee-hive-right'},
                                    {label: __("Nid d'abeilles en fond"), value: 'bee-hive-wide'},
                                    {label: __("Nid d'abeilles des côtés"), value: 'bee-hive-both'},
                                    {label: __("Nid d'abeilles à gauche - orange"), value: 'bee-hive-left bee-hive-orange'},
                                    {label: __("Nid d'abeilles à droite - orange"), value: 'bee-hive-right bee-hive-orange'},
                                ]}
                                value={attributes.beeHive}
                                onChange={(value) => setAttributes({beeHive: value})}
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
        if(attributes.containerBottomStyle) extraProps.className += " " + attributes.containerBottomStyle
        if(attributes.beeHive) extraProps.className += " " + attributes.beeHive
        return extraProps
    }
)
