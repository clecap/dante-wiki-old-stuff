
## Operation

### Core Concept

## Best Practice Operations

### Docker-Side Operations

| Task     | Command | Comment |
|----      | -----   | ------|
| Run   | `./run.sh WIKI-NAME`  | If WIKI-NAME is running, it will be stopped; if it is not initialized, it will be initialized from scratch.  |
| Usage     | `./all.sh`  | Displays names of configured Dantewikis; right-click on URL opens in browser  |
| Backup  | `./dbDump.sh WIKI-NAME` |  Generates a database dump file in directory dumps with identifying name. |
| Restore | `/.dbRestore.sh WIKI-NAME  SQL-FILE-NAME` | 
| Stop    | `./stop.sh WIKI-NAME` |       |
| Show    | `docker ps`  | Show all running docker containers  |


### Dante-Side Operations

