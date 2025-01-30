import {Component} from "react";

export default class Select extends Component {
    constructor(props) {
        super(props);
        this.props = props
    }

    render(){
        return <div className="select-component">
            <div className="selected-option">
                {this.props.placeholder  ? this.props.placeholder : "-"}
            </div>
            <ul>
                {this.props.options && this.props.options.length && this.props.options.map((option,i) => (
                    <li key={i} data-value={option.value}>{option.label}</li>
                ))}
            </ul>
            <input type="hidden" value={this.props.value}/>
        </div>
    }
}