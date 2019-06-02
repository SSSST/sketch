import * as React from 'react';
import './page.scss';
import { classnames } from '../../../utils/classname';

export function Page (props:{
  children:React.ReactNode;
  top?:React.ReactNode;
  bottom?:React.ReactNode;
  className?:string;
  style?:React.CSSProperties;
}) {
  return <div className={'page'}>
    { props.top &&
      <div className="top">
        {props.top}
      </div> 
    }

    <div className={classnames('body', props.className)} style={props.style}>
      {props.children}
    </div>

    { props.bottom &&
      <div className="bottom">
        {props.bottom}
      </div> 
    }

  </div>; 
}