<?php
	include "conn.php";

	if(isset($_SESSION["kakao_member_code"]) == false || !$_SESSION["kakao_member_code"]) { //로그인하지 않았을 때
		?>
			<script>
				location.replace("login.php");
			</script>
		<?php
		exit;
	}
  // $_SESSION["kakao_member_code"] = $ddlMemberList;
	// $_SESSION["kakao_member_alias"] = $alias;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
	<title>카카오톡</title>
	<style>
		.divFriendTr {
			height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:middle;
			padding-top: 6px;
			padding-bottom: 6px;
			padding-left: 14px;
			margin: 0px;
			width:calc(100% - 14px);
			clear:both;
		}

		.divChatTr{
			min-height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:middle;
			padding-top:6px;
			padding-bottom:6px;
			padding-right:30px;
			margin:0px;
			width:calc(100% - 30%);
			float:right;
			clear:both;
		}
		
		.divChatTrMy{
			min-height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:6px;
			padding-top:6px;
			padding-bottom:6px;
			padding-right:30px;
			margin:0px;
			width:calc(100% - 30px);
			float:right;
			clear:both;
			font-size:13px;
		}
	</style>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.1/mustache.min.js"></script>
	<script>
		let websocket = null; //웹소켓을 통해 서버와 연결

		$(document).ready(function(){
			connect();
			loadMemberList();
		});

		function connet() {
			websocket = new WebSocket("ws://172.30.1.254:8008");
			websocket.onopen = function(e){
				let data = {"code": "member_login", "memberCode": "<?php echo $_SESSION["kakao_member_code"]?>","memberAlias": "<?php echo $_SESSION["kakao_member_alias"]?>"}
				sendMessage(data);
			}
		}

		function sendMessage(msg) { //메시지 전송 역할
			websocket.send(JSON.stringify(msg));
		}

		function loadMemberList() {
			//1. 데이터베이스의 회원 정보를 읽어 json 객체 형태로 받는 것
			$.ajax({
				type: "post",
				url: "getMemberList.php",
				data: {},
				dataType: 'text',
				cache: false,
				async: false
			})
			.done(function( result ){
				//성공했을때
				let memberList = {"MEMBER": JSON.parse(result)};

			//2. 받은 내용을 무스타크로 출력하는 것
				let output = Mustache.render($("#divMemberList").html(), memberList);
				$("#divMemberList").html(output);
			})
			.fail(function( result, status, error ){
				//실패했을때
				alert("에러발생" + error);
			});



		}

		function openChat(){
			$('#MAIN').css("left", ($(document).width() + 100));
			$('#MAIN').load("chat.php", function() {
				$('#MAIN').animate({left:0, top:0});
			});
		}
	</script>
</head>
<body style="margin:0px">
	<div style="width:100%; display:inline-block; height:630px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="MAIN">
		<div style="width:20%; display:inline-block; height:100%; background-color:#ececed; padding:0px; padding-top:10px; margin:0px; text-align:center; float:left">
			<i class="fas fa-user" style="font-size:28px; color:#909297"></i>
		</div>
		<div style="width:76% display:line-block; height:100%; background-color:#ffffff; padding:0px margin:0px; padding-top:10px; float:left">
			<div style="width:100%; height:30px; padding:0px; margin:0px; color:black; padding-left:14px">
				친구
			</div>
			<div style="width:100%; height:calc(100% - 30px); padding:0px; margin:0px; margin-bottom:-30px; color:black; overflow-y:auto" id="divMemberList">
			{{#MEMBER}}
				{{#alias}}
						<div class="divFriendTr">
					<div style="float:left">
						<img src="{{usrIcon}}" style="width:33px; height:33px">
					</div>
					<div style="float:left; margin-left:7px" onclick="openChat();">
						{{alias}}
					</div>
				</div>
				{{/alias}}
			{{/MEMBER}}
			</div>
		</div>
	</div>
	<div style="width:0%; height:0px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="BACKGROUND">

	</div>
</body>
	</html>