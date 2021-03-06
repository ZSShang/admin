<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/8
 * Time: 9:56
 */
error_reporting(E_ERROR);
require_once("../Mpdo.php");
$conf = include_once("../config.php");
if(!empty($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    if(empty($_POST['phone']) || !is_numeric(trim($_POST['phone']))){
        echo '<script>alert("请输入正确的手机号！");window.history.go(-1);</script>';
        exit();
    }

    $mysql = new Mpdo();
    $db = $mysql->connect($conf['database']);
    $data['name'] = (isset($_POST['name']) && !empty($_POST['name'])) ? trim($_POST['name']) : '' ;
    $data['phone'] = (isset($_POST['phone']) && !empty($_POST['phone'])) ? trim($_POST['phone']) : '' ;
    $data['address'] = (isset($_POST['address']) && !empty($_POST['address'])) ? trim($_POST['address']) : '' ;
    $data['username'] = (isset($_POST['username']) && !empty($_POST['username'])) ? trim($_POST['username']) : '' ;

    if(isset($_POST['id']) && !empty(intval($_POST['id']))){
        $id = intval($_POST['id']);
        $sql = "UPDATE `firm` SET `name`=?,`phone`=?,`address`=?,`username`=? WHERE `id`={$id}";
        $res = $db->update($sql,array_values($data));
        if($res){
            echo '<script>alert("修改进货商信息成功！");window.location = "firm.php";</script>';
            exit();
        }else{
            echo '<script>alert("修改进货商信息失败！");window.history.go(-1);</script>';
            exit();
        }
    }else{
        $sql = "SELECT `id` FROM `firm` WHERE `phone`='{$data['phone']}'";
        $res = $db->query($sql)->row_one();
        if(!empty($res)){
            echo '<script>alert("手机号已被注册！");window.history.go(-1);</script>';
            exit();
        }
        $sql = "INSERT INTO `firm` (`name`,`phone`,`address`,`username`) VALUES (?,?,?,?)";
        $res = $db->insert($sql,array_values($data));
        if($res){
            echo '<script>alert("添加进货商信息成功！");window.location = "firm.php";</script>';
            exit();
        }else{
            echo '<script>alert("添加进货商信息失败！");window.history.go(-1);</script>';
            exit();
        }
    }

}

if(!empty(intval($_GET['id'])) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $mysql = new Mpdo();
    $db = $mysql->connect($conf['database']);
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM `firm` WHERE `id`={$id}";
    $userInfo = $db->query($sql)->row_one();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Amaze UI Admin index Examples</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="../assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <link rel="stylesheet" href="../assets/css/amazeui.min.css" />
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body data-type="generalComponents">
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-left-nav tpl-left-nav-hover">
        <div class="tpl-left-nav-list">
            <ul class="tpl-left-nav-menu">
                <li class="tpl-left-nav-item">
                    <a href="../index.php" class="nav-link ">
                        <i class="am-icon-home"></i>
                        <span>会员信息</span>
                    </a>
                </li>
                <li class="tpl-left-nav-item">
                    <a href="../goods/goods.php" class="nav-link tpl-left-nav-link-list ">
                        <i class="am-icon-bar-chart"></i>
                        <span>货品信息</span>
                    </a>
                </li>

                <li class="tpl-left-nav-item">
                    <!-- 打开状态 a 标签添加 active 即可   -->
                    <a href="../category/category.php" class="nav-link tpl-left-nav-link-list">
                        <i class="am-icon-table"></i>
                        <span>货品分类</span>
                        <!-- 列表打开状态的i标签添加 tpl-left-nav-more-ico-rotate 图表即90°旋转  -->
                    </a>
                </li>

                <li class="tpl-left-nav-item">
                    <a href="../firm/firm.php" class="nav-link tpl-left-nav-link-list active">
                        <i class="am-icon-wpforms"></i>
                        <span>进货商信息</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            <?php
                if(isset($_GET['id']) && !empty(intval($_GET['id']))){
                    echo '修改进货商信息';
                }else{
                    echo '添加进货商信息';
                }
            ?>
        </div>
        <div class="tpl-portlet-components">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    <span class="am-icon-code"></span> 表单
                </div>
            </div>
            <div class="tpl-block">

                <div class="am-g">
                    <div class="tpl-form-body tpl-form-line">
                        <form action="operation.php" method="post" class="am-form tpl-form-line-form">
                            <input type="hidden" name="id" value="<?=intval($_GET['id'])?>">
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label"> 联系人姓名 <span class="tpl-form-line-small-title">Username</span></label>
                                <div class="am-u-sm-9">
                                    <input type="text" name="username" value="<?=$userInfo['username']?>" class="tpl-form-input" id="user-name" placeholder="请输入用户姓名">
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">手机号 <span class="tpl-form-line-small-title">Phone</span></label>
                                <div class="am-u-sm-9">
                                    <input type="text" name="phone" value="<?=$userInfo['phone']?>" placeholder="输入手机号" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">进货商名称<span class="tpl-form-line-small-title">Name</span></label>
                                <div class="am-u-sm-9">
                                    <input type="text" name="name" value="<?=$userInfo['name']?>" id="user-weibo" placeholder="请添加进货商名称">
                                    <div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">地址 <span class="tpl-form-line-small-title">Address</span></label>
                                <div class="am-u-sm-9">
                                    <input type="text" name="address" value="<?=$userInfo['address']?>" id="user-weibo" placeholder="请添加积分地址">
                                    <div>

                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <input type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success " name="提交">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/amazeui.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>

</html>
