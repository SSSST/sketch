import * as React from 'react';
import { storiesOf, addDecorator } from '@storybook/react';
import { withViewport } from '@storybook/addon-viewport';
import { withConsole } from '@storybook/addon-console';
import { withKnobs, text, boolean, number, select } from '@storybook/addon-knobs';
import { Badge } from '../src/view/components/common/badge';
import { Mark } from '../src/view/components/common/mark';
import { action } from '@storybook/addon-actions';
import { Tag } from '../src/view/components/common/tag';
import { TagList } from '../src/view/components/common/tag-list';
import { FilterBar } from '../src/view/components/common/filter-bar';
import { Dropdown } from '../src/view/components/common/dropdown';
import { Popup } from '../src/view/components/common/popup';
import { Center } from '../src/view/components/common/center';
import { List } from '../src/view/components/common/list';
import { Accordion } from '../src/view/components/common/accordion';
import { RouteMenu } from '../src/view/components/common/route-menu';
import { Slider } from '../src/view/components/common/slider';
import { Card } from '../src/view/components/common/card';
import { Tab } from '../src/view/components/common/tab';

import '@fortawesome/fontawesome-free-webfonts/css/fontawesome.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-regular.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-solid.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-brands.css';
import { Router } from 'react-router';
import { createBrowserHistory } from 'history';

import '../src/theme.scss';

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
  .add('FilterBar', () => {
    return <FilterBar></FilterBar>
  })
  .add('Popup', () => React.createElement(class extends React.Component {
    public state = {
      showPopup: true,
      darkerBackgrond: boolean('darkerBackground', true),
      content: text('content', 'test'),
      bottom: boolean('bottom', false),
    };
    public render () {
      return <div>
        <button className="button" onClick={() => this.setState({showPopup: true})}>show popup</button>
        {this.state.showPopup &&
          <Popup
            bottom={this.state.bottom}
            darkerBackground={this.state.darkerBackgrond}
            onClose={() => this.setState({showPopup: false})}>
            <div>{this.state.content}</div>
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
  .add('Mark', () => 
    <Mark length={5} onClick={(val) => alert(val)} />
  )
  .add('Mark disabled', () => 
    <Mark length={5} mark={4} />
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
  });
;

storiesOf('Home Components', module)
.addDecorator(withViewport())
.addDecorator(withKnobs)
;

storiesOf('User Components', module)
.addDecorator(withViewport())
.addDecorator(withKnobs)
;

storiesOf('Thread Components', module)
.addDecorator(withViewport())
.addDecorator(withKnobs)
;