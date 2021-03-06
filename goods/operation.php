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
$mysql = new Mpdo();
$db = $mysql->connect($conf['database']);
if(!empty($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'){

    $data['goods_no'] = (isset($_POST['goods_no']) && !empty($_POST['goods_no'])) ? trim($_POST['goods_no']) : '' ;
    $data['price'] = (isset($_POST['price']) && !empty($_POST['price'])) ? (float) $_POST['price'] : 0;
    $data['sell'] = (isset($_POST['sell']) && !empty($_POST['sell'])) ? (float) $_POST['sell'] : 0;
    $data['category'] = (isset($_POST['category']) && !empty($_POST['category'])) ? intval($_POST['category']) : 0 ;
    $data['size'] = (isset($_POST['size']) && !empty($_POST['size'])) ? (float) $_POST['size'] : 0 ;
    $data['color'] = (isset($_POST['color']) && !empty($_POST['color'])) ? trim($_POST['color']) : 0 ;
    $data['store'] = (isset($_POST['store']) && !empty($_POST['store'])) ? intval($_POST['store']) : 0 ;
    $data['sold'] = (isset($_POST['sold']) && !empty($_POST['sold'])) ? intval($_POST['sold']) : 0 ;
    $data['firm'] = (isset($_POST['firm']) && !empty($_POST['firm'])) ? intval($_POST['firm']) : 0 ;

    if(isset($_POST['id']) && !empty(intval($_POST['id']))){
        $id = intval($_POST['id']);
        $sql = "UPDATE `goods` SET `goods_no`=?,`price`=?,`sell`=?,`category`=?,`size`=?,`color`=?,`store`=?,`sold`=?,`firm`=? WHERE `id`={$id}";
        $res = $db->update($sql,array_values($data));
        if($res){
            echo '<script>alert("修改货品信息成功！");window.location = "goods.php";</script>';
            exit();
        }else{
            echo '<script>alert("修改货品信息失败！");window.history.go(-1);</script>';
            exit();
        }
    }else{
        $sql = "INSERT INTO `goods` (`goods_no`,`price`,`sell`,`category`,`size`,`color`,`store`,`sold`,`firm`) VALUES (?,?,?,?,?,?,?,?,?)";
        $res = $db->insert($sql,array_values($data));
        if($res){
            echo '<script>alert("添加货品信息成功！");window.location = "goods.php";</script>';
            exit();
        }else{
            echo '<script>alert("添加货品信息失败！");window.history.go(-1);</script>';
            exit();
        }
    }

}

if(!empty(intval($_GET['id'])) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM `goods` WHERE `id`={$id}";
    $goodsInfo = $db->query($sql)->row_one();
}

$sql = "SELECT * FROM `firm`";
$firm = $db->query($sql)->row_all();
$category = get_category($db);
function get_category($db)
{
    $sql = "SELECT * FROM `category` WHERE `pid`=0";
    $data = $db->query($sql)->row_all();
    $category = [];
    $i = 0;
    foreach ($data as $k => $v){
        $category[$i]['id'] = $v['id'];
        $category[$i]['name'] = $v['name'];
        $sql = "SELECT * FROM `category` WHERE `pid`={$v['id']}";
        $children = $db->query($sql)->row_all();
        foreach ($children as $m => $n){
            $category[$i]['children'][] = $n;
        }
        $i++;
    }
    return $category;
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
                    <a href="../index.php" class="nav-link">
                        <i class="am-icon-home"></i>
                        <span>会员信息</span>
                    </a>
                </li>
                <li class="tpl-left-nav-item">
                    <a href="../goods/goods.php" class="nav-link tpl-left-nav-link-list active">
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
                    <a href="../firm/firm.php" class="nav-link tpl-left-nav-link-list">
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
                    echo '修改货品信息';
                }else{
                    echo '添加货品信息';
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
                                <label for="user-name" class="am-u-sm-3 am-form-label"> 货号 <span class="tpl-form-line-small-title">GoodsNo</span></label>
                                <div class="am-u-sm-9">
                                    <input type="text" name="goods_no" value="<?=$goodsInfo['goods_no']?>" class="tpl-form-input" id="user-name" placeholder="请输入货品编号" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label"> 进价 <span class="tpl-form-line-small-title"> Price </span></label>
                                <div class="am-u-sm-9">
                                    <input type="number" step="0.01" name="price" value="<?=$goodsInfo['price']?>" placeholder="请输入进价" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label"> 售价 <span class="tpl-form-line-small-title">Sell</span></label>
                                <div class="am-u-sm-9">
                                    <input type="number" step="0.01" name="sell" value="<?=$goodsInfo['sell']?>" id="user-weibo" placeholder="请输入售价" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label"> 所属分类 <span class="tpl-form-line-small-title"> Belong </span></label>
                                <div class="am-u-sm-9">
                                    <select name="category">
                                        <?php foreach ($category as $k => $v):?>
                                            <?php if(isset($v['children']) && !empty($v['children'])):?>
                                                <optgroup label="<?=$v['name']?>">
                                                    <?php foreach ($v['children'] as $m => $n):?>
                                                        <option value="<?=$n['id']?>" <?php if($n['id'] == $goodsInfo['category']):?>selected<?php endif;?>><?=$n['name']?></option>
                                                    <?php endforeach;?>
                                                </optgroup>
                                            <?php else:?>
                                                <option value="<?=$v['id']?>" <?php if($v['id'] == $goodsInfo['category']):?>selected<?php endif;?>><?=$v['name']?></option>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label"> 尺码 <span class="tpl-form-line-small-title">Size</span></label>
                                <div class="am-u-sm-9">
                                    <input type="number" step="0.5" name="size" value="<?=$goodsInfo['size']?>" id="user-weibo" placeholder="请输入尺码" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label"> 颜色 <span class="tpl-form-line-small-title">Color</span></label>
                                <div class="am-u-sm-9">
                                    <input type="text" name="color" value="<?=$goodsInfo['color']?>" id="user-weibo" placeholder="请输入颜色" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label"> 库存 <span class="tpl-form-line-small-title">Store</span></label>
                                <div class="am-u-sm-9">
                                    <input type="number" name="store" value="<?=$goodsInfo['store']?>" id="user-weibo" placeholder="请输入库存" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label"> 销量 <span class="tpl-form-line-small-title">Sold</span></label>
                                <div class="am-u-sm-9">
                                    <input type="number" name="sold" value="<?=$goodsInfo['sold']?>" id="user-weibo" placeholder="请输入销量" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label"> 进货商 <span class="tpl-form-line-small-title"> Firm</span></label>
                                <div class="am-u-sm-9">
                                    <select name="firm">
                                        <?php foreach ($firm as $k => $v):?>
                                            <option value="<?=$v['id']?>" <?php if($v['id'] == $goodsInfo['firm']):?>selected<?php endif;?>>- <?=$v['name']?> -</option>
                                        <?php endforeach;?>
                                    </select>
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
