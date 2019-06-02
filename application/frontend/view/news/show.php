<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>新闻发布系统</title>
    <link rel="stylesheet" href="/public/front/css/style.css" />
</head>
<body>
<div class="box">
    <div class="top">
        <div class="title">新闻发布系统</div>
        <div class="nav">
            <a href="./index.php">返回列表</a>
        </div>
    </div>
    <div class="main">
        <!-- 新闻标题 -->
        <div class="news-title"><?=$news['title']?></div>
        <!-- 发布时间 -->
        <div class="news-time"><?=date('Y-m-d H:i',$news['create_time'])?></div>
        <!-- 新闻内容 -->
        <div class="news-content"><?=$news['content']?></div>
    </div>
    <div class="footer">
        页面底部
    </div>
</div>
</body>
</html>