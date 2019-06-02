import * as React from 'react';
import { Link } from 'react-router-dom';
import { Carousel } from '../common/carousel';
import { ResData, } from '../../../config/api';
import { Core } from '../../../core/index';

interface Props {
  quotes:ResData.Quote[];
  core:Core;
}

interface State {
  index:number;
}

export class Quotes extends React.Component<Props, State> {
  public state = {
    index: 0,
  };

  public render () {
    return <div>
      <Carousel
        windowResizeEvent={this.props.core.windowResizeEvent}
        slides={this.props.quotes.map((quote, i) =>
          <div key={i}>
            <div>{quote.attributes.body}</div>
            <div>——{quote.attributes.is_anonymous ? quote.attributes.majia : quote.author.attributes.name}</div>
          </div>,
        )}
        getIndex={(index) => {this.setState({index : index})}}
        indicator={true}
        startIndex={this.state.index}
      />

      <div style={{margin:'10px 0 0 0'}}>
        {this.props.core.user.isLoggedIn() &&
          <Link to="/createquote" className="button">贡献题头</Link>
        }
        <a className="button is-pulled-right" /*onClick={()=>} TODO: 投掷咸鱼*/>
          咸鱼 {this.props.quotes[this.state.index] ? this.props.quotes[this.state.index].attributes.xianyu : 0}
        </a>
      </div>
    </div>;
  }
}