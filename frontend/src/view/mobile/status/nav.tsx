import * as React from 'react';
import { RouteMenu } from "../../components/common/route-menu";

export function StatusNav () {
  return <RouteMenu
    items={[
      {to:'/status/collection', label: '关注'},
      {to:'/status/all', label: '全站'},
    ]}
  />;
}