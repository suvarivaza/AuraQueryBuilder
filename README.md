# AuraQueryBuilder

**Usage**

```
use Suvarivaza\AQB\QueryBuilder;
$db = new QueryBuilder($config);

SELECT ('table', 'colums', [where])
$db->select('posts', '*', ['id', '=', 1]);
$db->getAll('posts', 'obj'); /OR
$db->getOne('posts', 6)); //OR


UPDATE ('table', [data], [where])
$db->update('posts', ['title' => 'new post title'], ['id', '=', 1]);

INSERT ('table', [data])
$db->insert('posts',  ['title' => 'post title']);

DELETE ('table', [where])
$db->delete('posts',  ['id' => 1]);

EXISTS ('table', [where])
$db->exists('posts', ['id', '=', 1]);


```
