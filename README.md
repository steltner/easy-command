# easy command

## Install
    php easy

## Usage
### Start/List Commands
    php easy
    
### Call command
    php easy {command [parameters]}
    
### Configure for own projects
    See config/projects.php
    
### Add own commands
    Just see for examples in src/Command/
    Add new commands in ConfigProviders with key 'commands'
    
### Use database command 
    Add config in config/database.php 
    Rename database/{name}/
    See for migration table in database/{name}/structure.sql 
    
## Implementation as library
    See example in https://github.com/steltner/easy-template