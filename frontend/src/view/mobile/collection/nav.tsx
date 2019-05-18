import * as React from 'react';
import { RouteMenu } from '../../components/common/route-menu';

export function CollectionNav () {
    return <RouteMenu
        items={[
            {to:'/collection/book', label: '文章'},
            {to:'/collection/thread', label: '讨论'},
            {to:'/collection/list', label: '收藏单'},
        ]}
    />;
}