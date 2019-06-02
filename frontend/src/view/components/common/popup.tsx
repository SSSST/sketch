import * as React from 'react';
import './popup.scss';
import { classnames } from '../../../utils/classname';

export class Popup extends React.Component <{
  // props
  onClose:() => void;
  bottom?:string; // default is center
  width?:string;
  minHeight?:string;
  style?:React.CSSProperties;
  className?:string;
  darkBackground?:number;
  customizeContent?:boolean;
}, {
  // state
}> {
  public render () {
    const { props } = this;
    return <div className="modal is-active" style={{
      justifyContent: props.bottom && undefined || 'center',
    }}>
      {this.props.darkBackground &&
        <div className={'modal-background'}
          onClick={() => props.onClose()}
          style={{
            background: `rgba(10, 10, 10, ${this.props.darkBackground})`
        }}></div>
      }

      {this.renderContent()}

      {props.darkBackground && <button
          className="modal-close is-large"
          onClick={() => props.onClose()}></button>}
    </div>;
  }

  public renderContent () : undefined|JSX.Element|React.ReactNode {
    const { props } = this;
    if (!this.props.customizeContent) {
      return <div className={classnames('modal-content', props.className)}
        style={Object.assign({
          width: props.width || '80%',
          minHeight: props.minHeight || '30%',
          boxShadow: !props.darkBackground && '0px 0px 6px 0px #d1d1d1',
          margin: 0,
          position: props.bottom ? 'fixed' : undefined,
          bottom: props.bottom || undefined,
        }, props.style || {})}>
          {props.children}
      </div>;
    }
    return props.children;
  }
}