<?php
error_reporting(E_ERROR);
require_once("../Mpdo.php");
require_once("../Pagination.php");
$conf = include_once("../config.php");
$mysql = new Mpdo();
$db = $mysql->connect($conf['database']);

if (isset($_GET['p']) && !empty(intval($_GET['p']))) {
    $page = intval($_GET['p']);
} else {
    $page = 1;
}
if ($page < 1) $page = 1;

$where = 'WHERE 1=1';
$query_str = [];

if(isset($_GET['c']) && !empty(intval($_GET['c']))){
    $category = intval($_GET['c']);
    $where .= " AND `category`={$category} ";
    $query_str['c'] = $category;
}

if(isset($_GET['n']) && !empty(trim($_GET['n']))){
    $goods_no = trim($_GET['n']);
    $where .= " AND `goods_no`='{$goods_no}' ";
    $query_str['n'] = $goods_no;
}

if(isset($_GET['s']) && !empty($_GET['s'])){
    $size = (float) $_GET['s'];
    $where .= " AND `size`={$size} ";
    $query_str['s'] = $size;
}

if(isset($_GET['col']) && !empty(trim($_GET['col']))){
    $color = trim($_GET['col']);
    $where .= " AND `color` like '{$color}%' ";
    $query_str['col'] = $color;
}

$pagesize = 2;

$res = $db->count("SELECT count(`id`) AS `total` FROM `goods` " . $where);
$pagetotal = ceil($res['total'] / $pagesize);

if ($pagetotal > 0) {
    if ($page > $pagetotal) $page = $pagetotal;
}

$offset = ($page - 1) * $pagesize;

$page_link = [];
if (1 < $pagetotal) {

    $pagination = new Pagination();

    $pagination->config([
        'base_url' => 'goods.php',
        'pagetotal' => $pagetotal,
        'cur_page' => $page,
        'query_str' => $query_str,
        'show_link_nums' => 7
    ]);

    $page_link = $pagination->create_links('array');
}

$sql = "SELECT * FROM `goods` ".$where." ORDER BY `id` DESC LIMIT {$offset},{$pagesize}";
$data = $db->query($sql)->row_all();
$category = get_category($db);
$goods = [];
$i = 0;
foreach($data as $k => $v){
    $goods[$i]['id'] = $v['id'];
    $goods[$i]['goods_no'] = $v['goods_no'];
    $goods[$i]['price'] = $v['price'];
    $goods[$i]['sell'] = $v['sell'];
    foreach ($category as $m => $n){
        if($n['id'] == $v['category']){
            $goods[$i]['category'] = $n['name'];
            break;
        }else{
            foreach ($n['children'] as $h => $j){
                if($j['id'] == $v['category']){
                    $goods[$i]['category'] = $n['name'].'/'.$j['name'];
                }
            }
        }
    }
    $goods[$i]['size'] = $v['size'];
    $goods[$i]['color'] = $v['color'];
    $goods[$i]['store'] = $v['store'];
    $goods[$i]['sold'] = $v['sold'];
    $sql = "SELECT `name` FROM `firm` WHERE `id`={$v['firm']}";
    $firm = $db->query($sql)->row_one();
    $goods[$i]['firm'] = $firm['name'];
    $i++;
}

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
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="icon" type="image/png" href="../assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
    <link rel="stylesheet" href="../assets/css/amazeui.min.css"/>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/app.css">
    <style>
        optgroup{
            margin-top: 8px;
            font-size: 1.3rem;
            color: #999;
            border-bottom: 1px solid #e5e5e5;
            cursor: default;
        }
        option{
            display: block;
            word-wrap: normal;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            margin-right: 30px;
        }
    </style>
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
            进货商信息列表
        </div>
        <div class="tpl-portlet-components">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    <span class="am-icon-code"></span> 列表
                </div>
            </div>
            <div class="tpl-block">
                <div class="am-g">
                    <div class="am-u-sm-12 am-u-md-3">
                        <div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <a href="operation.php" class="am-btn am-btn-default am-btn-success"><span
                                            class="am-icon-plus"></span> 新增
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-2">
                        <div class="am-form-group">
                            <select id="search-category" name="category">
                                <option value="">所有类别</option>
                                <?php foreach ($category as $k => $v):?>
                                    <?php if(isset($v['children']) && !empty($v['children'])):?>
                                        <optgroup label="<?=$v['name']?>">
                                            <?php foreach ($v['children'] as $m => $n):?>
                                            <option value="<?=$n['id']?>" <?php if($n['id'] == intval($_GET['c'])):?>selected<?php endif;?>><?=$n['name']?></option>
                                            <?php endforeach;?>
                                        </optgroup>
                                    <?php else:?>
                                        <option value="<?=$v['id']?>" <?php if($v['id'] == intval($_GET['c'])):?>selected<?php endif;?>><?=$v['name']?></option>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-2">
                        <div class="am-input-group am-input-group-sm">
                            <input id="search-goods-no" style="width:120px;" name="goods_no" value="<?= trim($_GET['n']) ?>" type="text"
                                   class="am-form-field" placeholder="      货品编号">
                        </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-2">
                        <div class="am-input-group am-input-group-sm">
                            <input id="search-size" style="width:80px;" name="size" value="<?= trim($_GET['s']) ?>" type="text"
                                   class="am-form-field" placeholder="      尺码">
                        </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-3">
                        <div class="am-input-group am-input-group-sm">
                            <input id="search-color" style="width:80px;" name="color" value="<?= trim($_GET['col']) ?>" type="text"
                                   class="am-form-field" placeholder="      颜色">
                            <span class="am-input-group-btn">
                                <span class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search">查询</span>
                              </span>
                        </div>
                    </div>
                </div>
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                    <tr>
                                        <th class="table-check"><input type="checkbox" class="tpl-table-fz-check"></th>
                                        <th class="table-id">ID</th>
                                        <th class="table-title">货号</th>
                                        <th class="table-type">进价</th>
                                        <th class="table-author">售价</th>
                                        <th class="table-date">分类</th>
                                        <th class="table-date">尺码</th>
                                        <th class="table-date">颜色</th>
                                        <th class="table-date">库存</th>
                                        <th class="table-date">销量</th>
                                        <th class="table-date">进货商</th>
                                        <th class="table-set">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($goods as $k => $v): ?>
                                        <tr class="edit-goal-tr" uid="<?=$v['id']?>">
                                            <td><input type="checkbox"></td>
                                            <td><?= $v['id'] ?></td>
                                            <td><?= $v['goods_no'] ?></td>
                                            <td><?= $v['price'] ?></td>
                                            <td><?= $v['sell'] ?></td>
                                            <td><?= $v['category'] ?></td>
                                            <td><?= $v['size'] ?></td>
                                            <td><?= $v['color'] ?></td>
                                            <td><?= $v['store'] ?></td>
                                            <td><?= $v['sold'] ?></td>
                                            <td><?= $v['firm'] ?></td>
                                            <td class="operation">
                                                <div class="am-btn-toolbar edit-delete-btn">
                                                    <div class="am-btn-group am-btn-group-xs">
                                                        <a href="operation.php?id=<?= $v['id'] ?>" class="am-btn am-btn-default am-btn-xs am-text-secondary">
                                                            <span class="am-icon-pencil-square-o"></span> 编辑
                                                        </a>
                                                        <a uid="<?= $v['id'] ?>" class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only delete-user">
                                                            <span class="am-icon-trash-o"></span> 删除
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (!empty($page_link)): ?>
                                    <div class="am-cf">
                                        <div class="am-fr">
                                            <ul class="am-pagination tpl-pagination">
                                                <?php if (isset($page_link['first_page'])): ?>
                                                    <li>
                                                        <a href="<?= $page_link['base_url'] ?>?<?= $page_link['first_page'] ?>">«</a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php foreach ($page_link['loop_page'] as $k => $v): ?>
                                                    <li<?php if ($k == $page_link['cur_page']): ?> class="am-active"<?php endif; ?>><?php if ($k != $page_link['cur_page']): ?>
                                                            <a
                                                            href="<?= $page_link['base_url'] ?>?<?= $v ?>"><?= $k ?></a><?php else: ?>
                                                            <span style="border-radius: 3px;
    padding: 6px 12px;"><?= $k ?></span><?php endif; ?></li>
                                                <?php endforeach; ?>
                                                <?php if (isset($page_link['last_page'])): ?>
                                                    <li>
                                                        <a href="<?= $page_link['base_url'] ?>?<?= $page_link['last_page'] ?>">»</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <hr>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/amazeui.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>
<script type="application/javascript">
    $(".delete-user").click(function () {
        if (confirm('你确定要删除吗？')) {
            var id = $(this).attr('uid');
            $.ajax({
                type: "POST",
                url: "delete.php",
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    if (data.status == 2000) {
                        alert(data.msg);
                        window.location = "goods.php";
                    } else {
                        alert(data.msg);
                    }
                }
            });
        }
    });

    $(".am-input-group-btn").click(function () {
        var cate = $("#search-category").val();
        var goods_no = $("#search-goods-no").val();
        var size = $("#search-size").val();
        var color = $("#search-color").val();

        if(cate == '' && goods_no == '' && size == '' && color == ''){
            window.location = 'goods.php';
            return false;
        }

        var query_str = [];
        if(cate != ''){
            query_str['c'] = cate
        }

        if(goods_no != ''){
            query_str['n'] = goods_no
        }

        if(size != ''){
            query_str['s'] = size
        }

        if(color != ''){
            query_str['col'] = color
        }
        var query = build_query(query_str,query_str.length);
        window.location = "goods.php?"+query;
    });

    var build_query = function (obj, num_prefix, temp_key) {

        var output_string = []

        Object.keys(obj).forEach(function (val) {

            var key = val;

            num_prefix && !isNaN(key) ? key = num_prefix + key : ''

            var key = encodeURIComponent(key.replace(/[!'()*]/g, escape));
            temp_key ? key = temp_key + '[' + key + ']' : ''

            if (typeof obj[val] === 'object') {
                var query = build_query(obj[val], null, key)
                output_string.push(query)
            }

            else {
                var value = encodeURIComponent(obj[val].replace(/[!'()*]/g, escape));
                output_string.push(key + '=' + value)
            }

        })

        return output_string.join('&')
    }
</script>
</html>
