import * as React from 'react';
import './popup.scss';
import { classnames } from '../../../utils/classname';

export function Popup (props:{
  children:React.ReactNode;
  onClose:() => void;
  bottom?:boolean; // default is center
  width?:string;
  minHeight?:string;
  style?:React.CSSProperties;
  className?:string;
  darkerBackground?:boolean;
}) {
  return <div className="modal is-active" style={{
    justifyContent: props.bottom && 'flex-end' || 'center',
  }}>
    <div className={'modal-background'}
      onClick={() => props.onClose()}
      style={{
        background: props.darkerBackground ? undefined : 'none',
    }}></div>
    <div className={classnames('modal-content', props.className)}
      style={Object.assign({
        width: props.width || '80%',
        minHeight: props.minHeight || '30%',
        boxShadow: !props.darkerBackground && '0px 0px 6px 0px #d1d1d1',
      }, props.style || {})}>
        {props.children}
    </div>
    {props.darkerBackground && <button
        className="modal-close is-large"
        onClick={() => props.onClose()}></button>}
  </div>;
}