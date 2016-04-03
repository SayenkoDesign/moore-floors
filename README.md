This is a Empty WordPress website with plugins we frequently use along with a base theme so you can quickly get started on new projhects.

#Base Theme

## Base Theme Requirements
The base theme makes use of the latest technologies and requires the following technologies
- Git
- Compass
- NodeJS
- Gulp
- Ruby
- Compass
 
## Installing
1. In your command line go to the base theme directory `wp-content/themes/sayenkodesign`
2. run `composer install`
3. run `npm install` 
4. run `bower install`
5. run `gulp`.

## Gulp Commands
| command | description |
| ------- | ----------- |
| gulp | default gulp command will run all the other commands. Runs watch last |
| gulp images | Minify images. Supports jpg, png, and gif |
| gulp scripts | Merge and minify jquery, foundation and your app.js file |
| gulp sass | Compile sass files
| gulp watch | watches all source files and runs the appropriate gulp command if a file is added or changed |
