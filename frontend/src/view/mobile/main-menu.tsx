import * as React from 'react';
import { RouteMenu } from '../components/common/route-menu';

export function MainMenu () {
  const items = [
    {to:'/', label: 'home'},
    {to:'/status/all',label: 'status'},
    {to:'/collection/book', label: 'collection'},
    {to:'/user', label: 'user'},
  ];
  let onIndex = 0;
  for (let i = 0; i < items.length; i ++) {
    if (location.pathname === items[i].to) {
      onIndex = i;
    }
  }

  return <RouteMenu
    onIndex={onIndex}
    items={items}
  />
}