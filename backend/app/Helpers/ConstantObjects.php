<?php

namespace App\Helpers;
use App\Models\Tag;
use App\Models\Title;
use Cache;
use DB;

class ConstantObjects
{
    protected static $channel_types = array('book', 'thread', 'request', 'homework', 'list'); // channel的分类类别

    public static function allChannels()//获得站上所有的channel
    {
        return collect(config('channel'));//将channels转化成collection

    }

    public static function publicChannelTypes($type='')
    {
        if (in_array($type, self::$channel_types)){
            return self::allChannels()->where('type', $type)->where('is_public', true)->pluck('id')->toArray();
        }
        return [];
    }

    public static function channelTypes($type='')
    {
        if (in_array($type, self::$channel_types)){
            return self::allChannels()->where('type', $type)->pluck('id')->toArray();
        }
        return [];
    }

    public static function public_channels()
    {
        return self::allChannels()->where('is_public', true)->pluck('id')->toArray();
    }

    public static function allTags()//获得站上所有的tags
    {
        return Cache::remember('allTags', 10, function (){
            return Tag::all();
        });
    }

    public static function noTongrenTags()//获得站上非同人的所有的tags
    {
        return Cache::remember('noTongrenTags', 10, function (){
            return Tag::whereNotIn('tag_type', ['同人原著', '同人CP'])->get();
        });
    }

    public static function titles()//获得站上所有的titles
    {
        return Cache::remember('titles', 10, function (){
            return Title::all();
        });
    }

    public static function system_variable()//获得当前系统数据
    {
        return Cache::remember('system_variable', 10, function () {
            return DB::table('system_variables')->get();
        });
    }

    public static function firewall_ips()//获得当前被屏蔽的ip地址列表
    {
        return Cache::remember('firewall_ips', 10, function () {
            return DB::table('firewall')->where('is_valid',true)->pluck('ip_address')->toArray();
        });

    }

    public static function role_users()//目前系统所保存的所有现在在使用中的特殊的用户身份列表，如是否admin，editor，no-logging，no-posting, channel_admin, tag_admin
    {
        return Cache::remember('role_users', config('constants.cache_time'), function () {
            return DB::table('role_user')->where('is_valid', true)->select(['user_id','role', 'options'])->get();
        });
    }

}
