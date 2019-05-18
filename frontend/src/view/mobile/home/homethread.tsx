import * as React from 'react';
import { API } from '../../../config/api';
import { HomeMenu } from './home-menu';
import { MobileRouteProps } from '../router';
import { ThreadPreview } from '../../components/home/thread-preview';
import { Page } from '../../components/common/page';
import { Card } from '../../components/common/card';
import { List } from '../../components/common/list';

interface State {
  data:API.Get['/homethread'];
}

export class HomeThread extends React.Component<MobileRouteProps, State> {
  public state:State = {
    data: {},
  };

  public async componentDidMount () {
    const data = await this.props.core.db.getPageHomeThread();
    if (data) {
      this.setState({data});
    }
  }

  public render () {
    const { data } = this.state; 
    const channelIdx = Object.keys(data);
    return <Page top={<HomeMenu />}>
      {channelIdx.map((idx) =>
        <Card title={{
          text: data[idx].channel.attributes.channel_name,
          link: `/threads?channels=[${data[idx].channel.id}]`,
        }} key={idx}>
          {<List children={data[idx].threads.map((thread) => <ThreadPreview mini data={thread} key={thread.id} />)} />}
        </Card>)}
    </Page>;
  }
}