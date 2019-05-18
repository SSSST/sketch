import * as React from 'react';
import { ResData } from '../../../config/api';
import { indexEq } from '../../../utils/id';
import { URLParser } from '../../../utils/url';
import { Link } from 'react-router-dom';
import { Card } from './card';
import { Tag } from './tag';
import { TagList } from './tag-list';

interface Props {
  tags:ResData.Tag[];
  getFullList:() => void;
  search:(path:string, tags:number[]) => void;
  redirectPathname?:string;
}

interface State {
}

export class TagFilter extends React.Component<Props, State> {
  public selectedTags:ResData.Tag[] = [];
  public selectedTagIds:number[] = [];
  public filterTags:ResData.Tag[] = [];

  public showFullList = false;
  public firstLoad = true;

  public clickTag = (tag:ResData.Tag) => {
    const idx = this.selectedTagIds.indexOf(tag.id);
    if (idx < 0) {
      this.selectedTagIds.push(tag.id);
      this.selectedTags.push(tag);
    }  else {
      this.selectedTagIds.splice(idx, 1);
      this.selectedTags.splice(indexEq(this.selectedTags, tag.id), 1);
    }
  }

  public render () {
    if (this.firstLoad && this.props.tags.length > 0) {
      const url = new URLParser();
      const tags = url.getQuery('tags');
      if (tags) {
        this.selectedTagIds = tags;
        this.filterTags.length = 0;
        this.selectedTags.length = 0;
        for (let i = 0; i < this.props.tags.length; i ++) {
          const tag = this.props.tags[i];
          if (this.selectedTagIds.indexOf(tag.id) >= 0) {
            this.filterTags.push(tag);
            this.selectedTags.push(tag);
          }
        }
        this.firstLoad = false
      }
    }

    let renderTagList:() => JSX.Element;
    if (this.showFullList) {
      renderTagList = this.renderFullTags;
    } else {
      if (this.filterTags.length !== 0) {
        renderTagList = this.renderFilterTags;
      } else {
        renderTagList = this.renderInitTags;
      }
    }
    
    return <Card className="book-tags">
      {renderTagList()}
    </Card>;
  }

  public renderFilterTags = () => {
    return <div className="short_list">
      {this.renderChannels()}
      <div className="buttons">
        <span>筛选标签:</span>
        {this.filterTags.map((tag) => {
          const idx = this.selectedTagIds.indexOf(tag.id);
          return <Tag
            selected={idx >= 0}
            key={tag.id}
            onClick={() => {
              this.clickTag(tag);
              this.props.search(this.getTagsPathname(), this.selectedTagIds);
            }}
            selectable
        >{tag.attributes.tag_name}</Tag>;})}
        <Tag onClick={() => {
          this.showFullList = true;
          this.props.getFullList();
        }}>更多</Tag>
      </div>
    </div>
  }

  public renderInitTags = () => {
    const { tags } = this.props;
    return <div className="short_list">
      {this.renderChannels()}
      <TagList>
        {tags.map((tag) => 
          <Tag
            onClick={() => {
              this.clickTag(tag);
              this.props.search(this.getTagsPathname() ,this.selectedTagIds);
            }}
            selectable
            key={tag.id}>{tag.attributes.tag_name}</Tag>)}
        <a className="tag" onClick={() => {
          this.showFullList = true;
          this.props.getFullList();
        }}>更多</a>
      </TagList>
    </div>;
  }

  public renderChannels = () => {
    const url = new URLParser();
    const channels = url.getQuery('channels');
    return <div className="field has-addons" style={{ width: '100%' }}>
      <p className="control" style={{ flex: 1 }}>
        <Channel id={1}
          text="原创"
          isSelected={ channels ? (channels.indexOf(1) < 0 ? false : true) : false } />
      </p>
      <p className="control" style={{ flex: 1 }}>
      <Channel id={2}
          text="同人"
          isSelected={ channels ? (channels.indexOf(2) < 0 ? false : true) : false } />
      </p>
    </div>;
  }

  public renderFullTags = () => {
    const { tags } = this.props;
    const tagTypes:{[type:string]:ResData.Tag[]} = {};

    for (let i = 0; i < tags.length; i ++) {
      const tag = tags[i];
      const type = tag.attributes.tag_type;
      if (type === '大类') { continue; }
      if (tagTypes[type]) {
        tagTypes[type].push(tag);
      } else {
        tagTypes[type] = [tag];
      }
    }

    return <div className="full_list">
      {Object.keys(tagTypes).map((type, idx) =>
        <div className="li" key={idx}>
          <TagList>
            <span>{type}</span>
            {tagTypes[type].map((tag) => {
              const idx = this.selectedTagIds.indexOf(tag.id)
              return <Tag
                onClick={() => {
                  this.clickTag(tag);
                }}
                selectable
                selected={idx >= 0}
                key={tag.id}>{tag.attributes.tag_name}</Tag>; 
            })}
          </TagList>
        </div>     
      )}
      <div className="li">
        <a className="button is-fullwidth" onClick={() => {}}>点击加载同人标签</a>
      </div>
      <div className="li">
        <a className="button is-fullwidth" onClick={() => {
          this.showFullList = false;
          this.filterTags = this.selectedTags.slice();
          this.props.search(this.getTagsPathname(), this.selectedTagIds);
        }}>筛选</a>
      </div>
    </div>;
  }

  public getTagsPathname () {
    const url = new URLParser();
    if (this.props.redirectPathname) {
      url.pathname = this.props.redirectPathname;
    }
    if (this.selectedTagIds.length === 0) {
      return url.removeQuery('tags').getPathname();
    } else {
      return url.setQuery('tags', this.selectedTagIds).getPathname();
    }
  }
}

class Channel extends React.Component<{
  id:number;
  text:string;
  isSelected?:boolean;
}, {}> {
  public render () {
    return <Link
      className={'button is-fullwidth' + (this.props.isSelected && ' is-primary' || '')}
      to={(() => {
        const url = new URLParser();
        if (this.props.isSelected) {
          url.removeArrayQuery('channels', [this.props.id]);
        } else {
          url.setArrayQuery('channels', [this.props.id]);
        }
        return url.getPathname();
      })()}
    >{this.props.text}</Link>
  }
}