## 功能
1. aliyun OSS 更改后支持获取image类型文件信息
2. auth 从 laravel 抽取出的组件
3. resource 从 laravel 抽取出的组件
4. hash 从 laravel 抽取出的组件
5. elasticsearch 二次封装
6. paginator 修改 配合 5 一起工作不需要自己处理分页数据
7. 请求全局的异常处理封装,响应封装

### Response
当逻辑正常走完后应该使用 `Response -> success()` 返回数据

### 异常处理
在请求中任何错误/参数不齐全等检查后可以直接通过抛出`BusinessException`来返回内容

example:
```php
if(auth('user')->guest()){
    throw new BusinessException(401,'未登录');
}
```

### 模型
配置数据后可以通过 `php bin/hyperf.php gen:model` 从数据库中表生成对应模型,模型需要软删除请继承类 `ModelSoftDelete`

## docker
注意主机网络模式

```shell script
docker build -t api:develop .

// 启动
docker run -dit --name apidev -v $PWD/:/opt/www api:develop

// 进入命令行 使用 Ctrl + Q 退出 P 暂停 D 删除 R 容器
docker attach apidev

// 重启 , 每次改完代码需要的步骤
docker restart apidev

// 停止
docker stop apidev

// 停止所有容器
docker stop $(docker ps -a -q)
// 删除所有容器
docker rm $(docker ps -a -q)


composer run docker:devlop:init
composer run docker:devlop:start
composer run docker:devlop:restart
composer run docker:devlop:stop
composer run docker:devlop:status
```