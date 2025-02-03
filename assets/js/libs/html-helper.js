export default class HtmlHelper {
    static create(tagName="div", className="", attributes={}){
        const element = document.createElement(tagName)
        if(className) element.className = className
        for(let key in attributes) element[key] = attributes[key]
        return element
    }

    static fromHtml(html){
        return document.createRange().createContextualFragment(html).children[0]
    }
}