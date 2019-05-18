import * as React from 'react';
import { classnames } from '../../../utils/classname';

type TagColor = 'black'|'dark'|'light'|'white'|'primary'|'link'|'info'|'success'|'warning'|'danger'; 

export class Tag extends React.Component<{
  // props
  children?:React.ReactNode;
  className?:string;
  style?:React.CSSProperties;
  onClick?:(selected:boolean) => void;
  selected?:boolean;
  size?:'normal'|'medium'|'large';
  color?:TagColor;
  selectedColor?:TagColor;
  rounded?:boolean;
  selectable?:boolean;
}, {
  // states
  selected:boolean;
}> {
  public state = {
    selected: this.props.selected || false,
  }

  public render () {
    const selectedColor = this.props.selectedColor ? 'is-' + this.props.selectedColor : 'is-warning';
    const color = this.props.color ? 'is-' + this.props.color : '';
    const size = this.props.size ? 'is-' + this.props.size : '';

    return <a className={classnames(
        'tag',
        this.props.className,
        size,
        {'is-rounded': this.props.rounded},
        {[selectedColor]: this.props.selectable && this.state.selected},
        {[color]: this.props.selectable && !this.state.selected}
      )}
      style={this.props.style}
      onClick={() => {
        if (!this.props.selectable) { return; }
        this.setState((prevState) => {
          this.props.onClick && this.props.onClick(!prevState.selected);
          return {
            selected: !prevState.selected,
          };
        });
      }}>{this.props.children}</a>;
  }
}