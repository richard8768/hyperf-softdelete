# richard8768/hyperf-softdelete

Hyperf Soft Delete Extension: Custom Soft Delete Fields and Deletion Values for Hyperf
Facilitates the creation of a unique composite index containing Soft Delete Fields.

Hyperf 软删除扩展：为 Hyperf 自定义软删除字段及删除标识值
便于创建包含软删除字段的唯一复合索引

> QQ:444626008

## Default configuration 默认配置

```
const DELETED_AT = 'deleted_at';//Soft Delete Fields,the default value is deleted_at 软删除字段 默认值 deleted_at
const UN_DELETED_VALUE = 0;//undeleted value for the soft delete field,the default value is 0 软删除字段数据未删除时的值 默认值 0
const TIMESTAMP_TYPE = 'seconds';//only support seconds milliseconds nanoseconds,nanoseconds achieves functionality through hrtime (true) 时间戳类型 秒 微妙 纳秒(通过hrtime(true)实现)

DDL for Soft Delete Field `deleted_at` 软删除字段`deleted_at`的DDL
`deleted_at` int NOT NULL DEFAULT '0' //only support seconds 只支持秒
`deleted_at` bigint NOT NULL DEFAULT '0'//support seconds milliseconds nanoseconds 支持秒 微妙 纳秒
```

## install 安装


```bash
composer require richard8768/hyperf-softdelete
```

## Useage 使用

```
Supposing your model is MemberAddress.php 假设你的模型是 MemberAddress.php
here is the config code 配置如下
<?php

declare (strict_types=1);

namespace App\Model;

use Richard\HyperfSoftdelete\SoftDeletes;//import file 导入文件

class MemberAddress extends Model
{
    use SoftDeletes;//use import file 使用导入的文件
    
    //const TIMESTAMP_TYPE = 'milliseconds'; //to use milliseconds 时间戳使用微妙
    //const TIMESTAMP_TYPE = 'nanoseconds'; //to use nanoseconds 时间戳使用纳秒

    /**
     * The table associated with the model.
     *
     * @var null|string
     */
    protected ?string $table = 'member_address';
    protected ?string $dateFormat = 'U';


}

//Delete data 删除数据
MemberAddress::where('member_id', 1)->where('id', 11)->delete();
//Restore deleted data 恢复删除的数据
MemberAddress::withTrashed()->where('member_id', 1)->restore();
//Force permanently delete deleted data 强制删除已删除的数据
MemberAddress::onlyTrashed()->where('member_id', 1)->forceDelete();
//Force delete data directly 直接强制删除数据
MemberAddress::where('member_id', 1)->forceDelete();
//Query includes deleted data 查询包含已删除的数据
$listAddress = MemberAddress::withTrashed()->where('member_id', 1)->select()->get();
//Query deleted data 查询已删除的数据
$listAddress = MemberAddress::onlyTrashed()->where('member_id', 1)->select()->get();
```
