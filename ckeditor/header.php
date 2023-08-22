<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="pragma" content="no-cache" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="google" content="notranslate">
<title><?php echo $gwTitle; ?></title>
<link rel="shortcut icon" href="https://gw.htenc.co.kr/images/HI_64x64.png">
<link rel="stylesheet" href="/gw/vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/gw/fontawesome-6.0.0-web/css/all.css">
<link rel="stylesheet" href="/gw/jquery/jquery-ui-1.13.0/jquery-ui.min.css" />
<link rel="stylesheet" href="/gw/vendor/snapappointments/bootstrap-select/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="/gw/css/style.css?random=<?php echo uniqid(); ?>" />
<script type="text/javascript" src="/gw/jquery/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="/gw/jquery/jquery-ui-1.13.0/jquery-ui.min.js"></script>
<script type="text/javascript" src="/gw/jquery/jquery-ui-1.13.0/i18n/datepicker-ko.js"></script>
<script type="text/javascript" src="/gw/vendor/snapappointments/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/gw/node_modules/popper.js/dist/umd/popper.min.js"></script>
<script type="text/javascript" src="/gw/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="/gw/js/grp.js?random=<?php echo uniqid(); ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<?php
if($isLogin) {
?>
<style>
#divFooter.active {
    width: 100%;
    margin: 0;
}

#modalLoading .modal-dialog {
    display: table;
    position: relative;
    margin: 0 auto;
    top: calc(50% - 1.5rem);
}

#modalLoading .modal-dialog .modal-content {
    background-color: transparent;
    border: none;
}

.fa-stack[data-count]:after {
    position:absolute;
    right:0%;
    top:1%;
    content: attr(data-count);
    font-size:30%;
    padding:.6em;
    border-radius:999px;
    line-height:.75em;
    color: white;
    background:rgba(255,0,0,.85);
    text-align:center;
    min-width:2em;
    font-weight:bold;
}
</style>

<script>
var iconList = {
    "nb0000000":"pen",
    "EA2100000":"clipboard",
    "EM200000": "envelope",
    "EM210000": "envelope",
    "EM250000":"comment-alt",
    "so0000000":"list",
    "kd0000000":"book-open",
    "mp0000000":"gear",
    "mp0000001":"phone",
    "bs0000000":"address-book",
    "sm0000000":"wrench",
};
$(document).ready(function() {
//     window.onpopstate = function(event) {
//         console.log("location: " + document.location + ", state: " + JSON.stringify(event.state));
//     };

    $(window).bind('popstate', function(event) {
//         var url = null;

//         if (event.state && event.state.url) {
//             url = event.state.url;
//         } 
//         else {
//             url = 'index.html'; // or whatever your initial url was
//         }

        $('.modal').modal('hide');
        $(".modal-backdrop").remove();

        var pathName = window.location.pathname;
        pathName = pathName.replace("/gw", "");
        pathName = pathName.slice(1, -1);
        var path = pathName.split('/');
        $("#topMenuCd").val(path[0]);
        showContent(path[1]);
	});

    //선택된 메뉴
    $("#topMenuCd").val("<?php
        echo $topMenuCd; 
    ?>");
    $("#subMenuCd").val("<?php
        echo $subMenuCd; 
    ?>");
    $("#parameters").val("<?php
        echo $parameters; 
    ?>");

    $.ajax({ 
        type: "GET", 
        url: "/gw/main_menu_data.php", 
        dataType: "json", 
        success: function(result) {
            var html = "";
            var htmlMobile = "";
            var lMenuId = "";
            var subMenuId = "";
            $.each(result["menuList"], function (i, obj) {
                html += '<li class="nav-item" id="menu_' + obj.lMenuId + '">';
                html += '<a';
//                 if (obj.menuCd == $("#menuCd").val()) {
//                     html += ' class="nav-link active" ';
//                 }
//                 else {
                    html += ' class="nav-link" ';
//                 }
                html += ' href="/gw/' + obj.menuCd + '/" onclick="return onMainMenuClick(\'' + obj.menuCd + '\', \'' + obj.lMenuId + '\', \'' + obj.subMenuId + '\')">' + obj.menuNm + '</a>';
                html += '</li>';
                if (obj.menuCd == $("#topMenuCd").val()) {
                    lMenuId = obj.lMenuId;
                    if ($("#subMenuCd").val() == "") {
                        subMenuId = obj.subMenuId;
                    }
                }
                htmlMobile += '<a class="dropdown-item" href="javascript:void(0);" onclick="onMainMenuClick(\'' + obj.menuCd + '\', \'' + obj.lMenuId + '\', \'' + obj.subMenuId + '\')">';
//                 htmlMobile += '<span class="fa-stack fa-3x has-badge" data-count="8">';
                htmlMobile += '<span class="fa-stack fa-2x has-badge">';
                htmlMobile += '<i class="fa fa-circle fa-stack-2x" style="color:#004377"></i>';
                htmlMobile += '<i class="fa fa-' + iconList[obj.menuCd] + ' fa-stack-1x fa-inverse" style="font-size:large"></i>';
                htmlMobile += '</span>';
                htmlMobile += '<br />';
                htmlMobile += '<div>';
                htmlMobile += obj.menuNm;
                htmlMobile += '</div>';
                htmlMobile += '</a>';
            });
            $("#mainMenu").empty().append(html);
            $("#mainMenuMobile").empty().append(htmlMobile);

            if ($("#topMenuCd").val() == "") {
                showContent("");
            }
            else {
                onMainMenuClick($("#topMenuCd").val(), lMenuId, subMenuId);
            }
        }
    });

    $.fn.extend({
        treed: function (o) {
            var openedClass = 'fa-minus-circle';
            var closedClass = 'fa-plus-circle';
            if (typeof o != 'undefined'){
                if (typeof o.openedClass != 'undefined'){
                    openedClass = o.openedClass;
                }
                if (typeof o.closedClass != 'undefined'){
                    closedClass = o.closedClass;
                }
            };

            //initialize each of the top levels
            var tree = $(this);
            tree.addClass("tree");
            tree.find('li').has("ul").each(function () {
                var branch = $(this); //li with children ul
                branch.prepend("<i class='indicator fas " + closedClass + "'></i>");
                branch.addClass('branch');
                branch.on('click', function (e) {
                    if (this == e.target) {
                        var icon = $(this).children('i:first');
                        icon.toggleClass(openedClass + " " + closedClass);
                        $(this).children().children().not("span").toggle();
                    }
                });
                branch.children().children().not("span").toggle();
            });
            //fire event from the dynamically added icon
            tree.find('.branch .indicator').each(function(){
                $(this).on('click', function () {
                    $(this).closest('li').click();
                });
            });
//             //fire event to open branch if the li contains an anchor instead of text
//             tree.find('.branch>a').each(function () {
//                 $(this).on('click', function (e) {
//                     $(this).closest('li').click();
//                     e.preventDefault();
//                 });
//             });
            //fire event to open branch if the li contains an anchor instead of text
            tree.find('a').each(function () {
                $(this).on('click', function (e) {
                    tree.find('a').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
            });
//             //fire event to open branch if the li contains a button instead of text
//             tree.find('.branch>button').each(function () {
//                 $(this).on('click', function (e) {
//                     $(this).closest('li').click();
//                     e.preventDefault();
//                 });
//             });
        },
        collapseAll: function (o) {
            var tree = $(this);
            var list = tree.find("i.fa-minus-circle");
            list.trigger('click');
        },
        expandAll: function (o) {
            var tree = $(this);
            var list = tree.find("i.fa-plus-circle");
            list.trigger('click');
        }
    });

    $(document).on('show.bs.modal', '.modal', function() {
        if($('.modal:visible').length == 0) {
            $("body").data('modal-zIndex', 1040);
        }
        const zIndex = $("body").data('modal-zIndex') + 10;
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            $("body").data('modal-zIndex', zIndex);
        }, 0);
    });
});

function showSubMenu() {
    $('#sidebar, #gwContent, #divFooter').toggleClass('active');
}

function onLogoClick() {
    history.pushState({}, '', "/gw/");

    onMainMenuClick("", "", "");
}

//메뉴 선택
function onMainMenuClick(menuCd, lMenuId, subMenuId) {
    if (checkClickedBefore("navHeader")) {
        return false;
    }

    $("#lMenuId").val(lMenuId);
    $("#topMenuCd").val(menuCd);

    $("#mainMenu").find("a").removeClass("active");
    if (lMenuId == "") {
        showContent("");
        checkClickedComplete("navHeader");
    }
    else {
        $("#menu_" + lMenuId).find("a").addClass("active");

        $("#subMenu").empty();
        $.ajax({ 
            type: "GET", 
            url: "/gw/sub_menu_data.php", 
            data: {topMenuCd: menuCd, lMenuId: lMenuId, subMenuId: subMenuId, subMenuCd: $("#subMenuCd").val()},
            dataType: "json", 
            success: function(result) {
                //세션 만료일 경우
                if (result["session_out"]) {
                    //로그인 화면으로 이동
                    onLogoutClick();
                }

                $("#subMenu").append(result["subMenuList"]);
                $("#subMenu > ul").treed();
                //회사표준
                if (lMenuId == "60") {
                    $("#subMenu ul").expandAll();
                }
                else {
                    $("#subMenu > ul:first-child > li").trigger('click');
                }

                var subMenuCd = result['subMenuCd'];
                if (subMenuCd == "") {
                    subMenuCd = $("#subMenuCd").val();
                }
                $("#subMenu_" + subMenuCd + " > a").trigger('click');
            },
            complete: function() {
                checkClickedComplete("navHeader");
            }
        });
    }

    return false;
}

function onSubmenuClick(subMenuCd) {
    history.pushState({}, '', "/gw/" + $("#topMenuCd").val() + "/" + subMenuCd + "/");

    showContent(subMenuCd);
}

function showContent(subMenuCd) {
    if (subMenuCd == "" || typeof subMenuCd === 'undefined') {
        $("#divSubMenuContent").hide();
    }
    else {
        $("#divSubMenuContent").show();
        if (checkClickedBefore("subMenu")) {
            return false;
        }
    }
    $("#gwContent").empty();
    $("#subMenuCd").val(subMenuCd);
    $.ajax({ 
        type: "GET", 
        url: "/gw/get_content_url.php", 
        data: {topMenuCd: $("#topMenuCd").val(), subMenuCd: subMenuCd},
        dataType: "json", 
        success: function(result) {
            //세션 만료일 경우
            if (result["session_out"]) {
                //로그인 화면으로 이동
                onLogoutClick();
            }

            var html = "";
            $.each(result["menuPath"], function (i, val) {
                html += '<li class="breadcrumb-item">';
                html += val;
                html += '</li>';
            });
            $(".breadcrumb li:not(.header-item-n)").remove();
            $(".breadcrumb").append(html);

            if (result["internal"]) {
                $("#gwContent").load(result["pageUrl"], result["params"]);
            }
            else {
                var height = $(document).height() - 150;
                html = '<iframe src="' + result["pageUrl"] + '" width="100%" height="' + height + '" style="border:none;">';
                html += '</iframe>';
                $("#gwContent").append(html);
            }

            const mediaQueryList = window.matchMedia("(max-width: 1199px)");
            if (mediaQueryList.matches) {
                $('#sidebar, #gwContent, #divFooter').removeClass('active');
            }
        },
        complete: function() {
            if (subMenuCd != "" || typeof subMenuCd !== 'undefined') {
                checkClickedComplete("subMenu");
            }
        }
    });
}

function checkClickedBefore(id) {
    var obj = $("#" + id);
    obj.find("a").css("pointer-events", "none");
    var alreadyClicked = obj.data('clicked');
    if (alreadyClicked) {
        return true;
    }
    obj.data('clicked', true);
    return false;
}

function checkClickedComplete(id) {
    setTimeout(function() {
        $("#" + id).data('clicked', false);
        $("#" + id).find("a").css("pointer-events", "auto");
    }, 800);
}

//개인정보수정
function onUserInfoClick() {
    location.href = "/gw/mp0000000/mp0100400/";
}

//로그아웃
function onLogoutClick() {
    $("#menuForm").attr({
        action:"/gw/cm/logout.php", 
        method:"post", 
        target:"_self"
    }).submit();
}

</script>
<?php
}
?>
</head>
<body>
<?php
if($isLogin){
?>
<nav id="navHeader" class="navbar-nav-main-menu navbar navbar-expand-sm navbar-dark fixed-top nav-color nav-main-link">
    <a class="navbar-brand" href="javascript:void(0);" onclick="onLogoClick()">
        <img src="/gw/images/ft_logo.png" alt="Logo" style="margin-top: -0.3rem;margin-left: 0.25rem;width: 8rem;" />
    </a>
    <div class="navbar-nav-w-menu">
    <ul id="mainMenu" class="navbar-nav">
    </ul>
    </div>

    <ul class="navbar-nav ml-auto navbar-nav-n">
        <li class="nav-item navbar-nav-n-menu">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" data-toggle="dropdown">
                <span class="fas fa-th fa-2x"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div id="mainMenuMobile" class="container-fluid">
                </div>
            </div>
        </li>
        <!-- Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" id="navbardrop" data-toggle="dropdown">
                <span class="fa-stack">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fas fa-user fa-stack-1x"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#" onclick="onUserInfoClick()"><?php echo $_SESSION["user"]["user_name"]?>(<?php echo $_SESSION["user"]["user_id"]?>)</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="onLogoutClick()">Logout</a>
            </div>
        </li>
    </ul>
</nav>
<div id="divSubMenuContent">
<form id="menuForm" name="menuForm">
<div id="sidebar" class="vertical-nav bg-light">
<div id="leftmenuinnerinner">
<div id="closeSidebar" class="clearfix">
    <button type="button" class="close btn-close mt-1 mr-2" onclick="showSubMenu()">&times;</button>
</div>
<!-- <nav id="subMenu" class="navbar-nav-sub-menu navbar bg-light navbar-light"> -->
<!--     <ul class="navbar-nav"> -->
<!--     </ul> -->
<!-- </nav> -->
<div id="subMenu" ></div>
</div>
</div>
<input type="hidden" id="topMenuCd" name="topMenuCd" />
<input type="hidden" id="lMenuId" name="lMenuId" />
<input type="hidden" id="subMenuCd" name="subMenuCd" />
<input type="hidden" id="parameters" name="parameters" />
</form>

<div id="divHeader"> 
<nav>
    <ol class="breadcrumb">
        <li class="header-item-n">
            <button type="button" class="btn" onclick="showSubMenu()">
                <span class="fas fa-bars"></span>
            </button>
        </li>
    </ol>
</nav>
</div>
</div>
<div id="gwContent" class="page-content p-2">
</div>
<?php
}
?>
