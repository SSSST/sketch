import * as React from 'react';
import { Link } from 'react-router-dom';
import './route-menu.scss';
import { classnames } from '../../../utils/classname';

type MenuItem = {
  to:string;
  label:string;
  icon?:string;
  defaultColor?:string;
  selectedColor?:string;
}

interface Props {
  items:MenuItem[];
  style?:React.CSSProperties;
  onIndex?:number;
  onClick?:(item:MenuItem, index:number) => void;
  className?:string;
}
interface State {
}

export class RouteMenu extends React.Component<Props, State> {
  public render () {
    return <div className={classnames('route-menu', this.props.className)} style={this.props.style}>
      {this.props.items.map((item, i) => {
        const selected = i === this.props.onIndex;
        const selectedCln = selected ? 'selected' : '';
        return <div className={classnames('item', selectedCln)}
          key={item.to + item.label + selected}
          onClick={() => this.props.onClick && this.props.onClick(item, i)}>
          {item.icon && <div style={{
            color: selected ? (item.selectedColor || '') : (item.defaultColor || ''), 
          }}>
            <i className={item.icon}></i>
          </div>}
          <Link className={classnames(selectedCln)}
            key={i}
            to={item.to}
          >{item.label}</Link>
        </div>
      })}
    </div>;
  }
}