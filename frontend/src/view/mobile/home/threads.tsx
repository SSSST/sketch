import * as React from 'react';
import { HomeMenu } from './home-menu';
import { MobileRouteProps } from '../router';
import { API, ResData, ReqData } from '../../../config/api';
import { URLParser } from '../../../utils/url';
import { ThreadPreview } from '../../components/home/thread-preview';
import { UnregisterCallback } from 'history';
import { Page } from '../../components/common/page';
import { Pagination } from '../../components/common/pagination';
import { Card } from '../../components/common/card';
import { List } from '../../components/common/list';

interface State {
  data:API.Get['/thread'];
}

export class Threads extends React.Component<MobileRouteProps, State> {
  public state:State = {
    data: {
      threads: [],
      paginate: ResData.allocThreadPaginate(),
    },
  };

  public unlisten:UnregisterCallback|null = null;
  public componentDidMount () {
    this.loadData();
    this.props.core.history.listen(() => this.loadData())
  }

  public componentWillUnmount () {
    this.unlisten && this.unlisten();
  }

  public render () {
    const { data } = this.state;
    return <Page top={<HomeMenu />}>
      <Pagination currentPage={data.paginate.current_page} lastPage={data.paginate.total_pages} />
      <Card>
        <List children={data.threads.map((thread) =>
          <ThreadPreview data={thread} key={thread.id} />)} />
      </Card>
    </Page>;
  }

  public loadData () {
    (async () => {
      const url = new URLParser();
      if (url.getAllPath()[0] !== this.props.path) { return; }

      const data = await this.props.core.db.getThreadList({
        page: url.getQuery('page'),
        tags: url.getQuery('tags'),
        channels: url.getQuery('channels'),
        withType: ReqData.Thread.withType.thread,
        ordered: url.getQuery('ordered'),
      });
      if (data) {
        this.setState({data});
      }
    })();
  }
}