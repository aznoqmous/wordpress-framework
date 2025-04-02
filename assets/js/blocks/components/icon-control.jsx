import {Component} from "react";

export default class IconControl extends Component {
    constructor(props) {
        super(props)
        this.state = {
            icons: [],
            selectedIcon: props.selectedIcon
        }
    }

    async getIcons(){
        return fetch(`/wp-json/api/sprite/${this.props.name}/icons`)
    }

    update(){
        this.setState({selectedIcon: this.selectedIcon})
        if(this.props.onUpdate) this.props.onUpdate(this.selectedIcon)
    }

    componentDidMount(){
        this.getIcons()
            .then(res => res.json())
            .then(icons => this.setState({icons}))
    }

    render(){
        return <div className="icon-control">
            <ul className="icons">
                {this.state.icons && this.state.icons.map((icon, i) =>
                    <li key={i} className={icon == this.state.selectedIcon ? "active": ""} onClick={()=> {
                        this.selectedIcon = this.selectedIcon != icon ? icon : null;
                        this.update()
                    }}>
                        <svg width="32" height="32" data-icon={icon}>
                            <use xlinkHref={"/icons/" + this.props.name + ".svg#" + icon}></use>
                        </svg>
                    </li>
                )}
            </ul>
        </div>
    }
}