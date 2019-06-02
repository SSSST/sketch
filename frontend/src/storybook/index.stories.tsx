import '@fortawesome/fontawesome-free-webfonts/css/fa-brands.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-regular.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-solid.css';
import '@fortawesome/fontawesome-free-webfonts/css/fontawesome.css';
import { action } from '@storybook/addon-actions';
import { withConsole } from '@storybook/addon-console';
import { boolean, number, select, text, withKnobs } from '@storybook/addon-knobs';
import { withViewport } from '@storybook/addon-viewport';
import { addDecorator, storiesOf } from '@storybook/react';
import { createBrowserHistory } from 'history';
import React from 'react';
import { Router } from 'react-router';
import '../theme.scss';
import { Accordion } from '../view/components/common/accordion';
import { Animate } from '../view/components/common/animate';
import { Badge } from '../view/components/common/badge';
import { Card } from '../view/components/common/card';
import { Center } from '../view/components/common/center';
import { Dropdown } from '../view/components/common/dropdown';
import { FilterBar } from '../view/components/common/filter-bar';
import { List } from '../view/components/common/list';
import { Mark } from '../view/components/common/mark';
import { NavBar } from '../view/components/common/navbar';
import { Popup } from '../view/components/common/popup';
import { PopupMenu } from '../view/components/common/popup-menu';
import { RouteMenu } from '../view/components/common/route-menu';
import { Slider } from '../view/components/common/slider';
import { Tab } from '../view/components/common/tab';
import { Tag } from '../view/components/common/tag';
import { TagList } from '../view/components/common/tag-list';
import { FloatButton } from '../view/components/common/float-button';
import { Core } from '../core/index';
import { Carousel } from '../view/components/common/carousel';
import { NoticeBar } from '../view/components/common/notice-bar';
import { Loading } from '../view/components/common/loading';

const core = new Core();

addDecorator((storyFn, context) => withConsole()(storyFn)(context));
addDecorator(withViewport());
addDecorator(withKnobs);

storiesOf('Common Components', module)
  .add('Badge', () => <Badge num={number('num', 10)}
      max={number('max', 0)}
      dot={boolean('dot', false)}
      hidden={boolean('hidden', false)}>
      {text('text', 'test')}
    </Badge>
  )
  .add('Tag', () => {
    const colorOptions = {
      default: '',
      black: 'black',
      dark: 'dark',
      light: 'light',
      white: 'white',
      primary: 'primary',
      link: 'link',
      info: 'info',
      success: 'success',
      warning: 'warning',
      danger: 'danger',
    };
    return <Tag
      selected={boolean('selected', false)}
      onClick={action('tag click')}
      size={select('size', {
        normal: 'normal',
        medium: 'medium',
        large: 'large',
      }, 'normal')}
      color={select('color', colorOptions, '')}
      selectedColor={select('selectedColor', colorOptions)}
      rounded={boolean('rounded', false)}
      selectable={boolean('selectable', true)}
    >{text('text', 'test')}</Tag>;
  })
  .add('TagList', () => {
    const colorOptions = {
      default: '',
      black: 'black',
      dark: 'dark',
      light: 'light',
      white: 'white',
      primary: 'primary',
      link: 'link',
      info: 'info',
      success: 'success',
      warning: 'warning',
      danger: 'danger',
    };
    return <TagList>
      {(new Array(number('length', 20)).fill(text('text', 'tag')).map((text, i) => <Tag
        key={i} 
        onClick={action('tag click ' + i)}
        size={select('size', {
          normal: 'normal',
          medium: 'medium',
          large: 'large',
        }, 'normal')}
        color={select('color', colorOptions, '')}
        selectedColor={select('selectedColor', colorOptions)}
        rounded={boolean('rounded', false)}
        selectable={boolean('selectable', true)}
      >{text}</Tag>))}
    </TagList>
  })

  .add('FilterBar', () => {
    return <FilterBar></FilterBar>
  })
  .add('Popup', () => React.createElement(class extends React.Component {
    public state = {
      showPopup: true,
    };
    public render () {
      return <div>
        <button className="button" onClick={() => this.setState({showPopup: true})}>show popup</button>
        {this.state.showPopup &&
          <Popup
            width={text('width', '')}
            bottom={text('bottom', '')}
            darkBackground={number('darkBackground', 0.8)}
            onClose={() => this.setState({showPopup: false})}>
            {text('content', 'example content')}
          </Popup>
        }
      </div>;
    }
  }))
  .add('Center', () => <Center width={text('width', '')} height={text('height','')}>
    <div>center anything</div>
  </Center>)
  .add('List', () => <List>
    {['a', 'b', 'c'].map((item, i) => <List.Item
      key={i}
      onClick={() => alert('click item ' + item)}
      arrow={boolean('arrow', false)}>
      {item}
    </List.Item>)}
  </List>)
  .add('Accordion', () => <Accordion
    title={text('title', 'accordion title')}
    arrow={boolean('arrow', true)}
  >
    <List>
      <List.Item>1</List.Item>
      <List.Item>2</List.Item>
    </List>
  </Accordion>)
  .add('Menu', () => React.createElement(class extends React.Component {
    public state = {
      onIndex: 0,
      icon: boolean('icon', false),
    };
    public render () {
      const items = [
        {to: '', label: 'menu1'},
        {to: '', label: 'menu2'},
        {to: '', label: 'menu3'}
      ];
      if (this.state.icon) {
        for (let i = 0; i < items.length; i ++) {
          const item = items[i];
          item['icon'] = 'fas fa-star';
          item['selectedColor'] = 'red';
          item['defaultColor'] = 'black';
        }
      }
      return <Router history={createBrowserHistory()}>
          <RouteMenu
            items={items}
            onIndex={this.state.onIndex}
            onClick={(_, i) => this.setState({onIndex: i})}
          ></RouteMenu>
      </Router>;
    }
  }))
  .add('Mark', () => <Mark length={number('length', 5)} 
      mark={boolean('disabled', false) ? 4 : undefined}
      onClick={action('onClick')} />
  )
  .add('Slider', () => 
    <Slider>
      {[1, 2, 3, 4, 5, 6, 7].map((i) => 
        <Slider.Item key={i}>
          <Card style={{
            width: '70px',
            height: '70px',
            border: '1px solid grey',
            padding: '1px',
            marginTop: '0',
          }}>
            <Center>
              card {i}
            </Center>
          </Card>
        </Slider.Item>
      )}
    </Slider>
  )
  .add('tab', () => {
    const tabContent = [1, 2, 3, 4, 5].map((tab) => {
      return {
        name: 'tab' + tab,
        children: <List noBorder>
          {[1, 2, 3, 4].map((item) =>
            <List.Item key={item}>tab {tab} list-item {item}</List.Item>
          )}
        </List>
      }
    });
    return <Tab
      tabs={tabContent}
      onClickTab={action('onClickTab')}
    />;
  })
  .add('popup menu', () => React.createElement(class extends React.Component<{}, {showPopup: boolean}> {
    public state = {
      showPopup: true,
    };
    public render () {
      return <div>
        <div className="button"
          onClick={() => this.setState((prevState) => ({showPopup: !prevState.showPopup}))}>
          show popup
        </div>
        {this.state.showPopup && <PopupMenu
          list={[
            { title: 'one', onClick: action('clickOne')},
            { title: 'two', onClick: action('clickTwo')},
          ]}
          onClose={() => this.setState({showPopup: false})}
        />}
    </div>; 
    }
  }))
  .add('animation', () => <Animate
    name={select('name', {
      slideInUp: 'slideInUp',    
      slideOutUp: 'slideOutUp',
      slideInDown: 'slideInDown',
      slideOutDown: 'slideOutDown',
      slideInRight: 'slideInRight',
      slideOutRight: 'slideOutRight',
      slideInLeft: 'slideInLeft',
      slideOutLeft: 'slideOutLeft',
    })}
    speed={select('speed', {
      slow: 'slow',
      slower: 'slower',
      fast: 'fast',
      faster: 'faster',
    })}
    infinite={boolean('infinite', false)}
    ><div>example animation</div></Animate>)
  .add('carousel', () => <Carousel
    windowResizeEvent={core.windowResizeEvent}
    slides={[
      <div> sample slide 1 </div>,
      <div> sample slide 2 </div>,
      <div> sample slide 3 </div>,
    ]}
    getIndex={action('getIndex')}
    indicator={boolean('indicator', true)}
    startIndex={number('startIndex', 0)}
  />)
  .add('Loading', () =>
    <Loading>
      <div style={{
        width: '200px',
        height: '200px',
        border: '1px solid black',
        lineHeight: '200px',
        textAlign: 'center',
      }}>
        <p> 加载中请稍后</p>
      </div>
    </Loading>,
  )
;

storiesOf('Common Components/Notice Bar', module)
  .add('short message', () => <NoticeBar
    icon={text('icon', 'fas fa-bullhorn')}
    closable={boolean('closable', true)}
    onClick={action('onClick')}
    customizeLink={(() => {
      if (boolean('customizeLink', false)) {
        return <div>go</div>
      }
      return undefined;
    })()}
    >example notice message
  </NoticeBar>)
  .add('long message', () => <NoticeBar
    icon={text('icon', 'fas fa-bullhorn')}
    closable={boolean('closable', true)} 
    onClick={action('onClick')}
  >
    example super super super super super super super super super super super super super super super super super super long notice message
  </NoticeBar>)
;

storiesOf('Common Components/Dropdown', module)
  .add('Dropwdown', () => <Dropdown
    list={[{text: '1', value: 1}, {text: '2', value: 2}]}
    onIndex={0}
    onClick={action('onClick')}
    />)
  .add('Dropdown(with title)', () => {
    return <Dropdown
      list={[{text: '1', value: 1}, {text: '2', value: 2}]}
      title={text('title', 'dropdown menu')}
      onClick={action('onClick')}
    />;
  })
;

storiesOf('Common Components/Navigation Bar', module)
  .add('simple', () => <NavBar goBack={action('goBack')} >
    {text('title', 'example title')}
  </NavBar>)
  .add('buttons', () => <NavBar goBack={action('goBack')}>
    <div className="button">阅读模式</div>
    <div className="button">论坛模式</div>
  </NavBar>)
  .add('with menu', () => React.createElement(class extends React.Component {
    public state = {
      showPopup: false,
    };
    public render () {
      return <NavBar goBack={action('goBack')}
      onMenuClick={() => this.setState({showPopup: true})}>
      {text('title', 'example title')}
      {this.state.showPopup &&
        <PopupMenu
          list={[
            {title: 'menu1', onClick: action('click menu1')},
            {title: 'menu2', onClick: action('click menu2')},
          ]}
          onClose={() => this.setState({showPopup: false})}
        />
      }
    </NavBar>;
    }
  }))
;

storiesOf('Common Components/Float Button', module)
  .add('plus', () => <FloatButton.Plus
    onClick={action('onClick')}
  />)
  .add('page', () => <div style={{ height: '1000px', position: 'relative', overflowY: 'auto' }}><FloatButton.Page
    currentPage={number('currentPage', 1)}
    totalPage={number('totalPage', 3)}
    onClick={action('onClick')}
  /></div>)
  .add('customize', () => <FloatButton>
    <div className="button">customize</div>
  </FloatButton>)
;

storiesOf('Home Components', module)
;

storiesOf('User Components', module)
;

storiesOf('Thread Components', module)
;