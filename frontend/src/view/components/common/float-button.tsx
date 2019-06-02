import * as React from 'react';
import './float-button.scss';
import { classnames } from '../../../utils/classname';

interface Props {
  style?:React.CSSProperties;
  className?:string;
  onClick?:() => void;
}

function Plus (props:Props) {
  return <div
    className={classnames('float-button-plus', props.className)}
    onClick={props.onClick}
    style={props.style}>
    <i className="fas fa-plus fa-lg"></i>
  </div>;
}

interface PageProps extends Props {
  currentPage:number;
  totalPage:number;
}
function Page (props:PageProps) {
  return <div
    className="float-button-page"
    style={props.style}>
    <div
      className="page"
      onClick={props.onClick}>{props.currentPage}/{props.totalPage}页</div>
    <div>|</div>
    <i className="fas fa-long-arrow-alt-up" onClick={() => {
      window.scrollTo(0, 0);
    }}></i>
  </div>;
}

export class FloatButton extends React.Component <Props, {}> {
  public static Plus = Plus;
  public static Page = Page;

  public render () {
    return <div
      onClick={this.props.onClick}
      className={classnames('float-button-wrapper', this.props.className)}
      style={this.props.style}>
      {this.props.children}
    </div>;
  }
}