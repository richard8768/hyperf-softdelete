# richard8768/hyperf-softdelete

Hyperf Soft Delete Extension: Custom Soft Delete Fields and Deletion Values for Hyperf

> QQ:444626008

## Default configuration

```
const DELETED_AT = 'deleted_at';//Soft Delete Fields,the default value is deleted_at
const UN_DELETED_VALUE = 0;//undeleted value for the soft delete field,the default value is 0

definition of the field `deleted_at`
`deleted_at` int NOT NULL DEFAULT '0'
```

## Useage

```
Supposing your model is MemberAddress.php
here is the config code
<?php

declare (strict_types=1);

namespace App\Model;

use Richard\HyperfSoftdelete\SoftDeletes;

/**
 * @property int $id
 * @property int $category_id
 * @property string $article_name
 * @property int $status
 */
class MemberAddress extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var null|string
     */
    protected ?string $table = 'member_address';
    protected ?string $dateFormat = 'U';


}
```
