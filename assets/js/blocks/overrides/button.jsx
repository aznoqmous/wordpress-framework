const __ = wp.i18n.__
const namespace = 'dvs/button-attributes'
const targetBlock = "core/button"

wp.hooks.addFilter('blocks.getSaveElement', namespace, (element, blockType, attributes)=>{
    if(!element) return;
    if(blockType.name != targetBlock) return element;
    if(!element.props.children) return element;
    return <button>{element.props.children.props.value}</button>;
})