CREATE DATABASE


Schema::create("example",function(Table $table){
    $table->increments('id');
    $table->varchar('country',225);
    $table->varchar('language',225);
    $table->varchar('lang_code',225);
    $table->varchar('hrefLang',225);
});


DROP DATABASE

Schema::drop('example');


MIGRATION

Migration::up("TestMigration");

Seeder::run("TestTableSeeder");


USE MODELS


CREATE NEW

$example = Example::getInstance();
$example->nave = "France";
$example->value = "country";
$example->save();

GET ALL DATA

Example::getAll();

GET DATA WHERE
Example::where('id','=',1);


UPDATE

$example = Example::getInstance();
$example->nave = "France";
$example->value = "country";
$example->save();
$example->update('id', '=', '2');


DELETE

$example::where('id','=',1)->delete();