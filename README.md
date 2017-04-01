# elasticsearch-service for laravel5
This package provides a unified API across a variety of different full text search services.

## Structure 
├── Commands  
│   └── ElasticsearchService.php  
├── config  
│   └── elasticsearch.php  
├── Analyze.php  
├── Builder.php  
├── Facade.php  
├── Query.php  
└── SearchServiceProvider.php  

## Install
 You can edit composer.json file, in the require object:
 ```json
"chenyuanqi/elasticsearch": "dev-master"
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
	'Search' => chenyuanqi\elasticsearch\Facade::class,
],
```
## Configure
Publish the default config file to your application then make modifications as you can.
```bash
php artisan vendor:publish
```
The following dependencies are needed for the listed search drivers:
```json
"php": ">=5.5.0",
"illuminate/support": "~5.1",
"elasticsearch/elasticsearch": "~2.0"
```
## Usage
### 1、search for create index
```php
Search::createIndex();
```
### 2、search for mapping due to config
```php
Search::createMapping();
Search::updateMapping();
Search::deleteMapping();
```
### 3、search for insert data 
```php
$data = [
    'name'  => 'Kyyomi',
    'age'   => 18,
    'birth' => '2017-03-03'
];
// However, you can set id for the record. For instance, "Search::insert($data, 1);"
Search::insert($data);
```
### 4、search for update data  
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
### 5、search for delete data  
Here provide two way for delete data, 
```php
$data = [
    'birth' => '1999-03-03'
];
// delete by id
Search::deleteById(1);
// delete by query
Search::queryString('name:"Kyyomi"')->delete();
```
### 6、search for clean index
```php
Search::truncate();
```
### 7、everything is for search   
...
