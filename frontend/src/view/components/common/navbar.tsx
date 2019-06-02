import * as React from 'react';

interface Props {
  goBack:() => void;
  onMenuClick?:() => void;
  style?:React.CSSProperties;
}

interface State {
}

export class NavBar extends React.Component<Props, State> {
  public render () {
    return <div style={Object.assign({
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center',
      textAlign: 'center',
      minHeight: '2.25rem',
      backgroundColor: 'white',
      position: 'relative',
    }, this.props.style || {})}
      className="navbar"
      role="navigation"
      aria-label="main navigation">

      <div className="navbar-brand">
        <a className="navbar-item prev" onClick={this.goBack}>&#10094;</a> 
      </div>

      <div className="navbar-start" style={{
        display: 'flex',
        justifyContent: 'space-around',
        flex: 1,
      }}>
        {this.props.children}
      </div>
      
      <div className="navbar-end" style={{width: '32.14px'}}>
        {this.props.onMenuClick &&
            <div className="navbar-item" onClick={this.props.onMenuClick} style={{
              cursor: 'pointer',
            }}>
              <i className="fas fa-ellipsis-h"></i>
            </div>
        }
      </div>
    </div>;
  }

  public goBack = (ev:React.MouseEvent<HTMLAnchorElement>) => {
    this.props.goBack();
  }
}