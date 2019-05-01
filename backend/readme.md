# Laravel-sosad 后端运行指南
## 1.序言
本文档是为用户理解和使用backend文件夹中的所有内容所准备的。backend的主要任务，是提供一套方便使用的api。本文档内容包含安装配置，数据库表格设计的解释，以及api各个指令的属性、所需数据的格式、返回结果的格式。
### 特别注意事项
前后端分离之后，所有laravel相关的指令，比如`php artisan migrate`，或者composer相关指令，比如`composer update`，都应进入backend文件夹之后操作，否则会报错。本文档中所有“根目录”，如未特别说明，指的都是居于backend文件夹内部的这个地址。

## 2.安装配置
### 2.1 composer更新组件
第一次使用时，运行
```
$ composer update
```
补完需要加载的框架和package文件。
### 2.2 书写和加载`.env`文件中数据库配置

laravel后端中所有环境变量（基本配置比如数据库地址、用户名、密码，和大部分不应被git上传的安全相关的文件比如加密所需的key）都存放在居于最外面的`.env`文件。laravel已经准备了范例，只需在范例基础上稍作加工，就可以得到自己的这个文件。将backend根目录下`.env.example`改写为`.env`，补完其中关于database信息的内容。  
其中环境变量`DB_DATABASE`应指向空白的本地mysql数据库，需要用户自己安装mysql，并创建一个新的数据库，将它的地址和用户名、密码填到这个地方。mac系统推荐使用sequel pro程序浏览自己本地mysql数据库的情形。
### 2.3 数据库migration，使用seeder填充mock数据
接下来，运行数据库migration，并通过预先写好的seeder，给数据库填充用于测试的mock信息   
```
$ php artisan migrate --seed
```
### 2.4 配置passport
#### 2.4.1 配置APP_KEY
如果是第一次使用laravel， `.env` 文件中不含APP_KEY这个变量，那么还需要让程序加载初始key。一些情况下，也可以使用以前曾经使用过的key，来确保数据库之间能够对应。只需运行下面这两个指令即可：
```
$ php artisan key:generate
$ php artisan passport:keys
```  
如果之前已经配置了key，程序会提示你，是否想要重置key，按自己需求选择即可。

#### 2.4.2 创建passport client
在这个工程中，我们使用laravel自带的passport这个package，给api进行基本的授权。为了在本地顺利测试相关情况，我们需要对passport进行基本配置，比如说，以Personal access client的名义，给自己的前端部分授权。
```
$ php artisan passport:install
```

这一步按照程序提示，输入任意字符串即可。
### 2.5 使用valet，或直接serve程序，让服务器“运行”起来。
#### 2.5.1 使用valet（mac用户，推荐使用此项）
valet是一个非常便利轻量开发的工具，它可以让编程和测试更方便地衔接起来。使用mac的用户，花费一点点时间配置它，事半功倍。  
使用valet的用户，只要进行到`backend`文件夹根目录，运行
```
$ valet park
```
以后就可以通过`backend.test`这个网址对本工程进行访问。比如，访问`backend.test/api/register`，进行新用户的注册。

#### 2.5.2 使用laravel自带的serve指令，模拟服务器服务
不愿使用valet的用户，可以运行指令  
```
$ php artisan serve
```
然后使用terminal中弹出的地址访问本工程即可。一般是`http://127.0.0.1:8000/`。也就是说，以后可以使用`http://127.0.0.1:8000/api/register`，访问注册页面

以下默认使用`http://127.0.0.1:8000/`作为访问路径。


### 2.6 在成功初次使用之后，需要重新pull代码，更新数据库
#### 2.6.1 使用场景
有的时候我们会面对这样的情况：
1. 有一段时间没工作了想重新开始
2. 刚完成了一部分工作，想在提交前确保和远端兼容
3. 想要进行下一个任务，
4. 其他人刚往远端push了新的代码，本地希望和远程保持同步  

这些时候我们需要重新加载来自远程主branch（`backend-reconstruct`）的更新，并且将数据库对应的变化同步更新。

#### 2.6.2 注意事项
- 请确保自己在backend文件夹内！否则会遇到artisan command不存在这一类的报错
- 如果还没配置好env，没有运行过后端，请先按照后端readme安装教程的介绍，将各部分先配置好，不能找搬本教程

#### 2.6.3 方法
依次运行以下指令即可 ：

```
$ git pull
$ composer update
$ php artisan migrate:reset
$ php artisan passport:install
$ php artisan db:seed
$ vendor/bin/phpunit
```
以上步骤的意义依次为：  
1. 从github下载最新更新
2. 用composer加载最新的安装包（重要！有的时候旧包会有一些bug）
3. 将现有数据库所有migration清空
4. 重新安装passport clients（因为数据库清空了，因此需要重新建立sample clients）
5. 将现有数据库用mock数据填充
6. 运行phpunit的测试组件，确定一切正常。

#### 2.6.4 结果
正常的话应该会看到绿色的测试通过公示。一切ok的话，说明这边没有大问题了

## 3. 数据库结构解释


### 3.1 数据库简单介绍

#### 3.1.1 废文网数据库的基本情况
关于数据库的ER图，请参考`backend`下文件`ER_of_sosad.png`。这个图还会根据具体情况作出调整。

#### 3.1.2 数据库最重要的三个表格：users，threads，posts
**站内最主要的表格是三个：_users，threads，posts。_**  
**users表**
存放一个用户的最基本数据，比如：用户名、注册日期、是否允许陌生人私信、最后使用的马甲……  
**threads表**
存放一本书或者一个讨论帖（在本站，一本“书籍”=一个“讨论帖”）的全部数据，比如：标题，简介，文案，是否采用markdown语法，是否采用段首缩进，是否允许跟帖，是否公开发布……  
**posts表**
存放一个讨论帖里每一个单独的回帖的信息，比如作者是谁，属于哪个thread，回帖的文本是什么，使用的格式……
<br>
这**三个表之间的逻辑关系**非常简单：  
* 一个user可以发布多个thread。  
* 一个thread可以有多个post。  
* 一个thread属于且只属于一个user。  
* 一个post属于且只属于一个thread。  
#### 3.1.3 thread和post的变体
为什么会有变体？  
很简单，因为想要利用thread->posts的这个关系。比如说，一本书（book）里面有很多章节（chapter），它也可以套用现在这个关系，如果我们采用变体的结构，就不用写重复的代码。
##### 3.1.3.1 thread的变体
thread也可以是book,list,homework,request这些类型，具体什么类型，根据它所在的channel来决定  
book：书籍（里面包含章节chapter）  
homework：作业楼（还没有完成 x）  
list：清单（里面包含文评review）  
request:站务请求/举报（目前还没有完成）
...  
thread的变体，不影响它的数据结构，只要根据channel_id就可以直接筛选明白。
#####  3.1.3.2 post的变体
post的变体，涉及了数据结构的改变。post的变体又称为component，component挂靠在post下面，每一个post都有一个叫type的值，这个值告诉我们，这个post挂靠的component是哪一种。  

post的变体，目前制作完成的有chapter和review两种：  
chapter：章节。在post的基础上，增加记录volumn（分卷）信息。  
review：书评。在post的基础上，增加记录rating（评分），recommend（是否推荐）这样的信息  

以chapter的实际挂靠结果举例：  
```
'post' => [
        'id' => post_id
        'type' => 'post'
        'attributes' => [
            'user_id' => user_id
            'post_type' => 'chapter',// 'chapter'/'review'/...
            ...
        ]
        'chapter' => [
            'id' => post_id,
            'type' => 'chapter',// 'chapter'/'review'/...
            'attributes' => [
                'volumn_id' => volumn_id,
                ...
            ],
        ],
    ],
```
上面表示的，就是挂靠了chapter这个component的post，它在前端眼中会表现出来的样子。

#### 3.1.4 对书籍/讨论帖系统的授权管理：channels，tags
##### 3.1.4.1 channel（频道/板块）
channel是对thread进行统一管理的一种方式。  
站内一共预设有**12个channel**，分别是“原创小说”，“同人小说”，“作业专区”，“日常闲聊”……  
每个channel除了本身的固有属性（名称，定义，版规等字串）之外有自己的**管理逻辑**。比如，  
* type 属性管理着一个channel里的讨论帖的实际状态，可以取的值有：'book'/'thread'/'list'/'column'/'request'/'homework'  
* allow_anonymous 属性决定普通用户是否能够在本channel中匿名发布内容
* allow_edit 属性决定普通用户是否能在本channel中修改thread和post
* is_public 属性决定这个channel里的thread是否对外公开可见（如果不属于public，意味着只有注册参加作业的同学、编辑和管理，才能够进入该channel查看特殊内容）

##### 3.1.4.2 tag(标签)
标签是对thread进行标记的另一种方式    
tag和thread之间具有多对多的关系，这个关系存储在名叫tag_thread的表格里。  
一个thread可以有多个tag，一个tag也可以对应多个thread。  
tag具有不同的类型（tag_type）,有的时候，也存在一些现象，就是某些tag不能同时标记一个thread，因为它们不符合语义。  
比如说，一个thread（书籍）不能既是“HE”（tag_name="HE"），又是“BE”（tag_name="BE"）。又比如，一个thread，不能既是“长篇”，又是“短篇”。对于这些限制，描述在tag_type（tag具有不同的类型，比如说“篇幅”，“进度”，“结局”……，某些类型的tag只能最多选一个，也有些类型的tag可以同时选多个）中，在`config/tag.php`里记录了它对应的关系。

#### 3.1.5 用户身份授权管理：roles, role_user
特殊身份的用户，具有特别的权限/限制。比如，一个已经“隐藏”（thread->is_public = false）的thread，不是作者本人就不能查看它的内容。但是管理员具有查看它的权限。又比如，编辑能够审核批准题头。再比如，有一些用户因为违禁，一段时间不能登陆（被关小黑屋）/不能发帖（被禁言）/不能注册参加作业……  
因此，将用户授予特别的身份（role），来对不同的role进行权限管理。  
一个user可以有多个role，一个role可以赋予多个user，因此这也是多对多的关系，存储在role_user表格里。  
因为用户身份这个信息不容易发生变化，将它存储在`config/role.php`中。
在`app/Models/User.php` 中，写了几个方法来调用user的role，和user具体被允许的操作。比如，`user->canSeeChannel(10)`,可以用来查询这个用户能不能“访问”第10个channel的内容.

## 4. API文档
建议下载并使用postman程序，对api进行测试。
注意：Postman更改method，比如之前是GET后来是POST，有时需要【保存】才能生效。建议经常duplicate指令并将其命名保存下来，便于以后测试。
### 4.1 Authentification 权限管理
本后端采取passport对用户授权与否进行管理。其授权的基础，是采取接受token，并核对token是否属于和数据库匹配的有效token，从而验证是否能够允许用户对应的操作。
#### 4.1.1 注册新用户（register）
打开postman，选择POST方式发送信息（记得不要变成GET！）  
网址设置为`http://127.0.0.1:8000/api/register`  
在下拉parameter表单中填写内容，冒号左边是变量名称，冒号右边是对应内容。可以在postman界面中保存相关指令，便于后续重试：  
name: tester  
email: tester@gmail.com  
password: password  
password_confirmation: password  
这里字串`password`是默认的密码，也可以设置成其他字串。
然后点击发送，成功的话就会收到格式为json的返回信息，其中`code:200`表示成功，所返回的`token`就是之后用户用于登陆的验证信息。
如果信息不符合要求，会出现对应的validation错误提示。

#### 4.1.2 普通登陆（login）
已经注册的用户，也可以通过输入用户邮箱和密码登陆，来获得token用于进一步访问，方法是：
网址设置为`http://127.0.0.1:8000/api/login`，选择POST方式发送信息  
在下拉内容中填写：  
email: tester@example.com  
password: password  
然后点击发送，就可以和上面一步一样，得到正确的访问token  
如果这里发生了信息输入错误，导致信息不匹配（比如说，邮箱输错，或者密码输错），这里会收到对应的错误代码：  
401:'unauthorised'  
全部错误列表，可以从`config/error.php`查看  

#### 4.1.3 使用token，以注册用户的身份进行操作
前端默认使用
```
'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Bearer '.$accessToken,
]
```
这样的格式，来表示自己是api终端，需要以xx用户的身份通过验证。
其中$accessToken应该是在之前的login步骤中获得的。  
在postman中下拽header并填写这部分内容就行了。  
实际在postman的Headers（菜单上第三格，不是默认的，需要鼠标点开来）上面显示的效果如下：
| Key       | Value       | 备注  |
| -------------|-------------|-------------|
| Accept  | application/json |照着写就行|
| Authorization | Bearer eyJ0eXAiOiJK...|这个token字串会很长，注意Bearer和token之间有一个英文空格， 还有注意是Bearer，不是Bear|



### 4.2 错误处理 error handling
全部error 列表目前存放在`config/error.php`中，基本遵循http相关指令的约定：2xx表示成功；4xx表示请求/数据有问题；5xx表示服务器问题。具体问题的解释，在这个文件里可以看。

### 4.3 固定页面信息呈现（比如主页信息表现）

#### 4.3.1 首页信息呈现
http://127.0.0.1:8000/api  
方法：GET  
授权：不需要使用token登陆  

#### 4.3.2 文库首页(有待进一步补充信息)  
http://127.0.0.1:8000/api/homebook  
方法：GET  
授权：不需要使用token登陆  

#### 4.3.3 论坛首页(有待进一步补充信息)  
http://127.0.0.1:8000/api/homethread  
方法：GET  
授权：不需要使用token登陆

#### 4.3.4 获得固定变量
##### 4.3.4.1 全部非同人tag信息
http://127.0.0.1:8000/api/config/noTongrenTags  
方法：GET  
授权：不需要使用token登陆  
(因为大部分tag是同人tag，避免加载全部tag)

##### 4.3.4.2 获得全部channel信息
http://127.0.0.1:8000/api/config/allChannels  
方法：GET  
授权：不需要使用token登陆  


### 4.4 资源页面信息呈现（比如目录检索，某一本书的首页信息）

#### 4.4.1 全站范围的thread index（也用于文库淘文）
(未来不确定是否拆分专门的文库淘文界面，待定)
http://127.0.0.1:8000/api/thread  
方法：GET  
授权：可选登陆与否，视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容）  
解释：这个api用于在全站范围内，过滤筛选符合条件的无论是什么种类的thread，并且显示它的最基本的信息，所有筛选嵌套进行  

可选的筛选变量及效果：  
channels(array)=[1,2,3] ：筛选channel。  
必须是array，不能是单独的值，只返回出现在channel1，2，3中的讨论帖   

withType(string)='thread'/'book'/'list'/'request'/'homework' ：筛选thread 的类型（根据它所从属的channel来决定）  

tags(array)=[1,22,4]：仅返回含有1，22，4这几个tag的书籍/讨论帖，只要符合其中之一就返回，不一定全部匹配。  

excludeTags(array)=[1,22,4]：仅返回肯定不含有1，22，4这几个tag的书籍/讨论帖

withBianyuan(string)='bianyuan_only'/'none_bianyuan_only' / 'all'：是否仅返回边缘/非边缘内容  

ordered(string)='latest_add_component' / 'jifen' / 'weighted_jifen' / 'latest_created' / 'id' /  'collection_count' / 'total_char' / 'random'：按最新更新时间排序/按总积分排序/按平衡积分排序/按创建时间排序/按id排序/按收藏总数排序/按总字数排序/随机排序:默认按最新回复排序  


#### 4.4.2 thread/post信息呈现

##### 4.4.2.1 展示单纯的首页信息和最早的posts（以后可以根据前端的需求，增加其他内容）
http://127.0.0.1:8000/api/thread/1  
方法：GET  
授权：可选登陆与否，视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容）  
按讨论帖格式，返回id=1（id可以更换成其他数字）的讨论帖首页信息   
视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容，只有符合要求的用户才能获得相关信息）   
单纯按照时间顺序排列post, 从早到晚

##### 4.4.2.2 获得thread内post信息（提供高级筛选功能）
http://127.0.0.1:8000/api/thread/{thread}/post  
方法：GET  
授权：可选登陆与否，视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容）  
视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容，只有符合要求的用户才能获得相关信息）
可选项：
withType(string):'post','comment','chapter','review'是否仅筛选出某种格式的post
withComponent(string):'component_only'/'none_component_only'是否仅筛选出属于component/不属于component的post
userOnly(int):仅返回xx用户的非匿名贴
withReplyTo(int):仅返回针对某个post的所有回帖
ordered(string):'latest_created'/'most_replied'/'most_upvoted'/'latest_responded'/'random'//默认按照时间顺序排列，越早越先出现

##### 4.4.2.3 获得thread内单独post的component的信息
http://127.0.0.1:8000/api/thread/{thread}/post/{post}
方法：GET  
授权：可选登陆与否，视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容）  
视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容，只有符合要求的用户才能获得相关信息）
解释：返回单章(chapter)或单个书评(review)内情。

### 4.4.3 book信息呈现

##### 4.4.3.1 获得书籍首页信息（首楼，部分章节列表）
http://127.0.0.1:8000/api/book/1  
方法：GET  
授权：可选登陆与否，视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容）    
按书籍格式，返回id=1（id可以更换成其他数字）的书籍首页信息    。  
thread, 文案及书籍数据信息  
author, 作者信息  
channel, 频道信息
tags, 文章标签信息
last_component, 最新章节
last_post，最新回复
chapters，部分章节列表（不全）,
volumns，这部分章节的分卷信息）,
pagination, 章节pagination（请使用另外的route：chapteriindex，直接获得全部章节目录信息，不要进一步使用这里的pagination，比较浪费）
most_upvoted, 最高赞的评论
top_review，最热书评
视登陆与否返回不同结果（只有登陆后返回内容才包含边缘内容，只有符合要求的用户才能获得相关信息）


##### 4.4.3.2 获得书籍章节列表
http://127.0.0.1:8000/api/book/1/chapterindex  
方法：GET  
获得本书的全部章节列表

#### 4.4.4 全站书评列表
http://127.0.0.1:8000/api/review   
方法：GET    
可选变量：  
thread_id(int):针对哪本书的评论  
withRecommend(string): 'recommend_only' (默认)/ 'none_recommend_only'/ 'all';  
withEditor(string): 'none_editor_only' / 'editor_only' / 'all'   
withLong(string): 'long_only' / 'short_only'  是只输出长评，还是只输出短评（不选则两种兼有）
withMinRating(int): 最少几分以上（可以选择打过分的）  
withMaxRating(int): 最多几分（可选择没打分的）  
ordered(string): 'latest_created'/ 'most_upvoted' / 'most_redirected' (默认按最多导航排序) / 'oldest_created' / 'random',    

#### 4.4.5 查看信箱
http://127.0.0.1:8000/api/user/1/message   
方法：GET  
授权：当前登录用户为管理员或当前登录用户访问自己的信箱  
必填项：  
withStyle(string): 'sendbox'/'receivebox'(默认)  /'dialogue',如果为'dialogue'则必填chatWith(int)  
选填项：  
chatWith(int)：（只有withStyle=dialogeue的时候必填）和谁的对话  
ordered(string) :'oldest'/'latest'(默认)  
read(string) :'read_only'/'unread_only'(默认显示全部，只有为receivebox时选填)  


### 4.5 增改删resource信息
#### 4.5.1 thread
##### 4.5.1.1 存储讨论帖/书籍/清单 store thread
http://127.0.0.1:8000/api/thread  
方法：POST  
授权：必须登陆  
如果是list，还会受数目限制

必填项：  
channel_id(int) 数字，必须为自己有权限访问的channel
title(string)：讨论帖标题  
brief(string)：讨论贴简介  
body(string)：首楼内容  

选填项：  
no_reply(bool)：是否不允许其他人在本楼回复，如果本项存在，则不允许回复。  
use_markdown(bool):是否使用markdown，如果本项存在，则保存为使用markdown。  
use_indentation(bool):是否使用段首缩进，如果本项存在，则保存为使用段首缩进。  
is_bianyuan(bool)：是否属于边缘内容，如果本项存在，则具体内容不被游客看到。（前端应控制，如果是thread选择bianyuan，简介以“午夜场”开头，并且提示用户本站关于午夜场的规则要求）  
is_public(bool)：是否属于公开内容，如果本项为true，则本楼整体公开。如果本项为false，则本楼整体隐藏。（前端应控制只有创立books才提交这个值）  

##### 4.5.1.2 修改讨论帖/书籍/清单 update thread  
http://127.0.0.1:8000/api/thread/{thread}  
方法：PUT|PATCH  
授权：必须登陆,且用户必须是创建thread的用户
选填项：
title(string)：讨论帖标题  
brief(string)：讨论贴简介  
body(string)：首楼内容   
no_reply（bool）：是否不允许其他人在本楼回复，如果本项存在，则不允许回复。  
use_markdown(bool):是否使用markdown，如果本项存在，则回帖保存为使用markdown。  
use_indentation(bool):是否使用段首缩进，如果本项存在，则回帖保存为使用段首缩进。  
is_bianyuan（bool）：是否属于边缘内容，如果本项存在，则回帖不被游客看到。（前端应控制，如果是thread选择bianyuan，简介以“午夜场”开头，并且提示用户本站关于午夜场的规则要求）  
is_public（bool）：是否属于公开内容，如果本项为true/1，则本楼整体公开。如果本项为false/0，则本楼整体隐藏。（前端应控制只有创立books才提交这个值）  

##### 4.5.1.3 给thread批量修改sync对应的tag  
http://127.0.0.1:8000/api/thread/{thread}  /synctags
方法：POST    
授权：必须登陆,且用户必须是创建thread的用户    
必填项：  
tags(array)用户希望增减的所有tag列表  
成功的话会返回200，和成功添加的tags  
不成功的话会返回422，并且返回输入的tag，和剔除不合格之后剩下的tag，供检查差异。  

#### 4.5.2 post
##### 4.5.2.1 存储某个post store post  
http://127.0.0.1:8000/api/thread/1/post  
method: POST  
授权：必须登陆, 必须能够访问这个thread  
必填项：  
body(string):回帖正文  
brief(string):回帖摘要，由前端剪裁好提供，不得超过50字
选填项：  
title(string):仅对回帖够长（比如超过200字）的时候，提供填写title的选择  
is_anonymous(bool):是否匿名，如果本项存在，且本channel属于可以匿名，则回帖存储为匿名。   
majia(string):马甲 仅当存在“is_anonymous”的时候才保存马甲内容。  
reply_id(number):回复对象的post_id。前端务必检查好，这个post需要同时属于这个thread  
use_markdown(bool):是否使用markdown，如果本项存在，则回帖保存为使用markdown。  
use_indentation(bool):是否使用段首缩进，如果本项存在，则回帖保存为使用段首缩进。  
is_bianyuan（bool）：是否属于边缘内容，如果是，则回帖不被游客看到。

##### 4.5.2.2 修改post update post  
http://127.0.0.1:8000/api/thread/1/post/1  
method: PATCH  
授权：必须登陆, 必须能够访问这个thread，thread属于能够修改的channel内
选填项：  
body(string):回帖正文  
brief(string):回帖摘要，由前端剪裁好提供，不得超过50字
title(string):仅对回帖够长（比如超过200字）的时候，提供填写title的选择
is_anonymous(bool):是否匿名，如果本项存在，且本channel属于可以匿名，则回帖存储为匿名。  
use_markdown(bool):是否使用markdown，如果本项存在，则回帖保存为使用markdown。  
use_indentation(bool):是否使用段首缩进，如果本项存在，则回帖保存为使用段首缩进。

##### 4.5.2.3 删除某个post destroy post（待做）

##### 4.5.2.4 删除某个post对应挂靠的component destroy post-component（待做）
解释：相当于把chapter/review改成普通post

#### 4.5.3 Recommendation (书籍推荐，本功能待全面修改)
##### 4.5.3.1 存储推荐store recommendation
http://127.0.0.1:8000/api/recommendation
方法：POST  
授权：必须登陆,必须具有editor或senior_editor或admin身份  
必填项：  
thread_id(number):必须具有能够检索到的一个被推荐thread  
brief(string):必须具有一句话推荐简介  
type(string):'short'/'long'/'topic' 必须是下面array中的一项  
**注意事项**
（一个thread&&type组合，它只能有一个推荐。也就是说，一个书籍最多只有一个短推——可以再有长推）  
选填项:  
body(string):长推的话，在这里写入长推推荐语  
users(array of integers):e.g.[1,2,3] 这个推荐语的作者。书籍推荐语允许合作完成。

##### 4.5.3.2 审阅/修改 recommendation (书籍推荐的审阅)
http://127.0.0.1:8000/api/recommendation
方法：PATCH
授权：必须登陆,必须是自己或senior_editor或admin身份  
选填项:     
brief(string):必须具有一句话推荐简介    
body(string):长推的话，在这里写入长推推荐语  
is_public(bool):是否公开（不公开的话，其他人不能在书籍下看见）这个信息必须是senior_editor/admin才能改变，也就是说，书籍推荐在editor建立之后，需senior_editor审阅再转公开  
is_past(bool):是否属于往期推荐（影响首页显示情况）这个信息必须是senior_editor/admin才能改变，也就是说，书籍推荐需senior_editor审阅之后转公开  


#### 4.5.4 chapter相关
##### 4.5.4.1 新建chapter  
http://127.0.0.1:8000/api/thread/{thread_id}/chapter  
方法：POST  
授权：必须登陆，需是自己创建的thread，需要这个thread属于book，thread没有被锁  
必填项：  
title(string)章节标题  
brief(string)章节概要/预览（如果用户不输入，前端自动节选title+body的一部分片段作为概要）  
body(string)章节内容  
选填项：
warning(string):文前预警（应有字数限制）  
annotation(string):作者有话说/章节注释  


##### 4.5.4.2 更新chapter
http://127.0.0.1:8000/api/thread/{thread_id}/chapter/{chapter_id}
方法：PUT/PATCH  
授权：必须登陆，需是自己创建的thread，需要这个thread属于book，thread没有被锁   
选填项  
title(string)章节标题  
brief(string)章节概要/预览（如果用户不输入，前端自动节选title+body的一部分片段作为概要）  
body(string)章节内容  
warning(string):文前预警（应有字数限制）  
annotation(string):作者有话说/章节注释


#### 4.5.5 collection相关
##### 4.5.5.1 新建collection
http://127.0.0.1:8000/api/thread/{thread}/collect
方法：POST  
授权：必须登陆，必须具有观看当前thread的权限

##### 4.5.5.2 展示当前用户(或管理员指定用户)的所有collection
http://127.0.0.1:8000/api/collection
方法：GET  
授权：必须登陆  
可选变量：  
user_id(integer) 指定显示某人的收藏（必须管理员输入才起效）
withType(string)='thread'/'book'/'list'/'request'/'homework' ：是否仅返回书籍/讨论帖/收藏单/信息
ordered(string)='latest_add_component'/'jifen'/'weighted_jifen'/'latest_created'/'id'/'collection_count'/'total_char'：按最新更新时间排序/按总积分排序/按平衡积分排序/按创建时间排序/按id排序/按收藏总数排序/按总字数排序:默认按最新回复排序   

##### 4.5.5.3 修改collection，更改是否跟踪显示更新提醒
http://127.0.0.1:8000/api/collection/{collection}
方法：PATCH
授权：必须登陆，必须是自己的collection
必选变量：  
keep_updated(boolean)是否继续更新提醒  

##### 4.5.5.4 删除现有collection
http://127.0.0.1:8000/api/collection/{collection}
方法：DELETE
授权：必须登陆，必须是自己的collection

#### 4.5.6 review相关
##### 4.5.6.1 新建review
http://127.0.0.1:8000/api/thread/{thread}/review
方法：POST  
授权：必须登陆，是thread的主人，thread属于‘list’  
必填项：  
reviewee_id: 被review的书籍它的thread_id
选填项：
title(string):评论标题  
brief(string):评论人不愿填充的话，前端应该自行填充节选的评论  
body(string):评论正文  
use_markdown(bool):是否使用md语法
use_indentation(bool)：是否段首缩进
recommend(bool)：是否推荐（推荐的话，书本首页、网站首页会显示这个评论，作者会得到通知）
rating(int) 可填写1~10的评分，也可以空置，空置为0


##### 4.5.6.2 修改review内容
http://127.0.0.1:8000/api/thread/{thread}/review/{review}
方法：PATCH
授权：必须登陆，必须是自己的review，必须是自己的list
选填项  
title(string):评论标题  
brief(string):评论人不愿填充的话，前端应该自行填充节选的评论  
body(string):评论正文  
use_markdown(bool):是否使用md语法
use_indentation(bool)：是否段首缩进
recommend(bool)：是否推荐（推荐的话，书本首页、网站首页会显示这个评论，作者会得到通知）
rating(int) 可填写1~10的评分，也可以空置，空置为0

#### 4.5.7 history用户断点反馈信息/书签/阅读历史（制作中）

##### 4.5.7.1 用户看自己的阅读历史  
http://127.0.0.1:8000/api/history  
方法：GET  
授权：必须登陆，要么看自己的review，要么是管理员，可以看指定用户的history  
选填项：  
user_id(int)(只有管理填了有用)  
withInDays(int):检索最后多少天内的历史  
withBeforeDays(int):多少天前的历史  
thread_id(int):和某本书有关的历史  
withMinMinutes(int):只看阅读时间在多少分钟以上的记录  
withMaxMinutes(int):只看阅读时间在多少分钟以下的记录  

返回paginated历史，和总计时间  

##### 4.5.7.2 前端定时（每天，或每积累一定程度，返回相关数据）  
http://127.0.0.1:8000/api/history  
方法：POST  
授权：必须登陆  
选填项：  
readingRecords(json)：浏览历史记录  
reviewRedirects(json)：书评重定向记录  
格式示范如下：  
```
readingRecords(json) => [
    [
        thread_id => thread_id, //读过的书的id
        last_read_component => post_id, //最后读到哪一章
        reading_minutes => time,//读了多少分钟
    ],[
    ...//下一本书
    ]
],
reviewRedirects(json) => [
    [
        review_id => post_id, // 这个文评的post_id
    ],[
    ...
    ]
]
```
#### 4.5.8 quote相关
###### 4.5.8.1 创建quote
http://127.0.0.1:8000/api/quote  
方法：POST  
授权：必须登陆  
必填项：  
body(string) 题头内容  
选填项：  
is_anonymous(bool) 是否匿名  
majia(string): 马甲，仅当存在“is_anonymous”的时候才保存马甲内容

#### 4.5.9 message相关
###### 4.5.9.1 创建message
http://127.0.0.1:8000/api/message  
方法：POST  
授权：必须登陆，且登陆用户的message_limit>0，接收用户的no_stranger_message=0  
必填项：  
sendTo(int) 接收用户id  
body(string) 消息内容

###### 4.5.9.2 管理员群发私信
http://127.0.0.1:8000/api/groupmessage   
方法: POST  
授权：必须登陆，且登录用户为管理员  
必填项：  
sendTos(array) 所有接收用户id  
body(string) 消息内容

###### 4.5.9.3 管理员发系统消息  
http://127.0.0.1:8000/api/publicnotice  
方法：POST  
授权：必须登陆，且登录用户为管理员  
必填项：  
body(string) 系统消息内容  

#### 4.5.10 vote相关
###### 4.5.10.1 创建vote
http://127.0.0.1:8000/api/vote
方法：POST
授权：必须登陆
必填项：
votable_type(string):'Post'|'Quote'|'Status'|'Thread' 被投票对象
votable_id(int) 被投票对象id
attitude(string):'upvote'|'downvote'|'funnyvote'|'foldvote' 投票类型

###### 4.5.10.1 展示votes
http://127.0.0.1:8000/api/vote
方法：GET
授权：无须登陆
必填项：
votable_type(string):'Post'|'Quote'|'Status' 被投票对象
votable_id(int) 被投票对象id

#### 4.5.11 follower相关
##### 4.5.11.1 展示用户的所有粉丝
http://127.0.0.1:8000/api/user/{user}/follower/
方法：GET
授权：不需要使用token登陆  

##### 4.5.11.2 展示用户的所有关注
http://127.0.0.1:8000/api/user/{user}/following/
方法：GET
授权：不需要使用token登陆  

##### 4.5.11.3 关注某个用户
http://127.0.0.1:8000/api/user/follow/{user}
方法：POST
授权：必须登录，只能为本人账户操作

##### 4.5.11.4 取关某个用户
http://127.0.0.1:8000/api/user/follow/{user}
方法： DELETE
授权：必须登录，只能为本人账户操作

##### 4.5.11.5 切换是否跟踪某账户的动态
http://127.0.0.1:8000/api/user/keepNotified/{user}
方法：PATCH
授权：必须登录，只能为本人账户操作

##### 4.5.11.5 获取与某段关注关系相关的信息（是否跟踪动态，是否已阅更新）
http://127.0.0.1:8000/api/user/follow/{user}
方法：GET
授权：必须登录，只能为本人账户操作  

#### 4.5.12 administration相关  
##### 4.5.12.1 管理员可创建管理记录并作出管理
http://127.0.0.1:8000/api/manage  
方法：POST  
授权：必须登录，且登录用户为管理员  
必填项：  
administratable_type(string):'user'|'thread'|'post'|'quote'|'status' 被管理内容类型  
administratable_id(int) 被管理内容id  
administration_type(string):'delete'... 管理操作  
reason(string) 原因  
选填项：  
report_id(int) 举报人id  
options(json) 具体执行操作，如禁言天数、匿名马甲...  
is_public(boolean) 是否公开

## 5. 如何测试
#### 5.1 写一个新的专项测试文件
在backend/tests/Feature目录下，放置对应的测试文件。
一些常用的test技巧如下：
```
$response = $this->post('api/thread/', $data);//可以直接使用post指令检查

//dd($response->decodeResponseJson());//同样可以使用helper，直接输出response的具体内容，查看错误原因

$response->assertStatus(200)//直接检查status是否符合需求

//可以连续使用->来直接进行多次连续的assert检查。
->assertJsonStructure([//检查数据结构，这适合检查类似于token的，只需要检查有没有，不需要检查是多少的代码。
    'code',
    'data' => [
        //...
    ],
])
->assertJson([//检查具体的数据的值是否和预期相符合，比如具体的内容是否经过了修改，存储为新的值，数据类型是否符合预期
    'code' => 200,
    'data' => [
        'type' => 'thread',
        'attributes' => [
            ...
        ],
    ],
]);

```


#### 普通测试
在backend目录下，运行  
```
vendor/bin/phpunit
```
进行测试。  
如果想要测试具体的某一个内容，可以运行如下代码：  
```
vendor/bin/phpunit --filter 'ChapterTest'
```
换成自己想要单独测试的内容即可。

#### 提交代码前进行最后检查（注意，这会重置数据库，最好先确保其他地方没有明显的问题）
每次新提交完成的pull request之前，后端应该确保自己的工作和现有backend能够协调、在一个fresh database的基础上能够通过检测，方法如下：  
```
git pull
php artisan migrate:reset
php artisan migrate --seed
php artisan passport:install
vendor/bin/phpunit
```
如果这一步发生了报错，那么需要进一步寻找到底是哪里出了问题。  

另外，请确保api documentation里，包含这个api会接受的所有变量和它们的可能的类型（包括必填项、可填项），确保相关api存储行为所改变的所有变量都在test中获得值的检查（比如说，不要出现想要修改xx变量，结果test里没有确保这个值经过修改，因此虽然返回成功代码200，实际上数据库里并没有存储对应值改变……不要出现这样的情况）  
