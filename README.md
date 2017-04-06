# elasticsearch-service for laravel5
This package provides a unified API across a variety of different full text search services.

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
Add now, the service provider to app/config/app.php, within the providers array.
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
Publish the default config file to your application then make modifications as you can.
```bash
php artisan vendor:publish
```
The following dependencies are needed for the listed search drivers:
```json
{
"php": ">=5.5.0",
"illuminate/support": "~5.1",
"elasticsearch/elasticsearch": "~2.0"
}
```
## Usage
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
```
### 6、search for increase or decrease data
```php
Search::queryString('name:"海盗之王"')->increase('age');
Search::queryString('name:"海盗之王"')->increase('age', 2);
Search::queryString('name:"海盗之王"')->decrease('age', 3);
```
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
You can select fields show when use output format.
```php
Search::select(['name', 'age'])->search()->outputFormat();
```
Default paging is true and show the result first ten, If you don't need it
```php
Search::select(['name', 'age'])->search(false)->outputFormat();
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
Or the conditions with range
```php
Search::range('age', [7, 18], ['gt', 'lte']);
```
However, the range query has fourth parameter which use as extra action.  


