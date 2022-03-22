# laravelapp
docker 部署的laravel8基础项目，由supervisord管理php-fpm、nginx

#### 直接运行
```
git clone https://github.com/zl-php/laravelapp.git

docker-compose up -d

```

#### 上传至 `hub.docker.com`仓库
```
# 打包镜像
docker build -t 你的仓库用户名/laravelapp:8 . 

# 登录
docker login

# 上传刚才打包的镜像
docker push 你的仓库用户名/laravelapp:8

# 通过docker-compose拉取镜像
docker-compose -f docker-compose.yml pull

# 启动
docker-compose up -d

#重启
docker-compose restart

```

#### 访问

127.0.0.1
