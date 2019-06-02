import * as React from 'react';

interface Props {
  length:number;
  mark?:number;
  onClick?:(mark:number) => void;
}
interface State {
  currentValue:number, 
  value:number,
}

export class Mark extends React.Component<Props, State> {

  constructor(props:Props) {
    super(props);

    this.state = {
      currentValue: -1,
      value: -1,
    }
  }

  private selectValue(value:number) {
    const {onClick, mark} = this.props;
    if(mark) {
      return;
    }

    this.setState({
      currentValue: value,
      value,
    }, () => {
      onClick && onClick(value + 1)
    });
  }

  private setCurrentValue(value:number) {

    const {mark} = this.props;
    if(mark) {
      return;
    }

    this.setState({
      currentValue: value
    });
  }

  private resetCurrentValue() {

    const {mark} = this.props;
    if(mark) {
      return;
    }

    this.setState((prevState) => ({
      currentValue: prevState.value
    }));
    
  }

  public render () {
    const {length, mark} = this.props;
    const { currentValue } = this.state;

    return (
      <div>
        {(new Array(length)).fill("").map((v, k) => (
          <span 
            style={{cursor: mark ? "auto" : "pointer"}}
            onMouseMove={(e) => this.setCurrentValue(k)}
            onMouseLeave={() => this.resetCurrentValue()}
            onClick={() => this.selectValue(k)}
            key={k}
          > 
            <i className={
                k <= (mark === undefined ? currentValue : mark - 1)
                  ? "fas fa-star"
                  : "far fa-star"
            }>
            </i> 
          </span>
        ))}
      </div>
    );
  }
}