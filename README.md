# AuraQueryBuilder

**Usage**

```
$config = ['driver' => 'mysql', // Db driver
'host' => 'localhost',
'db_name' => 'your-database',
'db_user' => 'root',
'db_password' => 'your-password',
'charset' => 'utf8', // Optional
'prefix' => 'cb_', // Table prefix, optional
'options' => [ // PDO constructor options, optional
PDO::ATTR_TIMEOUT => 5,
PDO::ATTR_EMULATE_PREPARES => false,
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
],
];

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
