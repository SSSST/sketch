import * as React from 'react';
import { MobileRouteProps } from '../router';
import { CollectionNav } from './nav';
import { ResData, ReqData, API } from '../../../config/api';
import { ThreadPreview } from '../../components/home/thread-preview';
import { Page } from '../../components/common/page';
import { Pagination } from '../../components/common/pagination';
import { Card } from '../../components/common/card';
import { List } from '../../components/common/list';

interface State {
    data:API.Get['/collection'];
}

export class CollectionThread extends React.Component<MobileRouteProps, State> {
    public state:State = {
        data: {
            threads: [],
            paginate: ResData.allocThreadPaginate(),
        },
    };

    public async componentDidMount () {
        const data = await this.props.core.db.getCollection(ReqData.Collection.type.thread);
        if (data) {
            this.setState({data});
        }
    }

    public render () {
        const { data } = this.state;
        return (<Page top={<CollectionNav />}>
            <Pagination currentPage={data.paginate.current_page} lastPage={data.paginate.total_pages} />
            <Card>
                <List children={data.threads.map((thread) => <ThreadPreview data={thread} key={thread.id} />)} />
            </Card>
        </Page>);
    }
}