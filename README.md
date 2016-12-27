# gc.status

基于Laravel开发

## 部署注意事项

* 计划任务部署

```
* * * * * php /data/wali/gc.status/artisan schedule:run >> /dev/null 2>&1
```

* 队列任务部署（可用supervisor来管理守护进程）

```
nohup /path/to/php/bin/php /path/to/artisan queue:work --queue=makerequest &
```
