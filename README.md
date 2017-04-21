# chenyuanqi/elasticsearch not only for laravel5
This package depends on "elasticsearch/elasticsearch" and it provides a unified API across a variety of different full text search services.  
> Notice: just test for elasticsearch 2.3.3  

The following dependencies are needed for the listed search drivers:
```json
{
"php": ">=5.5.0",
"illuminate/support": "~5.1",
"elasticsearch/elasticsearch": "~2.0"
}
```
## Structure 
├── Commands  
│   └── ElasticsearchService.php  
├── config  
│   └── elasticsearch.php  
├── Analyze.php  
├── Builder.php   
├── Query.php  
├── SearchFacade.php  
└── SearchServiceProvider.php  

## Suggestion
For safety reasons, please install the plugin: [shield](https://www.elastic.co/downloads/shield "shield")  
For search effectively, these plugins may be useful for you:  
1、[head](https://github.com/mobz/elasticsearch-head)  
2、[bigdesk](https://github.com/hlstudio/bigdesk)  
3、[kopt](https://github.com/lmenezes/elasticsearch-kopf)  
4、[sql](https://github.com/NLPchina/elasticsearch-sql)  
5、[ik](https://github.com/medcl/elasticsearch-analysis-ik)  
6、[pinyin](https://github.com/gitchennan/elasticsearch-analysis-lc-pinyin)  
7、[同义词](https://github.com/bells/elasticsearch-analysis-dynamic-synonym)  
8、[简繁转换](https://github.com/medcl/elasticsearch-analysis-stconvert)  
## Install
 You can edit composer.json file, in the require object:
 ```json
{
"chenyuanqi/elasticsearch": "dev-master"
}
```
Or use composer command:
```bash
composer require chenyuanqi/elasticsearch
```
After that, run composer update to install this package.  
Actually, it was finished but in ***laravel5*** that you still let the service provider to app/config/app.php, within the providers array.
```php
'providers' => [
	// elasticsearch service
	chenyuanqi\elasticsearch\SearchServiceProvider::class,
],
```
Add a class alias to app/config/app.php, within the aliases array.
```php
'aliases' => [
	// elasticsearch service facade
	'Search' => chenyuanqi\elasticsearch\SearchFacade::class,
],
```
## Configure
In ***laravel5***, publish the default config file to your application then make modifications as you can.
```bash
php artisan vendor:publish
```
However, default config file is _config/elasticsearch.php_.  
## Laravel Usage
### 1、search for create index
```php
Search::createIndex();
```
Notice: Index name must be lowercase.
### 2、search for mapping due to config
```php
Search::createMapping();
Search::updateMapping();
Search::deleteMapping();
```
### 3、search for select index and type
```php
Search::index('test')->type('test');
```
Notice: Here index and type has default value.
### 4、search for insert data 
```php
$data = [
    'name'  => 'Kyyomi',
    'age'   => 18,
    'birth' => '2017-03-03'
];
// However, you can set id for the record. For instance, "Search::insert($data, 1);"
Search::insert($data);
```
### 5、search for update data  
Here provide two way for update data, 
```php
$data = [
    'birth' => '1999-03-03'
];
// update by id
Search::updateById($data, 1);
// update by query
Search::queryString('name:"Kyyomi"')->update($data);
```
By the way, use update by query must open the script setting
```yaml
# In elasticsearch 2.3.3, allow script operate
  script.inline: true
  script.indexed: true
  script.file: true
  script.engine.groovy.inline.update: true
  script.engine.groovy.inline.aggs: true
```
### 6、search for increase or decrease data
```php
Search::queryString('name:"海盗之王"')->increase('age');
Search::queryString('name:"海盗之王"')->increase('age', 2);
Search::queryString('name:"海盗之王"')->decrease('age', 3);
```
Like update by query, increase or decrease also open the script setting
### 7、search for delete data  
Here provide two way for delete data, 
```php
$data = [
    'birth' => '1999-03-03'
];
// delete by id
Search::deleteById(1);
// delete by query, not support for version >2.0 (consider plugin: delete-by-query) 
Search::queryString('name:"Kyyomi"')->delete();
```
### 8、search for clean index
```php
Search::truncate();
```
### 9、search for bulk
```php
$data = [
    [
        'index',
        '_id'  => 1,
        'name' => 'viki',
        'age'  => 18
    ],
    [
        'create',
        '_id'  => 2
        'name' => 'lucy',
        'age'  => 15
    ],
    [
        'update',
        '_id'  => 1,
        'name' => 'vikey',
        'age'  => 28
    ],
    [
        'delete',
        '_id' => 2
    ]
]
Search::bulk($data);
```
Notice: Default handle is 'index'.  
### 10、everything is for search   
You can select fields show for search.
```php
Search::pluck(['name', 'age'])->search();
```
Default paging is true and show the result first ten, If you don't need it
```php
Search::pluck(['name', 'age'])->search(false);
```
Construct the conditions with queryString, just like that
 ```php
 Search::queryString('name=Kyyomi');
 ```
 Or the conditions with filter
 ```php
 Search::filter('status', 'show');
 ```
 Or the conditions with ids
 ```php
 Search::ids([1, 2, 3]);
 // If other field with in or not in function
 Search::whereIn('name', ['A', 'B', 'C']);
 Search::whereNotIn('name', ['A', 'B', 'C']);
 ```
 Or the conditions with match
 ```php
 Search::match('name', 'Kyyomi', 'match');
 Search::match(['name', 'age'], 'Kyyomi', 'multi_match');
 Search::match('Kyyomi');
 ```
 Or the conditions with term
 ```php
Search::term('name', 'Kyyomi');
```
Or the conditions with bool
```php
// The third parameter include must(default value), must_not, should, filter.
Search::bool('name', 'Kyyomi', 'must_not');
```
Or the conditions with null
```php
Search::isNull('name');
// If need the field is not null
Search::isNotNull('name');
```
Or the conditions with aggregation
```php
Search::max('id');
Search::min('id');
Search::sum('id');
Search::avg('id');
```
Or the conditions with range
```php
Search::range('age', [7, 18], ['gt', 'lte']);
```
However, the range query has fourth parameter which use as extra action.  
Or the conditions with where query
```php
Search::where('id', '=', 100)->search();
// The same as last sentence
Search::where('id', 100)->search();
Search::where('id', '=', 100)->orWhere('age', '>=', 18)->search();
// Also, we can use like query
Search::where('name', 'like', '%天天%')->search();
// any more where function like whereBetween, whereNotBetween
Search::whereBetween('id', [1, 2]);
```

Here are two ways When we need paging.
```php
// paging style
Search::queryString('name:"珍珠海盗"')->limit(0, 10)->search();
// scroll style
Search::queryString('name:"珍珠海盗"')->scroll(1000, '30s', 'scan')->search();
// If you want use scroll id for search or delete it
Search::searchByScrollId('xxx');
Search::deleteByScrollId('xxx');
```
And count the record, just use the count function.
```php
Search::queryString('name:"珍珠海盗"')->count();
```

At last, use the debug function that output the debug message as you need. 
```php
Search::queryString('name:"珍珠海盗"')->search();
Search::debug();
// If you need curl sentence, do it
Search::toCurl();
```
Notice: you must output the message after search.  

## Others Usage
You know, it uses the facade design pattern above all of [laravel usage](https://github.com/chenyuanqi/elasticsearch#laravel-usage).  
So in here, just replace the Search object like that:
```php
use chenyuanqi\elasticsearch\Builder;
$search = new Builder(false);
```
All right, Happy hacking~  
